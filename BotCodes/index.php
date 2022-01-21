<?php
require_once("config.php");
$json = file_get_contents('php://input');
$tg = Telegram::getInstance($json);
if ($tg->isChannelPost() === false && $tg->isEditChannelPost() === false) {
    $cr = Core::getInstance();
    $db = Database::getInstance();
    $chatId = $tg->getChatId();
    $text = $tg->getMessageText();

    $showLanguageMenu = true;
    $userData = $db->getUserData($chatId);
    $lan = "en";
    if (strlen(json_encode($userData)) > 4) {
        $lan = $userData['_lan'];
        $showLanguageMenu = false;
    }

    require_once("lan/" . $lan . ".php");

    $helper = $text;
    if (strpos($text, "/start") !== false) {
        $text = "Start";
    } elseif (strpos($text, "LimitShadow-") !== false) {
        $text = "Shadowban";
    } elseif (strpos($text, "Language-") !== false) {
        $text = "Language";
    } elseif (strpos($text, "SetLan-") !== false) {
        $text = "ChangeLan";
    } elseif (strpos($text, "SendCode-") !== false) {
        $text = "SendCode";
    } elseif (strpos($text, "HiddenPage-") !== false) {
        $text = "HiddenPage";
    } elseif (strpos($text, "ExitTwitterBoxBot-") !== false) {
        $text = "ExitTwitterBoxBot";
    } elseif (strpos($text, "SubmitExit-") !== false) {
        $text = "SubmitExit";
    } elseif (strpos($text, "UnfollowerFind-") !== false) {
        $text = "UnfollowerFind";
    } elseif (strpos($text, "UnfollowingFind-") !== false) {
        $text = "UnfollowingFind";
    } elseif (strpos($text, "/ignrflwr") !== false) {
        $text = "IgnoreUnfollower";
    } elseif (strpos($text, "/ignrflwn") !== false) {
        $text = "IgnoreUnfollowing";
    } elseif (strpos($text, "/rmvflwr") !== false) {
        $text = "RemoveUnfollower";
    } elseif (strpos($text, "/rmvflwn") !== false) {
        $text = "RemoveUnfollowing";
    } elseif (strpos($text, "getVideo-") !== false) {
        $text = "getVideo";
    } elseif (strpos($text, "/videoget") !== false) {
        $text = "sendVideo";
    } elseif (strpos($text, "/renew") !== false) {
        $text = "Renew";
    } elseif (strpos($text, "id-") !== false) {
        $text = "Id";
    }

    switch ($text) {
        case "Start":
            $cr->getUserLanguage($chatId, $lan, $helper, $showLanguageMenu);
            break;
        case "/help":
            $cr->sendHelpMessage($chatId, $lan, $text);
            break;
        case "/profile":
            $cr->setProfileMenu($chatId, $lan, $helper);
            break;
        case "/link":
        case "SetLink":
            $cr->setInviteLinkMessage($chatId, $lan, $helper);
            break;
        case "/unfollower":
            $cr->setUnfollowerMenu($chatId, $lan, $helper);
            break;
        case "/shadowban":
            $cr->setShadowbanButtonMenu($chatId, $lan, $helper);
            break;
        case "Language":
            $cr->setStartMessage($chatId, $lan, $helper, true);
            break;
        case "SetLan":
            $cr->setLanguegeMenu($chatId, $lan, $helper);
            break;
        case "Shadowban":
            $cr->setShadowbanMenu($chatId, $lan, $helper);
            break;
        case "ChangeLan":
            $cr->setLanguegeMessage($chatId, $lan, $helper);
            break;
        case "/about":
            $cr->sendBotAbout($chatId, $lan, $helper);
            break;
        case "/rank":
            $cr->sendRankMessage($chatId, $lan, $helper);
            break;
        case "/connect":
            $cr->setConnectMessage($chatId, $lan, $helper);
            break;
        case "SendCode":
            $cr->setSubmitCodeMessage($chatId, $lan, $helper);
            break;
        case "HiddenPage":
            $cr->setHiddenPageMessage($chatId, $lan, $helper);
            break;
        case "ExitTwitterBoxBot":
            $cr->setExitTwitterBoxMenu($chatId, $lan, $helper);
            break;
        case "SubmitExit":
            $cr->setSubmitExitMessage($chatId, $lan, $helper);
            break;
        case "HaveActiveAdmin":
            $cr->setHaveActiveAdminMessage($chatId, $lan, $helper);
            break;
        case "UnfollowerFind":
            $cr->setUnfollowerFindMenu($chatId, $lan, $helper);
            break;
        case "Id":
            $cr->setIdTwitterMessage($chatId, $helper);
            break;
        case "IgnoreUnfollower":
            $cr->setIgnoreUnfollowerMessage($chatId, $lan, $helper);
            break;
        case "RemoveUnfollower":
            $cr->setRemoveUnfollowerMessage($chatId, $lan, $helper);
            break;
        case "ignoredUnfollowerList":
            $cr->setIgnoredPersonListMessage($chatId, $lan, $helper, "follower");
            break;
        case "ignoredUnfollowingList":
            $cr->setIgnoredPersonListMessage($chatId, $lan, $helper, "following");
            break;
        case "/whitelist":
            $cr->setWhiteListMenu($chatId, $lan, $helper);
            break;
        case "getVideo":
            $cr->sendVideoMessage($chatId, $lan, $helper);
            break;
        case "/videolist":
            $cr->setVideoListMenu($chatId, $lan, $helper);
            break;
        case "sendVideo":
            $matches = str_replace("/videoget", "", $helper);
            $cr->setTweetVideoMessage($chatId, $lan, $matches, true);
            break;
        case "/statistics":
            $cr->setStatisticsMenu($chatId, $lan, $helper);
            break;
        case "Renew":
            $cr->setRenewMenu($chatId, $lan, $helper);
            break;
        default:
            $regex = '/^((http[s]?|ftp):\/)?\/?([^:\/\s]+)((\/\w+)*\/)([\w\-\.]+[^#?\s]+)(.*)?(#[\w\-]+)?$/m';
            if (preg_match_all($regex, $text, $matches, PREG_SET_ORDER)) {
                $cr->setTweetVideoMessage($chatId, $lan, $matches[0][6], false);
            } else {
                $cr->setProfilePreviewMenu($chatId, $lan, $helper);
            }
            break;
    }
}
