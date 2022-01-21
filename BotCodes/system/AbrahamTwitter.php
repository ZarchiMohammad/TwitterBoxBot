<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class AbrahamTwitter
{
    private static $at;
    private static $connection;

    public static function getInstance()
    {
        if (self::$at == null) {
            self::$at = new AbrahamTwitter();
        }
        return self::$at;
    }

    public function __construct()
    {
        global $config;
        self::$connection = new TwitterOAuth(
            $config['consumer_key'],
            $config['consumer_secret'],
            $config['oauth_access_token'],
            $config['oauth_access_token_secret']
        );
    }

    public function getTweetData($tweetId)
    {
      return self::$connection->get('statuses/show', [
            'id' => $tweetId,
            'tweet_mode' => 'extended',
            'include_entities' => 'true'
        ]);
    }
}
