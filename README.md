# Twitter to Telegram Channel Bot v2.0 (ttt)

This project is built with the help of [laravel-zero](https://laravel-zero.com/).

The aim of this bot is to "seamlessly" duplicate tweets from any public Twitter account to Telegram
channel by leveraging Twitter API v2.0 and Telegram Bot API.

<!--
### A bit of history
The main idea of the bot was (with v1.0) to create a bot that can seamlessly duplicate tweets
from a Twitter account to Telegram channel without any "signs" that the posts in Telegram
is duplicated from Twitter, so basically without any mention of Twitter like urls etc.

The Twitter released its new API version (2.0) and that was one of the reasons why I wanted
to make a new version of my original bot. This one is not a better version than previous one
because Twitter's API v2.0 for some reason doesn't have sources for videos and animated gifs
(v1.1 has this "features" in it), although they say that it's a temporary thing. That's why 
for handling gifs and videos Twitter API v1.1 is still being used.

I stumbled upon a problem that I needed a feature in the original bot to be able to handle
multiple Twitter to Telegram channels at the same time, but I didn't want to touch anything in
the original bot because I thought that sooner or later I will need to rewrite it for v2.0.

So this is the short history of this small application.
-->

## Features

* Possibility to duplicate Twitter account feeds to multiple Telegram channels. E.g. you can
have 1 Twitter account to post to multiple Telegram channels or 2+ separate Twitter to Telegram
pairs.
* Possibility to extend and add your own handlers (pipes) for Twitter feed manipulation. E.g. if
you want to do some manipulation with text from tweets, or maybe you don't need to see any gifs
with cats in your Telegram channel you can make that each tweet can be handled as you see fit.
* I finally decided to make this bot as a standalone app (!), although you still need a php ^8.1
to run the script on your machine.
* And as a result of the previous point you can now pass all the necessary configuration as
arguments to the bot itself on the command line.

## Before using the bot

If you want to use this bot, firstly you need to do some preparations on Twitter and Telegram side.
This app can be used for any Twitter account.

1. Create telegram channel.
2. Create telegram bot (through @botfather), remember it's token to access the HTTP API.
3. Add the newly created telegram bot to your telegram channel of choice and give it access to "Post messages".
Note: one telegram bot can be added to multiple telegram channels.
4. Create an app on [developer.twitter](https://developer.twitter.com).

## Requirements

PHP ^8.1

## Running the bot itself

1. From the command line run:

    `php ttt manage --help`

    This will print help message for the bot.
2. If you wanna do the `.env` stuff then copy `.env.example` file to `.env` file and fill in all the
necessary variables OR fill in the environment variables on your server. The values for `TWITTER_*` can 
be found on [developer.twitter](https://developer.twitter.com). The value for `TELEGRAM_ACCESS_TOKEN`
should be retrieved when you created your telegram bot. 
3. If you wanna use bot's optional arguments than the same approach for filling in the values applies to
all the `--twitter-*` and `--telegram-access-token` optional arguments.
4. Fill in bot's required argument(s) (`<ids>`). It should be a string with a digit form of twitter
id on the left and telegram channel id on the right delimited with `:` (colon).
   * Twitter account id is an id in digit form, to convert your `@twitterid` to digit form you can
   use [this website](https://tweeterid.com/).
   * Telegram channel id is the id in form `@telegramchannelid`.
   
   This required argument (`<ids>`) can be repeated multiple times. E.g.:

    `php ttt manage "1111:@telegram1" "2222:@telegram2" "3333:@telegram3"`

## Development

If you want to build from source or to help the project and fix some bugs or add some features you
can leverage the `Dockerfile` included within the project.

1. Run `docker-compose up -d` in terminal.
2. Run `docker-compose exec app sh` to log in to the container.
3. If you want to build the app then run `php ttt app:build <your-build-name>`.
4. The build will be in the `build` folder.

### Adding new Pipelines (handlers)

If you want to add some custom handlers for the tweets, e.g. add some text to it or remove possibility
to post images to Telegram channel than you can add your own Pipelines.

Pipelines lie in `Pipelines` folder and there you can add you own `PipelineFactory` to handle your
tweets as you see fit. `PipelineFactory` has a chain of `Pipes` to handle tweet content.

The default behaviour for tweet handling lies in `DefaultPipelineFactory`.

Each Pipeline consists of Pipes that handle different parts of the tweet and changes the main
`DataHub` object. Depending on whether tweet has some media (like videos, gifs, photos) or not,
it changes `DataHub` object to represent what the resulting `TelegramPayload` will be.
It's sort of like Chain of Responsibility pattern.

For example if you want to add something to the text or format it somehow you can add your
custom Pipe after default `TextPipe` to format the text as you want.

The `config/pipelines.php` configuration file has a map of pipelines. When you add your new Pipeline
you need to register it in this file with your custom name. Right now it has only on pipeline called
`'default'`.

In order to use custom Pipeline you need to add another optional argument to the bot when running it.
The format with this argument is the same as with the required argument: telegram id to the left and
pipeline name from `pipelines.php` file to the right delimited with `:` (colon). E.g.:

`php ttt manage 1111:@telegramchannelid --channel-pipeline=1111:customPipelineName`


## Stuff to add

* Unit Tests
* Feature Tests
* Logging
* Polls
