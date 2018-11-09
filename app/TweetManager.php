<?php

namespace App;


class TweetManager
{
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
            $data = [
                'chat_id' => '@kyivmetrotweet',
                'text' => $tweet['text']
            ];

            file_get_contents(
                "https://api.telegram.org/bot{$this->_apiToken}/sendMessage?"
                . http_build_query($data)
            );
        }
    }
}