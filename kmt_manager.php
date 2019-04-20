<?php

require_once 'vendor/autoload.php';

use Spatie\TwitterStreamingApi\PublicStream;
use App\TweetManager;

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$tweetManager = new TweetManager();

PublicStream::create(
    $tweetManager->getAccessToken(),
    $tweetManager->getAccessTokenSecret(),
    $tweetManager->getConsumerKey(),
    $tweetManager->getConsumerSecret()
)->whenTweets($tweetManager->getTwitterChannelId(), function(array $tweet) use ($tweetManager) {
    $tweetManager->manage($tweet);
    echo json_encode($tweet, JSON_PRETTY_PRINT);
})->startListening();
