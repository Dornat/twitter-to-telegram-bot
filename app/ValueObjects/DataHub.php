<?php

namespace App\ValueObjects;

class DataHub
{
    private array $tweet;

    private TelegramPayload $telegramPayload;

    public function __construct(array $tweet, TelegramPayload $telegramPayload = null)
    {
        $this->tweet = $tweet;
        $this->telegramPayload = $telegramPayload ?: new TelegramPayload();
    }

    public function getTweet(): array
    {
        return $this->tweet;
    }

    public function getTelegramPayload(): TelegramPayload
    {
        return $this->telegramPayload;
    }

    public function setTelegramPayload(TelegramPayload $payload): void
    {
        $this->telegramPayload = $payload;
    }
}
