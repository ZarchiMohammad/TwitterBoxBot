<?php
require_once("config.php");
$db = Database::getInstance();
$tg = Telegram::getInstance();

$postId = 10;
$age = time() - _BOT_BIRTHDAY;
$days = floor($age / 86400);
$hours = floor(($age % 86400) / 3600);
$userCount = $db->getTableCount("_user_data");

$post = "#RoBot Name: <b>Twitter Box</b>" . "\n";
$post .= "Username: @TwitterBoxBot" . "\n";
$post .= "Member(s): <code>" . number_format($userCount) . "</code>\n";
$post .= "Version: <code>" . _BOT_VERSION . "</code>\n \n";
$post .= "Created: <code>" . number_format($days) . "</code> day(s), <code>" . $hours . "</code> hour(s) ago.\n";
$post .= "- Time: <code>" . date("H:i:s", _BOT_BIRTHDAY) . "</code> UTC\n";
$post .= "- Date: <code>" . date("Y-m-d", _BOT_BIRTHDAY) . "</code>\n \n";
$post .= "<b>Abilities: </b>\n";
$post .= "v1: Detect user shadowban limitation." . "\n";
$post .= "v2: Preview user profile picture and details." . "\n";
$post .= "v3: Add 🇮🇷 Fa (فارسی) language." . "\n";
$post .= "v4: Add profile menu & invited link." . "\n";
$post .= "v5: Connect to Twitter account without a password" . "\n";
$post .= "v6: Add /unfollower command for identify the people who have unfollowed you" . "\n";
$post .= "v7: Download tweet video and archive it." . "\n";
$post .= "\n";
$post .= "<i>This post is updated automatically every hour.</i>\n";
$post .= "Channel: @ZarchiProjects";

$tg->editMessage(_PROJECTS_CHANNEL, $postId, $post);
