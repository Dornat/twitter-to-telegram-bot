<?php

namespace App\Pipes;


use App\ValueObjects\DataHub;
use App\ValueObjects\TwitterMediaType;

class PicturePipe extends PipeAbstract
{
    public function handle(DataHub $dataHub): mixed
    {
        $media = $dataHub->getTweet()['includes']['media'] ?? null;
        if (is_null($media)) {
            return parent::handle($dataHub);
        }
        $telegramPayload = $dataHub->getTelegramPayload();

        if (!empty($media[0]['type']) && $media[0]['type'] === TwitterMediaType::PHOTO->value) {
            if (count($media) > 1) {
                $mediaArray = array_map(function ($m) {
                    return [
                        'type' => TwitterMediaType::PHOTO->value,
                        'media' => $m['url'],
                    ];
                }, $media);
                // In order to create caption for MediaGroup in Telegram we need to set caption
                // ONLY for the first element in a media array.
                $mediaArray[0]['caption'] = $telegramPayload->text;

                $telegramPayload->media = json_encode($mediaArray);
            } else {
                $telegramPayload->photo = $media[0]['url'];

                if ($telegramPayload->text) {
                    $telegramPayload->caption = $telegramPayload->text;
                    $telegramPayload->text = null;
                }
            }
        }

        return parent::handle($dataHub);
    }
}
