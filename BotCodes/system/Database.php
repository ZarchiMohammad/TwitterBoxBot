<?php


class Database
{

    private $connection;
    private static $db;

    public static function getInstance($option = null)
    {
        if (self::$db == null) {
            self::$db = new Database($option);
        }
        return self::$db;
    }

    private function __construct($option = null)
    {
        if ($option != null) {
            $host = $option['host'];
            $user = $option['user'];
            $pass = $option['pass'];
            $name = $option['name'];
        } else {
            global $config;
            $host = $config['host'];
            $user = $config['user'];
            $pass = $config['pass'];
            $name = $config['name'];
        }

        $this->connection = new mysqli($host, $user, $pass, $name);
        if ($this->connection->connect_error) {
            echo "Connection failed: " . $this->connection->connect_error;
            exit;
        }

        $this->connection->query("SET NAMES 'ut8'");
    }

    public function query($sql)
    {
        return $this->connection->query($sql);
    }

    public function insertUserData($chatId, $invited)
    {
        $this->query("INSERT INTO `_user_data` VALUES (NULL, '" . time() . "', '" . $chatId . "','" . $invited . "', 'en', '0', '0',  '20', '--------', '0', '0', '0', '0', '0', '0', '|', '|')");
    }

    public function insertTwitterData($userId, $chatId, $screenName, $signUp)
    {
        $this->query("INSERT INTO `_user_twitter` VALUES (NULL, '" . $userId . "', '" . $chatId . "','" . $screenName . "', '" . $signUp . "', '1')");
    }

    public function insertTweetData($tweetId, $creator, $sender, $type, $videoLow, $videoHigh, $date)
    {
        $this->query("INSERT INTO `_tweet_data` VALUES (NULL, '" . $tweetId . "', '" . $creator . "','|" . $sender . "|', '" . $type . "', '" . $videoLow . "', '" . $videoHigh . "', '" . $date . "')");
    }

    public function insertEntrances($chatId, $lan, $entrance)
    {
        $this->query("INSERT INTO `_user_entrances` VALUES (NULL, '" . time() . "', '" . $chatId . "', '" . $lan . "', '" . $entrance . "')");
    }

    public function getTableCount($table)
    {
        $result = $this->query("SELECT COUNT(*) AS _count FROM `" . $table . "` ");
        return $result->fetch_array()['_count'];
    }

    public function getInvitedCount($chatId)
    {
        $result = $this->query("SELECT COUNT(*) AS _count FROM `_user_data` WHERE `_invited` LIKE '" . $chatId . "'");
        return $result->fetch_array()['_count'];
    }

    public function getUserData($chatId)
    {
        $result = $this->query("SELECT * FROM `_user_data` WHERE `_chatId` LIKE '" . $chatId . "'");
        return $result->fetch_array();
    }

    public function getBotData()
    {
        $result = $this->query("SELECT * FROM `_bot_data` WHERE `_id` = '1'");
        return $result->fetch_array();
    }

    public function getTweetData($tweetId)
    {
        $result = $this->query("SELECT * FROM `_tweet_data` WHERE `_tweet_id` LIKE '" . $tweetId . "'");
        return $result->fetch_array();
    }

    public function getTwitterData($type, $value)
    {
        if ($type == "id") {
            $result = $this->query("SELECT * FROM `_user_twitter` WHERE `_twitter_id` LIKE '" . $value . "'");
        } else {
            $result = $this->query("SELECT * FROM `_user_twitter` WHERE `_screen_name` LIKE '" . $value . "'");
        }
        return $result->fetch_array();
    }

    public function setUserData($chatId, $field, $value)
    {
        $this->query("UPDATE `_user_data` SET " . $field . " = '" . $value . "' WHERE `_chatId` LIKE '" . $chatId . "'");
    }

    public function setBotData($field, $value)
    {
        $this->query("UPDATE `_bot_data` SET " . $field . " = '" . $value . "' WHERE `_id` = '1'");
    }

