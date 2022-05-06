<?php

namespace App\Pipes;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Services\TwitterAPIv1CredentialsService;
use App\ValueObjects\DataHub;
use App\ValueObjects\TwitterMediaType;

/**
 * According to Twitter API v2.0 documentation media object should have a 'url' key in it
 * when there is an animated_gif object present but for some reason it doesn't work,
 * maybe it's because the API page states that "Note that video URLs are not currently available, only static images."
 * and that's why we don't have the url for animated_gif's as well as for videos.
 * Very weird.
 */
class AnimatedGifPipe extends PipeAbstract
{
    public function handle(DataHub $dataHub): mixed
    {
        $media = $dataHub->getTweet()['includes']['media'] ?? null;
        if (is_null($media)) {
            return parent::handle($dataHub);
        }
        $data = $dataHub->getTweet()['data'];
        $telegramPayload = $dataHub->getTelegramPayload();

        // @TODO Handle properly when the Twitter API v2 will be fixed.
        if (!empty($media[0]['type']) && $media[0]['type'] === TwitterMediaType::ANIMATED_GIF->value) {
            /** @var TwitterAPIv1CredentialsService $v1Credentials */
            $v1Credentials = app(TwitterAPIv1CredentialsService::class);
            $connection = new TwitterOAuth($v1Credentials->getTwitterApiKey(), $v1Credentials->getTwitterApiSecret());
            $content = $connection->get('statuses/show', ['id' => $data['id']]);

            $telegramPayload->animation = $content->extended_entities->media[0]->video_info->variants[0]->url;

            if ($telegramPayload->text) {
                $telegramPayload->caption = $telegramPayload->text;
                $telegramPayload->text = null;
            }
        }

        return parent::handle($dataHub);
    }
}
