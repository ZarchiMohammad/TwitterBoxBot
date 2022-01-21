<?php


class Core
{

    private static $cr;
    private static $db;
    private static $tg;
    private static $ta;

    public static function getInstance()
    {
        if (self::$cr == null) {
            self::$cr = new Core();
        }
        return self::$cr;
    }

    public function __construct()
    {
        self::$db = Database::getInstance();
        self::$tg = Telegram::getInstance();
        self::$ta = Twitter::getInstance();
    }

    public function sendUserEntrance($chatId, $lan, $entrance, $function)
    {
        self::$db->insertEntrances($chatId, $lan, $entrance);
        if ($entrance != null && strpos("/start", $entrance) !== false) {
            $message = "E: <code>" . $entrance . "</code>" . "\n";
            $message .= "F: <code>" . $function . "</code>" . "\n";
            $message .= "U: <code>" . $chatId . "</code> (<a href='tg://user?id=" . $chatId . "'>view</a>) \n";
            $message .= "N: <code>" . self::$tg->getFirstName() . " " . self::$tg->getLastName() . "</code>\n";
            $message .= "T: <code>" . time() . "</code>\n";
            self::$tg->sendMessage(_REPORT_CHANNEL, $message);
        }
    }

    public function setDeactiveMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        self::$tg->sendMessage($chatId, _Msg_Deactivate);
    }

    public function getUserLanguage($chatId, $lan, $text, $showLanguegeMenu)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);

        if ($showLanguegeMenu == true) {
            $invited = "--------";
            if (strlen($text) > 7) {
                $invited = str_replace("/start ", "", $text);
                $inviteScore = self::$db->getUserData($invited)['_score'] + _InviteScore;
                self::$db->setUserData($invited, "_score", $inviteScore);
            }
            self::$db->insertUserData($chatId, $invited);
            $message = _Msg_Language;
            $body[0]['text'] = "🇬🇧 English";
            $body[0]['callback_data'] = "Language-en";
            $body[1]['text'] = "🇮🇷 فارسی";
            $body[1]['callback_data'] = "Language-fa";
            $buttons = array('body' => $body, 'bodyVertical' => 2);
            self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
        } else {
            $this->setStartMessage($chatId, $lan, "gul -> ssm", false);
        }
    }

    public function setStartMessage($chatId, $lan, $text, $updateUserLan)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $userData = self::$db->getUserData($chatId);
        if ($userData['_active'] > 0) {
            self::$db->setUserData($chatId, "_active", "0");
        }

        if ($updateUserLan == true) {
            $lan = str_replace("Languege-", "", $text);
            if ($lan != "en") {
                self::$db->setUserData($chatId, "_lan", "fa");
                $message = "Languege updated to 🇮🇷 Fa (فارسی)" . "\n";
                $message .= "Please click on /start again.";
                self::$tg->sendMessage($chatId, $message);
            } else {
                self::$tg->sendMessage($chatId, _Msg_Welcome);
            }
        } else {
            self::$tg->sendMessage($chatId, _Msg_Welcome);
        }
    }

    public function setProfilePreviewMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $userData = self::$db->getUserData($chatId);
        if ($userData['_score'] > 0) {
            // کاربر امتیاز لازم برای بررسی را دارد
            if (time() > $userData['_timeProfile']) {
                // اگر کاربر بعد از زمان تعیین شده چک کرد
                if (preg_match("/^.*[A-Z|a-z|0-9|\-\=\?\/]/", $text) == true) {
                    // یوزرنیم وارد شده صحیح است
                    self::$db->setUserData($chatId, "_timeProfile", time() + _timeProfile);
                    $repId = array("@", "twitter.com/", "https://", "?s=09");
                    $username = str_replace($repId, "", $text);
                    $twitterData = self::$ta->getUserData("name", $username);
                    $twitterData = json_decode($twitterData, true);
                    if ($twitterData['screen_name'] != null) {
                        // اگر کاربر داخل توییتر وجود داشت
                        $userProfileImage = str_replace("_normal", "", $twitterData['profile_image_url_https']);
                        $userTwitterData = self::$db->getTwitterData("id", $twitterData['id']);
                        if (strlen(json_encode($userTwitterData)) > 4) {
                            // حساب توییتر در دیتابیس وجود داشت
                            self::$db->updateTwitterData($twitterData['id'], "_screen_name", $twitterData['screen_name']);
                            $message = $this->setUserMessageBody($twitterData);
                            $message .= "\n" . $this->scoreManager($chatId, 1) . "\n";
                            $message .= _Msg_MoreScore;
                            if ($userTwitterData['_telegram_id'] == "--------") {
                                // حساب توییتر ادمین ندارد
                                $body[0]['text'] = "Check again";
                                $body[0]['callback_data'] = "/connect" . $twitterData['id'];
                                $body[1]['text'] = _Btn_ConnectAccount;
                                $body[1]['callback_data'] = "/connect";
                                $buttons = array('body' => $body, 'bodyVertical' => 1);
                                self::$tg->sendInlineKeyboard($chatId, $message, "photo", $userProfileImage, $buttons);
                            } else {
                                if ($userTwitterData['_telegram_id'] == $chatId) {
                                    // کاربر خودش ادمین حساب توییر است
                                    $header[0]['text'] = _Btn_ChackProfile;
                                    $header[0]['callback_data'] = $userTwitterData['_screen_name'];
                                    $body[0]['text'] = _Btn_Shadowban;
                                    $body[0]['callback_data'] = "LimitShadow-" . $userTwitterData['_screen_name'];
                                    $body[1]['text'] = _Btn_UnfollowerFind;
                                    $body[1]['callback_data'] = "UnfollowerFind-" . $twitterData['id'];
                                    $buttons = array(
                                        'header' => $header, 'headerVertical' => 1,
                                        'body' => $body, 'bodyVertical' => 2
                                    );
                                    self::$tg->sendInlineKeyboard($chatId, $message, "photo", $userProfileImage, $buttons);
                                } else {
                                    if ($userTwitterData['_show']) {
                                        // ادمین قابلیت نمایش پروفایل را باز گذاشته
                                        $body[0]['text'] = _Btn_HaveActiveAdmin;
                                        $body[0]['callback_data'] = "HaveActiveAdmin";
                                        $buttons = array('body' => $body, 'bodyVertical' => 1);
                                        self::$tg->sendInlineKeyboard($chatId, $message, "photo", $userProfileImage, $buttons);
                                    } else {
                                        self::$tg->sendMessage($chatId, _Msg_HiddenInformation);
                                    }
                                }
                            }
                        } else {
                            self::$db->insertTwitterData($twitterData['id'], "--------", $twitterData['screen_name'], $this->setSignUpTimestamp($twitterData['created_at']));
                            $message = $this->setUserMessageBody($twitterData);
                            $body[0]['text'] = _Btn_ConnectAccount;
                            $body[0]['callback_data'] = "/connect";
                            $buttons = array('body' => $body, 'bodyVertical' => 1);
                            self::$tg->sendInlineKeyboard($chatId, $message, "photo", $userProfileImage, $buttons);
                        }
                    } else {
                        self::$tg->sendMessage($chatId, _Msg_UserNotFound);
                    }
                } else {
                    self::$tg->sendMessage($chatId, _Msg_ValidUsername);
                }
            } else {
                $time = $userData['_timeProfile'] - time();
                $min = ceil($time / 60);
                $message = _Msg_TimePermision . $min . " " . _Msg_Minutes;
                self::$tg->sendMessage($chatId, $message);
            }
        } else {
            $message = _Msg_EmptyScore . "\n";
            $message .= _Msg_Score . ": 0 ♘";
            $message .= _Msg_MoreScore;
            self::$tg->sendMessage($chatId, $message);
        }
    }


    public function setShadowbanMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $userData = self::$db->getUserData($chatId);
        if ($userData['_score'] > 0) {
            $text = str_replace("LimitShadow-", "", $text);
            if (time() > $userData['_timeShadowban']) {
                $userTwitterData = self::$db->getTwitterData("name", $text);
                if (strlen(json_encode($userTwitterData)) > 4) {
                    // کاربر در دردیتابیس وجود داشت
                    if ($userTwitterData['_telegram_id'] == $chatId) {
                        // اگر کاربر ادمین بود
                        self::$db->setUserData($chatId, "_timeShadowban", time() + _timeShadowban);
                        $data = self::$tg->sendMessage($chatId, _Msg_PleaseWait);
                        $messageId = self::$tg->getMessageId(json_decode($data));
                        sleep(1);

                        $username = str_replace("LimitShadow-", "", $text);

                        $curl = curl_init("https://shadowban.eu/.api/" . $username);
                        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0");
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                        $page = curl_exec($curl);
                        if (curl_errno($curl)) {
                            echo 'Scraper error: ' . curl_error($curl);
                            exit;
                        }
                        curl_close($curl);

                        $data = json_decode($page);
                        if ($data->profile->protected) {
                            self::$tg->sendEditMessage($chatId, _Msg_PrivatePage, $messageId, $username);
                        } else {

                            if ($data->profile->exists) {
                                $message = "<a href='https://twitter.com/" . $username . "'>@" . $username . "</a> " . _Msg_Exists . "\n\n";

                                if ($data->tests->ghost->ban) {
                                    $message .= _Msg_SearchLimit . _Msg_Yes . "\n";
                                    $message .= _Msg_Ghostlimit . _Msg_Yes . "\n";
                                } else {
                                    $message .= _Msg_SearchLimit . _Msg_No . "\n";
                                    $message .= _Msg_Ghostlimit . _Msg_No . "\n";
                                }

                                if ($data->tests->more_replies->ban) {
                                    $message .= _Msg_Replylimit . _Msg_Yes . "\n";
                                } else {
                                    $message .= _Msg_Replylimit . _Msg_No . "\n";
                                }
                            } else {
                                $message = "<a href='https://twitter.com/" . $username . "'>@" . $username . "</a> " . _Msg_IsNoLimit;
                            }

                            $message .= "\n" . $this->scoreManager($chatId, 1) . "\n";
                            $message .= _Msg_MoreScore;
                            self::$tg->sendEditMessage($chatId, $message, $messageId, $username);
                        }
                    } else {
                        self::$tg->sendMessage($chatId, _Msg_HiddenInformation);
                    }
                } else {
                    self::$tg->sendMessage($chatId, _Msg_ConnectTwitterProfile);
                }
            } else {
                $time = $userData['_timeShadowban'] - time();
                $min = ceil($time / 60);
                $message = _Msg_TimePermision . $min . " " . _Msg_Minutes;
                self::$tg->sendMessage($chatId, $message);
            }
        } else {
            $message = _Msg_EmptyScore . "\n";
            $message .= _Msg_Score . ": 0 ♘";
            $message .= _Msg_MoreScore;
            self::$tg->sendMessage($chatId, $message);
        }
    }

    public function setProfileMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);

        $data = self::$db->getUserData($chatId);
        $ts = time() - $data['_sign_date'];
        $days = floor($ts / 86400);
        $hours = floor(($ts % 86400) / 3600);
        $invited = self::$db->getInvitedCount($chatId);
        $score = $data['_dev'] ? "∞" : $data['_score'];

        $message = _Msg_Name . " " . self::$tg->getFirstName() . " " . self::$tg->getLastName() . "\n";
        $message .= _Msg_SignUp . ": <code>" . number_format($days) . "</code> " . _Msg_Days . ", <code>" . $hours . "</code> " . _Msg_Hours . " " . _Msg_Ago . ".\n";
        $message .= "- " . _Msg_Time . ": <code>" . date("H:i:s", $data['_sign_date']) . " UTC</code>\n";
        $message .= "- " . _Msg_Date . ": <code>" . date("Y-m-d", $data['_sign_date']) . "</code>\n";
        $message .= _Msg_Score . ": <code>" . $score . "</code> ♘" . "\n";
        $message .= _Msg_Invited . ": <code>" . $invited . "</code> 🙎‍♂️" . "\n";
        $message .= _Msg_YourRank . ": " . $this->setUserRank($chatId, $invited);
        $body[0]['text'] = _Btn_Languege;
        $body[0]['callback_data'] = "SetLan";
        $body[1]['text'] = _Btn_Link;
        $body[1]['callback_data'] = "SetLink";
        $buttons = array('body' => $body, 'bodyVertical' => 2);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setUserRank($chatId, $invited)
    {
        $result = null;
        switch ($chatId) {
            case _ADMIN:
                $result = _Msg_DeMedal;
                break;
            default:
                if ($invited >= 8000) {
                    $result = _Msg_KiMedal;
                } elseif ($invited < 4000 && $invited >= 2000) {
                    $result = _Msg_GiMedal;
                } elseif ($invited < 4000 && $invited >= 2000) {
                    $result = _Msg_StMedal;
                } elseif ($invited < 2000 && $invited >= 1000) {
                    $result = _Msg_NdMedal;
                } elseif ($invited < 1000) {
                    $result = _Msg_RdMedal;
                }
                break;
        }

        return $result;
    }

    public function setInviteLinkMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);

        self::$tg->sendMessage($chatId, _Msg_LinkDetails);
        $message = "https://t.me/" . _BotName . "?start=" . $chatId;
        self::$tg->sendMessage($chatId, $message);
    }

    public function setLanguegeMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $message = _Msg_Language;
        $body[0]['text'] = "🇬🇧 English";
        $body[0]['callback_data'] = "SetLan-en";
        $body[1]['text'] = "🇮🇷 فارسی";
        $body[1]['callback_data'] = "SetLan-fa";
        $buttons = array('body' => $body, 'bodyVertical' => 2);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setLanguegeMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $lan = str_replace("SetLan-", "", $text);
        self::$db->setUserData($chatId, "_lan", $lan);
        self::$tg->sendMessage($chatId, _Msg_PleaseClick . ": /profile");
    }

    public function sendBotAbout($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        self::$tg->sendMessage($chatId, "https://t.me/ZarchiProjects/10");
    }

    public function sendHelpMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        self::$tg->sendMessage($chatId, _Msg_Help);
    }

    public function sendRankMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        self::$tg->sendMessage($chatId, _Msg_Rank);
    }

    public function setConnectMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $userData = self::$db->getUserData($chatId);
        if ($userData['_score'] > 5) {
            $verifiedNumber = $this->setVerifiedNumber();
            self::$db->setUserData($chatId, "_verified", $verifiedNumber);
            $message = _Msg_ConnectOne . " <code>" . $verifiedNumber . "</code>\n \n";
            $message .= _Msg_ConnectTwo;
            self::$tg->sendConnectCodeMenu($chatId, $verifiedNumber, $message);
            self::$db->setUserData($chatId, "_timeDirect", time() + _timeDirect1);
        } else {
            $message = _Msg_EmptyScore . "\n";
            $message .= _Msg_MoreScore . "\n";
            self::$tg->sendMessage($chatId, $message);
        }
    }

    public function setSubmitCodeMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $userData = self::$db->getUserData($chatId);
        if (time() > $userData['_timeDirect']) {
            $user_id = null;
            $verified = str_replace("SendCode-", "", $text);
            $data = self::$ta->reciveDirect();
            $data = json_decode($data, true);
            foreach ($data as $drct) {
                if ($drct['text'] == $verified) {
                    $user_id = $drct['sender_id'];
                    break;
                }
            }

            if ($user_id != null) {
                $data = self::$ta->getUserData("id", $user_id);
                $user = json_decode($data, true);
                $timeStamp = $this->setSignUpTimestamp($user['created_at']);

                $twitterData = self::$db->getTwitterData("id", $user_id);
                if (strlen(json_encode($twitterData)) > 4) {
                    self::$db->updateTwitterData($user_id, "_telegram_id", $chatId);
                    self::$db->updateTwitterData($user_id, "_screen_name", $user['screen_name']);
                } else {
                    self::$db->insertTwitterData($user_id, $chatId, $user['screen_name'], $timeStamp);
                }

                $message = $this->setUserMessageBody(json_decode($data, true));
                self::$tg->sendMessage($chatId, $message);

                $message = _Msg_PrivateAccount;
                $body[0]['text'] = _Msg_No;
                $body[0]['callback_data'] = "HiddenPage-" . $user_id . "-0";
                $body[1]['text'] = _Msg_Yes;
                $body[1]['callback_data'] = "HiddenPage-" . $user_id . "-1";
                $buttons = array('body' => $body, 'bodyVertical' => 2);
                self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
            } else {
                self::$db->setUserData($chatId, "_timeDirect", time() + _timeDirect2);
                self::$tg->sendMessage($chatId, _Msg_CurrectlySendCode);
            }
        } else {
            $time = $userData['_timeDirect'] - time();
            $message = _Msg_TimePermision . $time . " " . _Msg_Seconds;
            self::$tg->sendMessage($chatId, $message);
        }
    }

    public function setHiddenPageMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $message = null;
        $data = explode("-", $text);
        $twitterData = self::$db->getTwitterData("id", $data[1]);
        if ($data[2] == "0") {
            self::$db->updateTwitterData($data[1], "_show", "0");
            $message = _Msg_PrivateInformation;
        } else {
            $message = _Msg_PublicInformation;
        }
        $body[0]['text'] = _Btn_Shadowban;
        $body[0]['callback_data'] = "LimitShadow-" . $twitterData['_screen_name'];
        $body[1]['text'] = _Btn_UnfollowerFind;
        $body[1]['callback_data'] = "UnfollowerFind-" . $data[1];
        $buttons = array('body' => $body, 'bodyVertical' => 2);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setIdTwitterMessage($chatId, $text)
    {
        $id = str_replace("id-", "", $text);
        $twitterData = self::$ta->getUserData("id", $id);
        $data = json_decode($twitterData, true);
        $message = "Id → <code>" . $id . "</code>\n";
        $message .= "Name → <code>" . $data['name'] . "</code>" . "\n";
        $message .= "User → <a href='https://twitter.com/" . $data['screen_name'] . "'>@" . $data['screen_name'] . "</a>";

        $body[0]['text'] = "Check Again";
        $body[0]['callback_data'] = "id-" . $id;
        $buttons = array('body' => $body, 'bodyVertical' => 1);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setExitTwitterBoxMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $twitterId = str_replace("ExitTwitterBoxBot-", "", $text);
        $twitterData = self::$db->getTwitterData("id", $twitterId);
        $message = _Msg_InjectTwitterProfileOne . "<a href='https://twitter.com/" . $twitterData['_screen_name'] . "'>@" . $twitterData['_screen_name'] . "</a> ";
        $message .= _Msg_InjectTwitterProfileTwo;
        $body[0]['text'] = _Msg_No;
        $body[0]['callback_data'] = "SubmitExit-No-" . $twitterId;
        $body[1]['text'] = _Msg_Yes;
        $body[1]['callback_data'] = "SubmitExit-Yes-" . $twitterId;
        $buttons = array('body' => $body, 'bodyVertical' => 2);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setSubmitExitMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $data = explode("-", $text);
        if ($data[1] == "Yes") {
            self::$db->updateTwitterData($data[2], "_telegram_id", "--------");
            self::$db->updateTwitterData($data[2], "_show", "1");
            self::$tg->sendMessage($chatId, _Msg_SubmitExitYes);
        } else {
            self::$tg->sendMessage($chatId, _Msg_SubmitExitNo);
        }
    }

    public function setHaveActiveAdminMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        self::$tg->sendMessage($chatId, _Msg_HaveActiveAdmin);
    }

    public function setUnfollowerFindMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $userId = str_replace("UnfollowerFind-", "", $text);
        $userData = self::$db->getUserData($chatId);
        if ($userData['_score'] >= 3) {
            // کاربر امتیاز لازم برای بررسی را دارد
            if (time() > $userData['_timeUnfollower']) {
                // اگر کاربر بعد از زمان تعیین شده چک کرد
                $userTwitterData = self::$db->getTwitterData("id", $userId);
                if ($userTwitterData['_telegram_id'] != "--------") {
                    // حساب توییتر ادمین دارد
                    if ($userTwitterData['_telegram_id'] == $chatId) {
                        // کاربر خودش ادمین است
                        $twitterData = self::$ta->getUserData("id", $userId);
                        $twitterData = json_decode($twitterData, true);
                        if ($twitterData['followers_count'] < _unfollowerCondition && $twitterData['friends_count'] < _unfollowerCondition) {
                            // اگر تعداد فالورها و فالوینگ‌ها کمتر از 5000 بود
                            $message = $this->setUnfollowerHelper($chatId, $userId);
                            $message .= $this->scoreManager($chatId, 1) . "\n";
                            $message .= _Msg_MoreScore;
                            $body[0]['text'] = _Btn_ChackProfile;
                            $body[0]['callback_data'] = "UnfollowerFind-" . $userId;
                            $buttons = array('body' => $body, 'bodyVertical' => 1);
                            self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
                            self::$db->setUserData($chatId, "_timeUnfollower", time() + _timeUnFollower);
                        } else {
                            self::$tg->sendMessage($chatId, _Msg_UnfollowerCondition);
                        }
                    } else {
                        self::$tg->sendMessage($chatId, _Msg_GetDataActiveAdmin);
                    }
                } else {
                    self::$tg->sendMessage($chatId, _Msg_ConnectTwitterProfile);
                }
            } else {
                $time = $userData['_timeUnfollower'] - time();
                $min = ceil($time / 60);
                $message = _Msg_TimePermision . $min . " " . _Msg_Minutes;
                self::$tg->sendMessage($chatId, $message);
            }
        } else {
            $message = _Msg_Score . ": ♘ < 3" . "\n";
            $message .= _Msg_EmptyScore . "\n";
            $message .= _Msg_MoreScore;
            self::$tg->sendMessage($chatId, $message);
        }
    }

    public function setUnfollowerHelper($chatId, $userId)
    {
        $follower = self::$ta->getFollowerIds($userId);
        $following = self::$ta->getFollowingIds($userId);
        $users = array_filter(str_replace($follower, "", $following));
        $userData = self::$db->getUserData($chatId);

        $message = null;

        $i = 1;
        foreach ($users as $value) {
            if ($i <= 20) {
                if (strpos($userData['_ignoreUnfollower'], "|" . $value . "|") === false) {
                    $name = null;
                    $userTwitterData = self::$db->getTwitterData("id", $value);
                    if (strlen(json_encode($userTwitterData)) > 4) {
                        // حساب توییتر در دیتابیس وجود داشت
                        $name = $userTwitterData['_screen_name'];
                        $age = $this->setTwitterAccountAge($userTwitterData['_signup']);
                        $this->setApiBdValue("_bd");
                    } else {
                        $twitterData = self::$ta->getUserData("id", $value);
                        $twitterData = json_decode($twitterData, true);
                        $name = $twitterData['screen_name'];
                        $timeStamp = $this->setSignUpTimestamp($twitterData['created_at']);
                        $age = $this->setTwitterAccountAge($timeStamp);
                        self::$db->insertTwitterData($twitterData['id'], "--------", $twitterData['screen_name'], $timeStamp);
                        $this->setApiBdValue("_api");
                    }

                    $message .= $i . ". " . _Msg_Username . " <a href='https://twitter.com/" . $name . "'>@" . $name . "</a>" . "\n";
                    $message .= _Msg_Age . ": " . number_format($age['d']) . " " . _Msg_Days . ", " . number_format($age['h']) . " " . _Msg_Hours . " " . _Msg_Ago . "\n";
                    $message .= _Msg_Ignore . ": /ignrflwr" . $value . "\n";
                    $message .= _Msg_Renew . ": /renew" . $value . "\n \n";
                    $i++;
                } else {
                    continue;
                }
            } else {
                break;
            }
        }
        if ($message != null) {
            return $message;
        } else {
            return _Msg_NoUnfollowered . "\n \n";
        }
    }

    public function setUnfollowerMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $data = self::$db->getUserTwitterAccounts($chatId);
        if (strlen(json_encode($data)) > 4) {
            // کاربر حساب فعال دارد
            $body = array();
            for ($i = 0; $i < sizeof($data); $i++) {
                $body[$i]['text'] = $data[$i]['name'];
                $body[$i]['callback_data'] = "UnfollowerFind-" . $data[$i]['id'];
            }
            $buttons = array('body' => $body, 'bodyVertical' => 1);
            self::$tg->sendInlineKeyboard($chatId, _Msg_SelectAccount, "text", null, $buttons);
        } else {
            self::$tg->sendMessage($chatId, _Msg_ConnectTwitterProfile);
        }
    }


    public function setShadowbanButtonMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $data = self::$db->getUserTwitterAccounts($chatId);
        if (strlen(json_encode($data)) > 4) {
            // کاربر حساب فعال دارد
            $body = array();
            for ($i = 0; $i < sizeof($data); $i++) {
                $body[$i]['text'] = $data[$i]['name'];
                $body[$i]['callback_data'] = "LimitShadow-" . $data[$i]['name'];
            }
            $buttons = array('body' => $body, 'bodyVertical' => 1);
            self::$tg->sendInlineKeyboard($chatId, _Msg_SelectAccount, "text", null, $buttons);
        } else {
            self::$tg->sendMessage($chatId, _Msg_ConnectTwitterProfile);
        }
    }

    public function setIgnoreUnfollowerMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $userData = self::$db->getUserData($chatId);
        $userId = str_replace("/ignrflwr", "", $text);
        if (strpos($userData['_ignoreUnfollower'], "|" . $userId . "|") !== false) {
            self::$tg->sendMessage($chatId, _Msg_RepeatPerson);
        } else {
            $this->appendIgnoredUnfollowerPerson($userData, $userId);
            $twitterData = self::$db->getTwitterData("id", $userId);
            $name = $twitterData['_screen_name'];
            $message = _Msg_IgnoreMessageOne . " <a href='https://twitter.com/" . $name . "'>@" . $name . "</a> " . _Msg_IgnoreMessageTwo . "\n";
            $message .= "- /rmvflwr" . $userId;
            self::$tg->sendMessage($chatId, $message);
        }
    }

    public function appendIgnoredUnfollowerPerson($userData, $userId)
    {
        $ignore = $userData['_ignoreUnfollower'] . $userId . "|";
        self::$db->setUserData($userData['_chatId'], "_ignoreUnfollower", $ignore);
    }

    public function setRemoveUnfollowerMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $userId = str_replace("/rmvflwr", "", $text);
        self::$db->removeIgnoredPerson($chatId, "_ignoreUnfollower", $userId);
        $twitterData = self::$db->getTwitterData("id", $userId);
        $name = $twitterData['_screen_name'];
        $message = _Msg_RemoveMessageOne . " <a href='https://twitter.com/" . $name . "'>@" . $name . "</a> " . _Msg_RemoveMessageTwo . "\n";
        $message .= "- /ignrflwr" . $userId;
        self::$tg->sendMessage($chatId, $message);
    }

    public function setWhiteListMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $message = _Msg_SelectIgnoredGroup;
        $body[0]['text'] = _Btn_Unfollowers;
        $body[0]['callback_data'] = "ignoredUnfollowerList";
        $buttons = array('body' => $body, 'bodyVertical' => 2);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setIgnoredPersonListMessage($chatId, $lan, $text, $switch)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        if ($switch == "follower") {
            $field = "_ignoreUnfollower";
            $remove = "/rmvflwr";
        } else {
            $field = "_ignoreUnfollowing";
            $remove = "/rmvflwn";
        }

        $userData = self::$db->getUserData($chatId);
        $users = explode("|", $userData[$field]);
        $message = null;
        for ($i = 1; $i < sizeof($users) - 1; $i++) {
            $userTwitterData = self::$db->getTwitterData("id", $users[$i]);
            $name = $userTwitterData['_screen_name'];
            $age = $this->setTwitterAccountAge($userTwitterData['_signup']);

            $message .= $i . ". " . _Msg_Username . " <a href='https://twitter.com/" . $name . "'>@" . $name . "</a>" . "\n";
            $message .= _Msg_Age . ": " . number_format($age['d']) . " " . _Msg_Days . ", " . number_format($age['h']) . " " . _Msg_Hours . " " . _Msg_Ago . "\n";
            $message .= "- " . $remove . $users[$i] . "\n \n";
        }

        if ($message == null) {
            $message = _Msg_NoWhiteList;
        }

        $body[0]['text'] = _Btn_ChackProfile;
        $body[0]['callback_data'] = "/whitelist";
        $buttons = array('body' => $body, 'bodyVertical' => 1);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setTweetVideoMessage($chatId, $lan, $tweetId, $condition)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, "Tweet link", __FUNCTION__);

        $userData = self::$db->getUserData($chatId);
        $userData['_timeVideo'] = $condition ? 0 : $userData['_timeVideo'];
        if (time() > $userData['_timeVideo']) {
            // اگر کاربر محدودیت زمانی نداشت
            $tweetData = self::$db->getTweetData($tweetId);
            if (strlen(json_encode($tweetData)) > 4) {
                // اگر توییت قبلا توسط کسی وارد شده بود
                $this->setApiBdValue("_bd");
                if ($tweetData['_type'] == "video") {
                    // اگر نوع توییت ویدیو بود
                    if (strpos($tweetData['_sender'], "|" . $chatId . "|") === false) {
                        // اگر شناسه کاربر در لیست فرستنده توییت وجود نداشت
                        $newSender = $tweetData['_sender'] . $chatId . "|";
                        self::$db->setTwitterData($tweetId, "_sender", $newSender);
                    }
                    $body[0]['text'] = _Btn_LowQuality;
                    $body[0]['callback_data'] = "getVideo-" . $tweetId . "-low";
                    $body[1]['text'] = _Btn_HighQuality;
                    $body[1]['callback_data'] = "getVideo-" . $tweetId . "-high";
                    $buttons = array('body' => $body, 'bodyVertical' => 2);
                    self::$tg->sendInlineKeyboard($chatId, _Msg_MovieQuality, "text", null, $buttons);
                } else {
                    self::$tg->sendMessage($chatId, _Msg_NotHaveVideo);
                }
            } else {
                $this->setApiBdValue("_api");
                $tweetData = AbrahamTwitter::getInstance()->getTweetData($tweetId);
                self::$tg->sendMessage(_MOHAMMAD, json_encode($tweetData));
                $creator = $tweetData->user->id;
                $type = "text";
                $fileIdOne = "--------";
                $fileIdTwo = "--------";
                $date = $this->setSignUpTimestamp($tweetData->created_at);
                if (isset($tweetData->extended_entities->media[0]->video_info->variants)) {
                    // اگر توییت ویدیو بود
                    self::$db->setUserData($chatId, "_timeVideo", time() + _timeVideo);
                    $links = $tweetData->extended_entities->media[0]->video_info->variants;

                    if ($this->getFileSize($links[0]->url) > 1024) {
                        $videoOneData = self::$tg->sendVideo(_VIDEO_CHANNEL, $links[0]->url);
                        $fileIdOne = json_decode($videoOneData, true)['result']['video']['file_id'];
                    }

                    if ($this->getFileSize($links[1]->url) > 1024) {
                        $videoTwoData = self::$tg->sendVideo(_VIDEO_CHANNEL, $links[1]->url);
                        $fileIdTwo = json_decode($videoTwoData, true)['result']['video']['file_id'];
                    }

                    $type = "video";
                    $body[0]['text'] = _Btn_LowQuality;
                    $body[0]['callback_data'] = "getVideo-" . $tweetId . "-low";
                    $body[1]['text'] = _Btn_HighQuality;
                    $body[1]['callback_data'] = "getVideo-" . $tweetId . "-high";
                    $buttons = array('body' => $body, 'bodyVertical' => 2);
                    self::$tg->sendInlineKeyboard($chatId, _Msg_MovieQuality, "text", null, $buttons);
                } else {
                    self::$tg->sendMessage($chatId, _Msg_NotHaveVideo);
                }

                self::$db->insertTweetData($tweetId, $creator, $chatId, $type, $fileIdOne, $fileIdTwo, $date);
            }
        } else {
            $time = $userData['_timeVideo'] - time();
            $min = ceil($time / 60);
            $message = _Msg_TimePermision . $min . " " . _Msg_Minutes;
            self::$tg->sendMessage($chatId, $message);
        }
    }

    public function sendVideoMessage($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId, "upload_video");
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);

        $data = explode("-", $text);
        $tweetData = self::$db->getTweetData($data[1]);
        $age = $age = $this->setTwitterAccountAge($tweetData['_date']);
        $caption = _Msg_PublishedIn . ": " . number_format($age['d']) . " " . _Msg_Days . ", " . number_format($age['h']) . " " . _Msg_Hours . " " . _Msg_Ago . "\n";
        $caption .= "- " . _Msg_Time . ": <code>" . date("H:i:s", $tweetData['_date']) . " UTC</code>\n";
        $caption .= "- " . _Msg_Date . ": <code>" . date("Y-m-d", $tweetData['_date']) . "</code>\n";
        switch ($data[2]) {
            case "low":
                if ($tweetData['_video_low'] != '--------') {
                    self::$tg->sendVideo($chatId, $tweetData['_video_low'], $caption);
                } else {
                    self::$tg->sendMessage($chatId, _Msg_DoNotHaveLowQuality);
                }
                break;

            case "high":
                if ($tweetData['_video_high'] != '--------') {
                    self::$tg->sendVideo($chatId, $tweetData['_video_high'], $caption);
                } else {
                    self::$tg->sendMessage($chatId, _Msg_DoNotHaveHighQuality);
                }
                break;
        }
    }

    public function setVideoListMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $videoData = self::$db->getVideoData($chatId);
        $message = _Msg_YourVideos . "\n \n";
        if (strlen(json_encode($videoData)) > 4) {
            for ($i = 0; $i < sizeof($videoData); $i++) {
                $message .= ($i + 1) . "." . _Msg_PublishedIn . ": <code>" . date('Y/m/d, H:i:s', $videoData[$i]['_date']) . " UTC</code>" . "\n";
                $message .= "- /videoget" . $videoData[$i]['_tweet_id'] . "\n \n";
            }
        } else {
            $message = _Msg_EmptyVideoList . "\n";
        }

        $body[0]['text'] = _Btn_ChackProfile;
        $body[0]['callback_data'] = "/videolist";
        $buttons = array('body' => $body, 'bodyVertical' => 1);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setStatisticsMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        //self::$tg->unpinChatMessage($chatId);
        $userData = self::$db->getUserData($chatId);
        $age = time() - _BOT_BIRTHDAY;
        $days = floor($age / 86400);
        $hours = floor(($age % 86400) / 3600);
        $data = self::$db->getUserStatus();
        $userCount = $data['All'];

        $i = 1;
        $message = "Bot Statistics:" . "\n";
        $message .= "¦" . $this->setTen($i++) . "¦ Bot Age: <code>" . number_format($days) . "</code> day(s), <code>" . $hours . "</code> hour(s) ago." . "\n";
        $message .= "¦" . $this->setTen($i++) . "¦ Bot Birthday: <code>" . date("Y-m-d, H:i:s", _BOT_BIRTHDAY) . " UTC</code>" . "\n";
        if ($userData['_dev']) {
            $userActive = $data['Active'];
            $activePercent = floor(($userActive * 100) / $userCount);
            $userSignup = $data['Signup'];
            $signupPercent = ceil(($userSignup * 100) / $userCount);
            $messageCount = self::$tg->getMessageId();
            $twitterUserCount = $data['Accounts'];
            $twitterVideo = $data['video'];

            $request = $data['Bd'] + $data['Api'] + $data['Insert'] + $data['Update'];
            $bd = $data['Bd'];
            $bdPercent = floor(($bd * 100) / $request);
            $api = $data['Api'];
            $apiPercent = ceil(($api * 100) / $request);
            $ins = $data['Insert'];
            $insPercent = ceil(($ins * 100) / $request);
            $upd = $data['Update'];
            $updPercent = ceil(($upd * 100) / $request);

            $message .= "¦" . $this->setTen($i++) . "¦ User Count: <code>" . number_format($userActive) . "/" . number_format($userCount) . " (" . $activePercent . "% Active)</code>" . "\n";
            $message .= "¦" . $this->setTen($i++) . "¦ User Signup: <code>" . number_format($userSignup) . "/" . number_format($userCount) . " (" . $signupPercent . "% Connect)</code>" . "\n";
            $message .= "¦" . $this->setTen($i++) . "¦ Message Count: <code>" . number_format($messageCount) . "</code>" . "\n";
            $message .= "¦" . $this->setTen($i++) . "¦ Twitter User Count: <code>" . number_format($twitterUserCount) . "</code>" . "\n";
            $message .= "¦" . $this->setTen($i++) . "¦ Twitter Video Count: <code>" . number_format($twitterVideo) . "</code>" . "\n";
            $message .= "¦" . $this->setTen($i++) . "¦ <code>" . "(" . $bdPercent . "%) " . number_format($bd) . "/" . number_format($request) . " </code> BigData use" . "\n";
            $message .= "¦" . $this->setTen($i++) . "¦ <code>" . "(" . $apiPercent . "%) " . number_format($api) . "/" . number_format($request) . " </code> API use" . "\n";
            $message .= "¦" . $this->setTen($i++) . "¦ <code>" . "(" . $insPercent . "%) " . number_format($ins) . "/" . number_format($request) . " </code> API insert" . "\n";
            $message .= "¦" . $this->setTen($i++) . "¦ <code>" . "(" . $updPercent . "%) " . number_format($upd) . "/" . number_format($request) . " </code> API update" . "\n";
        } else {
            $message .= "¦" . $this->setTen($i++) . "¦ User Count: <code>" . number_format($userCount) . "</code>" . "\n";
        }
        $message .= "¦" . $this->setTen($i++) . "¦ Log link: https://t.me/ZarchiProjects/10" . "\n";
        $message .= "¦" . $this->setTen($i) . "¦ Bot Version: <code>" . _BOT_VERSION . "</code>" . "\n";

        $body[0]['text'] = _Btn_ChackProfile;
        $body[0]['callback_data'] = "/statistics";
        $buttons = array('body' => $body, 'bodyVertical' => 1);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    public function setRenewMenu($chatId, $lan, $text)
    {
        self::$tg->setChatAction($chatId);
        $this->sendUserEntrance($chatId, $lan, $text, __FUNCTION__);
        $id = str_replace("/renew", "", $text);
        $twitterData = self::$ta->getUserData("id", $id);
        $data = json_decode($twitterData, true);
        $screenName = $data['screen_name'];
        $message = _Msg_Name . " <code>" . $data['name'] . "</code>" . "\n";
        $message .= _Msg_Username . " <a href='https://twitter.com/" . $screenName . "'>@" . $screenName . "</a>";
        self::$db->updateTwitterData($id, "_screen_name", $screenName);

        $body[0]['text'] = _Btn_ChackProfile;
        $body[0]['callback_data'] = "/renew" . $id;
        $buttons = array('body' => $body, 'bodyVertical' => 1);
        self::$tg->sendInlineKeyboard($chatId, $message, "text", null, $buttons);
    }

    /* * * * * * * * * * * * * * * * * * * * * * *
     * ╔═╗╔═╗  ╔╗╔╗             ╔╗╔╗      ╔╗      *
     * ║║╚╝║║ ╔╝╚╣║            ╔╝╚╣║      ║║      *
     * ║╔╗╔╗╠═╩╗╔╣╚═╦══╦═╗ ╔╗╔╦═╩╗╔╣╚═╦══╦═╝╠══╗  *
     * ║║║║║║╔╗║║║╔╗║║═╣╔╝ ║╚╝║║═╣║║╔╗║╔╗║╔╗║══╣  *
     * ║║║║║║╚╝║╚╣║║║║═╣║  ║║║║║═╣╚╣║║║╚╝║╚╝╠══║  *
     * ╚╝╚╝╚╩══╩═╩╝╚╩══╩╝  ╚╩╩╩══╩═╩╝╚╩══╩══╩══╝  *
     * * * * * * * * * * * * * * * * * * * * * * */

    private function scoreManager($chatId, $value)
    {
        $result = null;
        $userData = self::$db->getUserData($chatId);
        if ($userData['_dev']) {
            $result = _Msg_Score . ": ∞ ♘";
        } else {
            $score = $userData['_score'] - $value;
            self::$db->setUserData($chatId, "_score", $score);
            $result = _Msg_Score . ": " . $score . " ♘";
        }
        return $result;
    }

    public function setVerifiedNumber()
    {
        $data = explode(" ", microtime());
        $mic = str_replace("0.", "", number_format($data[0], 6));
        $nu = str_split(time() . $mic, 2);
        return $nu[4] . $nu[3] . $nu[2] . $nu[5] . $nu[6] . $nu[1] . $nu[0] . $nu[7];
    }

    private function setUserMessageBody($userData)
    {
        $message = "<code>" . _Msg_Id . " → " . $userData['id'] . "</code> \n";
        $message .= _Msg_Username . " <code>" . $userData['screen_name'] . "</code> \n";
        $message .= _Msg_Name . " <code>" . $userData['name'] . "</code> \n";
        $message .= _Msg_Bio . " <code>" . $userData['description'] . "</code> \n";
        $message .= _Msg_Link . " " . $userData['entities']['url']['urls'][0]['expanded_url'] . "\n";
        $message .= _Msg_Location . " <code>" . $userData['location'] . "</code> \n";
        $message .= _Msg_Following . " " . number_format($userData['friends_count']) . " \n";
        $message .= _Msg_Followers . " " . number_format($userData['followers_count']) . " \n";
        $message .= _Msg_Listed . " " . number_format($userData['listed_count']) . " \n";
        $message .= _Msg_Tweets . " " . number_format($userData['statuses_count']) . " \n";
        $message .= _Msg_Favourite . " " . number_format($userData['favourites_count']) . "\n";
        $message .= _Msg_Verified . " " . ($userData['verified'] == true ? _Msg_Yes : _Msg_No) . " \n";
        $message .= _Msg_Protected . " " . ($userData['protected'] == true ? _Msg_Yes : _Msg_No) . " \n";
        $timeStamp = $this->setSignUpTimestamp($userData['created_at']);
        $age = $this->setTwitterAccountAge($timeStamp);
        $message .= _Msg_SignUp . ": " . number_format($age['d']) . " " . _Msg_Days . ", " . number_format($age['h']) . " " . _Msg_Hours . " " . _Msg_Ago . "\n";
        $message .= "- " . _Msg_Time . ": <code>" . date('H:i:s', $timeStamp) . " UTC</code>" . "\n";
        $message .= "- " . _Msg_Date . ": <code>" . date('Y-m-d', $timeStamp) . "</code>\n";
        $message .= _Msg_AllTweet . " <a href='https://twitter.com/search?q=from:" . $userData['screen_name'] . "/exclude:replies'>Click to view</a>" . " \n";
        return $message;
    }

    private function setSignUpTimestamp($userDate)
    {
        $date = explode(" ", $userDate);
        return strtotime($date[3] . " " . $date[2] . " " . $date[1] . " " . $date[5]);
    }

    private function setTwitterAccountAge($timeStamp)
    {
        $result = array();
        $age = time() - $timeStamp;
        $result['d'] = floor($age / 86400);
        $result['h'] = floor(($age % 86400) / 3600);
        return $result;
    }

    public function setApiBdValue($field)
    {
        $data = self::$db->getBotData();
        $value = $data[$field] + 1;
        self::$db->setBotData($field, $value);
    }

    public function getFileSize($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_exec($curl);
        return curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    }

    public function setTen($i)
    {
        return ($i < 10 ? "0" . $i : $i);
    }
}