    public function updateTwitterData($userId, $field, $value)
    {
        $this->query("UPDATE `_user_twitter` SET " . $field . " = '" . $value . "' WHERE `_twitter_id` LIKE '" . $userId . "'");
    }

    public function setTwitterData($tweetId, $field, $value)
    {
        $this->query("UPDATE `_tweet_data` SET " . $field . " = '" . $value . "' WHERE `_tweet_id` LIKE '" . $tweetId . "'");
    }

    public function getReceptorData($group)
    {
        $sql = null;
        switch ($group) {
            case "All":
                $sql = "SELECT `_id`, `_chatId` FROM `_user_data` WHERE `_active` < 2 ORDER BY `_id` ASC";
                break;
            case "En":
                $sql = "SELECT `_id`, `_chatId` FROM `_user_data` WHERE `_lan` LIKE 'en' AND `_active` < 2 ORDER BY `_id` ASC";
                break;
            case "Fa":
                $sql = "SELECT `_id`, `_chatId` FROM `_user_data` WHERE `_lan` LIKE 'fa' AND `_active` < 2 ORDER BY `_id` ASC";
                break;
        }

        $result = $this->query($sql);
        $data = array();
        while ($row = mysqli_fetch_array($result)) {
            $helper['id'] = $row['_id'];
            $helper['chatId'] = $row['_chatId'];
            $data[] = $helper;
        }
        return $data;
    }

    public function getUserStatus()
    {
        $data = array();
        $result = $this->query("SELECT COUNT(*) AS _count FROM `_user_data`");
        $data['All'] = $result->fetch_array()['_count'];
        $result = $this->query("SELECT COUNT(*) AS _count FROM `_user_data` WHERE `_active` < 2 ");
        $data['Active'] = $result->fetch_array()['_count'];
        $result = $this->query("SELECT COUNT(*) as _count FROM `_user_twitter`");
        $data['Accounts'] = $result->fetch_array()['_count'];
        $result = $this->query("SELECT COUNT(*) as _count FROM `_user_twitter` WHERE `_telegram_id` NOT LIKE '--------'");
        $data['Signup'] = $result->fetch_array()['_count'];
        $result = $this->query("SELECT * FROM `_bot_data`");
        $fetch = $result->fetch_array();
        $data['Bd'] = $fetch['_bd'];
        $data['Api'] = $fetch['_api'];
        $data['Insert'] = $fetch['_insert'];
        $data['Update'] = $fetch['_update'];
        $result = $this->query("SELECT COUNT(*) as _count FROM `_tweet_data` WHERE `_type` LIKE 'video'");
        $data['video'] = $result->fetch_array()['_count'];
        return $data;
    }

    public function getUserTwitterAccounts($chatId)
    {
        $sql = "SELECT * FROM `_user_twitter` WHERE `_telegram_id` LIKE '" . $chatId . "'";
        $result = $this->query($sql);
        $data = array();
        while ($row = mysqli_fetch_array($result)) {
            $helper['id'] = $row['_twitter_id'];
            $helper['name'] = $row['_screen_name'];
            $data[] = $helper;
        }
        return $data;
    }

    public function getVideoData($chatId)
    {
        $sql = "SELECT * FROM `_tweet_data` WHERE `_sender` LIKE '%|" . $chatId . "|%' AND `_type` LIKE 'video'";
        $result = $this->query($sql);
        $data = array();
        while ($row = mysqli_fetch_array($result)) {
            $helper['_tweet_id'] = $row['_tweet_id'];
            $helper['_date'] = $row['_date'];
            $data[] = $helper;
        }
        return $data;
    }

    public function removeIgnoredPerson($chatId, $field, $userId)
    {
        $this->query("UPDATE `_user_data` SET `" . $field . "` = REPLACE(`" . $field . "`, '|" . $userId . "|', '|') WHERE `_chatId` LIKE '" . $chatId . "'");
    }
}
