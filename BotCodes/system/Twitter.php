<?php


class Twitter
{

    private static $twttr;
    private static $tae;

    public static function getInstance()
    {
        if (self::$twttr == null) {
            self::$twttr = new Twitter();
        }
        return self::$twttr;
    }

    private function __construct()
    {
        if (self::$tae == null) {
            self::$tae = new TwitterAPIExchange(_TwitterSetting);
        }
    }

    public function getUserData($type, $value)
    {
        $url = 'https://api.twitter.com/1.1/users/show.json';

        if ($type == "id") {
            $getField = '?user_id=' . $value;
        } else {
            $getField = '?screen_name=' . $value;
        }

        return self::$tae->setGetfield($getField)
            ->buildOauth($url, 'GET')
            ->performRequest();
    }

    public function reciveDirect()
    {
        $url = 'https://api.twitter.com/1.1/direct_messages/events/list.json';
        $getField = '?count=50';

        $data = self::$tae->setGetfield($getField)
            ->buildOauth($url, 'GET')
            ->performRequest();
        $direct = json_decode($data, true)['events'];
        $result = array();
        foreach ($direct as $drct) {
            $helper = array();
            $helper['sender_id'] = $drct['message_create']['sender_id'];
            $helper['text'] = $drct['message_create']['message_data']['text'];
            $result[] = $helper;
        }

        return json_encode($result);
    }

    public function getFollowerIds($username)
    {
        $url = 'https://api.twitter.com/1.1/followers/ids.json';
        $getField = '?cursor=-1&user_id=' . $username . '&count=5000';

        $users = self::$tae->setGetfield($getField)
            ->buildOauth($url, 'GET')
            ->performRequest();
        $result = json_decode($users, true);
        return $result['ids'];
    }

    public function getFollowingIds($username)
    {
        $url = 'https://api.twitter.com/1.1/friends/ids.json';
        $getField = '?cursor=-1&user_id=' . $username . '&count=5000';

        $users = self::$tae->setGetfield($getField)
            ->buildOauth($url, 'GET')
            ->performRequest();
        $result = json_decode($users, true);
        return $result['ids'];
    }

}
