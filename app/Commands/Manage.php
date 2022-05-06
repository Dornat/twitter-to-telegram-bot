<?php

namespace App\Commands;

use App\Exceptions\ArgumentValidationException;
use App\Helpers\IdsMap;
use App\Helpers\IdsValidator;
use App\Helpers\PipelinesMap;
use App\Services\TwitterAPIv1CredentialsService;
use App\Services\TelegramSendTypeService;
use App\ValueObjects\DataHub;
use App\ValueObjects\TelegramPayload;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use RWC\TwitterStream\Sets;
use RWC\TwitterStream\Fieldset;
use Spatie\TwitterStreamingApi\PublicStream;

class Manage extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature =
        'manage
        {ids* : Twitter Channel Id to Telegram Channel Id pair in the form "twitterId:telegramId", multiple pairs can be provided. Example: "414630776:@testchannel".}
        {--telegram-access-token= : Telegram bot access token.}
        {--twitter-bearer-token= : Twitter api bearer token.}
        {--twitter-api-key= : Twitter api key.}
        {--twitter-api-secret= : Twitter api secret.}
        {--channel-pipeline=* : A possibility to use different pipelines for each provided twitter channel id in the form "twitterId:pipelineName". Example: "414630776:default".}
        ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Simple bot that can link any twitter account feed to telegram channel.';

    /**
     * Execute the console command.
     */
    public function handle(IdsValidator $idsValidator, TelegramSendTypeService $telegramSendTypeService, TwitterAPIv1CredentialsService $v1Credentials): void
    {
        try {
            $idsArg = $this->argument('ids');
            $idsValidator->validate($idsArg);
        } catch (ArgumentValidationException) {
            $this->error('ids argument should be in the form "twitterId:telegramId".');
            return;
        }

        try {
            $channelPipes = $this->option('channel-pipeline');
            $idsValidator->validate($channelPipes);
        } catch (ArgumentValidationException) {
            $this->error('ids argument should be in the form "twitterId:pipelineName".');
            return;
        }

        $idsMap = new IdsMap(array_map(fn($value): array => explode(':', $value), $idsArg));
        $pipelinesMap = empty($channelPipes) ? null : new PipelinesMap(array_map(fn($value): array => explode(':', $value), $channelPipes));

        $twitterBearerToken = $this->option('twitter-bearer-token') ?: env('TWITTER_BEARER_TOKEN');
        $twitterApiKey = $this->option('twitter-api-key') ?: env('TWITTER_API_KEY');
        $twitterApiSecret = $this->option('twitter-api-secret') ?: env('TWITTER_API_KEY_SECRET');
        $telegramApiToken = $this->option('telegram-access-token') ?: env('TELEGRAM_ACCESS_TOKEN');

        $v1Credentials->create($twitterApiKey, $twitterApiSecret);

        PublicStream::create(
            $twitterBearerToken,
            $twitterApiKey,
            $twitterApiSecret
        )->whenTweets($idsMap->array_keys(), function (array $tweet) use (
            $idsMap, $pipelinesMap, $telegramApiToken, $telegramSendTypeService
        ) {
            echo json_encode($tweet, JSON_PRETTY_PRINT);
            $pipelines = config('pipelines');
            $currentTwitterId = $tweet['data']['author_id'];

            foreach ($idsMap[$currentTwitterId] as $telegramChatId) {
                if (!is_null($pipelinesMap)) {
                    /** @var DataHub $dataHub */
                    $dataHub = (new $pipelines[$pipelinesMap[$currentTwitterId]]())->create()
                        ->run(new DataHub($tweet, new TelegramPayload($telegramChatId)));
                } else {
                    /** @var DataHub $dataHub */
                    $dataHub = (new $pipelines['default']())->create()
                        ->run(new DataHub($tweet, new TelegramPayload($telegramChatId)));
                }

                $sendType = $telegramSendTypeService->generate($dataHub)->value;

                file_get_contents(
                    "https://api.telegram.org/bot$telegramApiToken/$sendType?"
                    . http_build_query($dataHub->getTelegramPayload())
                );
            }
        })->startListening(
            // Add all the necessary fields to response.
            new Sets(
                new Fieldset('expansions', 'author_id', 'referenced_tweets.id', 'attachments.media_keys'),
                new Fieldset('tweet.fields', 'attachments', 'author_id', 'context_annotations', 'conversation_id', 'created_at', 'entities', 'id', 'in_reply_to_user_id', 'referenced_tweets', 'source', 'text'),
                new Fieldset('media.fields', 'duration_ms', 'height', 'media_key', 'preview_image_url', 'type', 'url', 'width', 'public_metrics', 'non_public_metrics', 'organic_metrics', 'alt_text'),
                new Fieldset('user.fields', 'id')
            )
        );
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
