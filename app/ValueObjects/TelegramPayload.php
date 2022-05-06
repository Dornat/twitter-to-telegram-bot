<?php

namespace App\ValueObjects;

class TelegramPayload
{
    public function __construct(
        public ?string $chat_id = null,
        public ?string $text = null,
        public ?string $caption = null,
        public ?string $photo = null,
        public ?string $video = null,
        public ?string $media = null,
        public ?string $animation = null,
    )
    {
    }
}
