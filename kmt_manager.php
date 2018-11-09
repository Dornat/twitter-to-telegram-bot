<?php

require_once 'vendor/autoload.php';

use Spatie\TwitterStreamingApi\PublicStream;
use App\TweetManager;

$tweetManager = new TweetManager();

PublicStream::create(
    $accessToken,
    $accessTokenSecret,
    $consumerKey,
    $consumerSecret
)->whenTweets('2199708446', function(array $tweet) use ($tweetManager) {
    $tweetManager->manage($tweet);
    echo json_encode($tweet, JSON_PRETTY_PRINT);
})->startListening();
