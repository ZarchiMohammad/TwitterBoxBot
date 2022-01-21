<?php

require_once("system/Telegram.php");
require_once("system/Database.php");
require_once("system/TwitterAPIExchange.php");
require_once("system/twitteroauth/autoload.php");
require_once("system/Twitter.php");
require_once("system/AbrahamTwitter.php");
require_once("system/Core.php");

const _TOKEN = "--TelegramBotToken--";
const _BotName = "--BotName--";
const _ADMIN = "--AdminChatId";

const _BOT_VERSION = "--Version--";
const _BOT_BIRTHDAY = "--TimestampOfBotCreateTime--";

const _InviteScore = 10;
const _REPORT_CHANNEL = "--ChannelChatId--";
const _PROJECTS_CHANNEL = "--ChannelChatId--";
const _VIDEO_CHANNEL = "--ChannelChatId--";

const _unfollowerCondition = 5000;


const _timeVideo = 60;
const _timeDirect1 = 120;
const _timeDirect2 = 120;
const _timeProfile = 60; //
const _timeShadowban = 60; // 2H
const _timeUnFollower = 600; // 3H

global $config;
$config['host'] = "localhost";
$config['user'] = "--Username--";
$config['pass'] = "--Password--";
$config['name'] = "--DatadaseName--";

$config['consumer_key'] = "--ConsumerKey--";
$config['consumer_secret'] = "--ConsomerSecret--";
$config['oauth_access_token'] = "--OauthAccessToken--";
$config['oauth_access_token_secret'] = "--OauthAccessTokenSecret--";

define("_TwitterSetting", array(
    'oauth_access_token' => $config['oauth_access_token'],
    'oauth_access_token_secret' => $config['oauth_access_token_secret'],
    'consumer_key' => $config['consumer_key'],
    'consumer_secret' => $config['consumer_secret']
));
