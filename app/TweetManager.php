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
            if (array_key_exists('retweeted_status', $tweet)) {
                $data = [
                    'chat_id' => $this->_telegramChatId,
                    'text' => $tweet['retweeted_status']['text']
                ];
                $this->_sendToTelegramChat($data, 'sendMessage');
            } else {
                if (array_key_exists('extended_entities', $tweet)) {
                    $this->_processExtendedEntitiesMedia($tweet, $tweet['text']);
                } elseif (array_key_exists('extended_tweet', $tweet)) {
                    $this->_processExtendedEntitiesMedia($tweet['extended_tweet'], $tweet['extended_tweet']['full_text']);
                } else {
                    $text = $this->_processText($tweet['text']);
                    if (!empty($tweet['entities'])) {
                        if (!empty($tweet['entities']['urls'])) {
                            foreach ($tweet['entities']['urls'] as $url) {
                                $text .= ' ' . $url['url'];
                            }
                        }
                    }
                    $data = [
                        'chat_id' => $this->_telegramChatId,
                        'text' => $text
                    ];
                    $this->_sendToTelegramChat($data, 'sendMessage');
                }
            }
        }
    }

    private function _processExtendedEntitiesMedia($tweet, $text)
    {
        if (!empty($tweet['extended_entities']['media'])) {
            if ($tweet['extended_entities']['media'][0]['type'] === MediaType::ANIMATED) {
                $data = [
                    'chat_id' => $this->_telegramChatId,
                    'animation' => $tweet['extended_entities']['media'][0]['video_info']['variants'][0]['url'],
                    'caption' => $this->_processText($text)
                ];
                $this->_sendToTelegramChat($data, 'sendAnimation');
            } elseif ($tweet['extended_entities']['media'][0]['type'] === MediaType::PHOTO) {
                if (count($tweet['extended_entities']['media']) > 1) {
                    $urls = [];
                    foreach ($tweet['extended_entities']['media'] as $m) {
                        $urls[] = [
                            'type' => 'photo',
                            'media' => $m['media_url_https']
                        ];
                    }
                    $jsonSerializedUrls = json_encode($urls);
                    $data = [
                        'chat_id' => $this->_telegramChatId,
                        'media' => $jsonSerializedUrls
                    ];
                    $this->_sendToTelegramChat($data, 'sendMediaGroup');
                    $data = [
                        'chat_id' => $this->_telegramChatId,
                        'text' => $this->_processText($text)
                    ];
                    $this->_sendToTelegramChat($data, 'sendMessage');
                } else {
                    $data = [
                        'chat_id' => $this->_telegramChatId,
                        'photo' => $tweet['extended_entities']['media'][0]['media_url_https'],
                        'caption' => $this->_processText($text)
                    ];
                    $this->_sendToTelegramChat($data, 'sendPhoto');
                }
            } elseif ($tweet['extended_entities']['media'][0]['type'] === MediaType::VIDEO) {
                $maxBitrate = max(array_column($tweet['extended_entities']['media'][0]['video_info']['variants'], 'bitrate'));
                $key = array_search($maxBitrate, array_column($tweet['extended_entities']['media'][0]['video_info']['variants'], 'bitrate'));
                $url = $tweet['extended_entities']['media'][0]['video_info']['variants'][$key]['url'];
                $data = [
                    'chat_id' => $this->_telegramChatId,
                    'video' => $url,
                    'caption' => $this->_processText($text)
                ];
                $this->_sendToTelegramChat($data, 'sendVideo');
            }
        } else {
            $newText = $this->_processText($text);
            if (!empty($tweet['entities'])) {
                if (!empty($tweet['entities']['urls'])) {
                    foreach ($tweet['entities']['urls'] as $url) {
                        $newText .= ' ' . $url['url'];
                    }
                }
            }
            $data = [
                'chat_id' => $this->_telegramChatId,
                'text' => $newText
            ];
            $this->_sendToTelegramChat($data, 'sendMessage');
        }

    }

    /**
     * @param $text
     * @return string
     */
    private function _processText($text)
    {
        return trim(
            preg_replace(
                '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)$@',
                '',
                $text
            )
        );
    }

    /**
     * @param $data
     * @param $method
     */
    private function _sendToTelegramChat($data, $method)
    {
        file_get_contents(
            "https://api.telegram.org/bot{$this->_apiToken}/{$method}?"
            . http_build_query($data)
        );
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