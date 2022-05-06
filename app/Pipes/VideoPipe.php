<?php

namespace App\Pipes;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Services\TwitterAPIv1CredentialsService;
use App\ValueObjects\DataHub;
use App\ValueObjects\TwitterMediaType;

/**
 * @see AnimatedGifPipe description.
 */
class VideoPipe extends PipeAbstract
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
        if (!empty($media[0]['type']) && $media[0]['type'] === TwitterMediaType::VIDEO->value) {
            /** @var TwitterAPIv1CredentialsService $v1Credentials */
            $v1Credentials = app(TwitterAPIv1CredentialsService::class);
            $connection = new TwitterOAuth($v1Credentials->getTwitterApiKey(), $v1Credentials->getTwitterApiSecret());
            $content = $connection->get('statuses/show', ['id' => $data['id']]);

            $bitrates = array_values(array_filter(array_map(fn($v) => isset($v->bitrate) ? ['bitrate' => $v->bitrate, 'url' => $v->url] : null, $content->extended_entities->media[0]->video_info->variants)));
            $maxBitrate = max(array_column($bitrates, 'bitrate'));
            $key = array_search($maxBitrate, array_column($bitrates, 'bitrate'));
            $url = $bitrates[$key]['url'];

            $telegramPayload->video = $url;

            if ($telegramPayload->text) {
                $telegramPayload->caption = $telegramPayload->text;
                $telegramPayload->text = null;
            }
        }

        return parent::handle($dataHub);
    }
}
