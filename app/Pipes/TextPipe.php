<?php

namespace App\Pipes;

use App\ValueObjects\DataHub;

class TextPipe extends PipeAbstract
{
    public function handle(DataHub $dataHub): mixed
    {
        $tweet = $dataHub->getTweet();
        $telegramPayload = $dataHub->getTelegramPayload();
        // We need to clean up the message from any kind of urls
        // because if tweet has some urls in it then they will be
        // present in entities array, and we can extract proper urls from there.
        $text = $this->deurlify($tweet['data']['text']);

        if (!empty($tweet['data']['entities']) && !isset($tweet['includes']['media'])) {
            if (!empty($tweet['data']['entities']['urls'])) {
                foreach ($tweet['data']['entities']['urls'] as $urlObject) {
                    $text .= ' ' . $urlObject['expanded_url'];
                }
            }
        }

        $telegramPayload->text = $text;

        $dataHub->setTelegramPayload($telegramPayload);

        return parent::handle($dataHub);
    }

    private function deurlify($text): string
    {
        return trim(
            preg_replace(
                '@(https?://([-\w.]+[-\w])+(:\d+)?(/([\w/_.#-]*(\?\S+)?[^.\s])?)?)$@',
                '',
                $text
            )
        );
    }
}
