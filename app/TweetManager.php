<?php

namespace App;


class TweetManager
{
    /** @var string */
    private $_apiToken;

    /** @var string */
    private $_accessToken;

    /** @var string */
    private $_accessTokenSecret;

    /** @var string */
    private $_consumerKey;

    /** @var string */
    private $_consumerSecret;

    /** @var string */
    private $_telegramChatId;

    /** @var string */
    private $_twitterChannelId;

    public function __construct()
    {
        $this->_apiToken = getenv('API_TOKEN');
        $this->_accessToken = getenv('ACCESS_TOKEN');
        $this->_accessTokenSecret = getenv('ACCESS_TOKEN_SECRET');
        $this->_consumerKey = getenv('CONSUMER_KEY');
        $this->_consumerSecret = getenv('CONSUMER_SECRET');
        $this->_telegramChatId = getenv('TELEGRAM_CHAT_ID');
        $this->_twitterChannelId = getenv('TWITTER_CHANNEL_ID');
    }

    /**
     * Manages tweet according to tweet type and tweet content
     * @param array $tweet
     */
    public function manage($tweet)
    {
        if (array_key_exists('text', $tweet) && $tweet['user']['id_str'] === $this->_twitterChannelId && is_null($tweet['in_reply_to_status_id'])) {
            $text = $tweet['text'];
            if (array_key_exists('retweeted_status', $tweet)) {
                $text = $tweet['retweeted_status']['extended_tweet']['full_text'];
            }
            $data = [
                'chat_id' => $this->_telegramChatId,
                'text' => $text
            ];

            file_get_contents(
                "https://api.telegram.org/bot{$this->_apiToken}/sendMessage?"
                . http_build_query($data)
            );
        }
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->_apiToken;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * @return string
     */
    public function getAccessTokenSecret()
    {
        return $this->_accessTokenSecret;
    }

    /**
     * @return string
     */
    public function getConsumerKey()
    {
        return $this->_consumerKey;
    }

    /**
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->_consumerSecret;
    }

    /**
     * @return string
     */
    public function getTelegramChatId()
    {
        return $this->_telegramChatId;
    }

    /**
     * @return string
     */
    public function getTwitterChannelId()
    {
        return $this->_twitterChannelId;
    }
}