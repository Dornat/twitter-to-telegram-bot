# General usage

This app can be used for any twitter account.

1. Create telegram channel
2. Create telegram bot, remember it's token to access the HTTP API
3. Create an app on [developer.twitter](https://developer.twitter.com)
4. Rename or copy `.env.example` file to `.env`.
5. Fill in all necessary environment variables:
* `CONSUMER_KEY`, `CONSUMER_SECRET`, `ACCESS_TOKEN`, `ACCESS_TOKEN_SECRET` is found on [developer.twitter](https://developer.twitter.com) on your app's page in `Keys and tokens` tab
* `API_TOKEN` is your telegram bot access token
* `TELEGRAM_CHAT_ID` is an id in form `@telegramchannelid` of your created telegram channel
* `TWITTER_CHANNEL_ID` is an id of chosen twitter account in digit form, to convert your `@twitterid` to digit form you can use [this website](https://tweeterid.com/)
6. Run `composer update` in terminal
7. Run the manager itself: `php kmt_manager.php`
8. Add bot to your created channel
