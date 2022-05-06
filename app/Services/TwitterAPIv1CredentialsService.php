<?php

namespace App\Services;

/**
 * This service is used for getting credentials when using Twitter API v1.1.
 */
class TwitterAPIv1CredentialsService
{
    private string $twitterApiKey;
    private string $twitterApiSecret;

    public function create(string $twitterApiKey, string $twitterApiSecret): void
    {
        $this->twitterApiKey = $twitterApiKey;
        $this->twitterApiSecret = $twitterApiSecret;
    }

    /**
     * @return string
     */
    public function getTwitterApiKey(): string
    {
        return $this->twitterApiKey;
    }

    /**
     * @return string
     */
    public function getTwitterApiSecret(): string
    {
        return $this->twitterApiSecret;
    }
}
