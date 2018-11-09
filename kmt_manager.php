<?php

require_once 'vendor/autoload.php';

use Spatie\TwitterStreamingApi\PublicStream;
use App\TweetManager;

const KYIV_METRO_ALERTS_ID = '2199708446';

$tweetManager = new TweetManager();

PublicStream::create(
    $tweetManager->getAccessToken(),
    $tweetManager->getAccessTokenSecret(),
    $tweetManager->getConsumerKey(),
    $tweetManager->getConsumerSecret()
)->whenTweets(KYIV_METRO_ALERTS_ID, function(array $tweet) use ($tweetManager) {
    $tweetManager->manage($tweet);
    echo json_encode($tweet, JSON_PRETTY_PRINT);
})->startListening();
