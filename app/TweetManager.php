<?php

namespace App;


class TweetManager
{
    const CHAT_ID = '@kyivmetrotweet';

    private $_apiToken;
    private $_accessToken;
    private $_accessTokenSecret;
    private $_consumerKey;
    private $_consumerSecret;

    public function __construct()
    {
        $this->_apiToken = getenv('API_TOKEN');
        $this->_accessToken = getenv('ACCESS_TOKEN');
        $this->_accessTokenSecret = getenv('ACCESS_TOKEN_SECRET');
        $this->_consumerKey = getenv('CONSUMER_KEY');
        $this->_consumerSecret = getenv('CONSUMER_SECRET');
    }

    /**
     * Manages tweet according to tweet type and tweet content
     * @param array $tweet
     */
    public function manage($tweet)
    {
        if (array_key_exists('text', $tweet)) {
            $text = $tweet['text'];
            if (array_key_exists('retweeted_status', $tweet)) {
               $text = $tweet['retweeted_status']['extended_tweet']['full_text'];
            }
            $data = [
                'chat_id' => self::CHAT_ID,
                'text' => $text
            ];

            file_get_contents(
                "https://api.telegram.org/bot{$this->_apiToken}/sendMessage?"
                . http_build_query($data)
            );
        }
    }

    /**
     * @return false|string
     */
    public function getApiToken()
    {
        return $this->_apiToken;
    }

    /**
     * @return false|string
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * @return false|string
     */
    public function getAccessTokenSecret()
    {
        return $this->_accessTokenSecret;
    }

    /**
     * @return false|string
     */
    public function getConsumerKey()
    {
        return $this->_consumerKey;
    }

    /**
     * @return false|string
     */
    public function getConsumerSecret()
    {
        return $this->_consumerSecret;
    }
}