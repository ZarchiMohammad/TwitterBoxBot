CREATE TABLE `_bot_data` (
  `_id` int(11) NOT NULL,
  `_name` char(64) NOT NULL,
  `_bd` int(11) NOT NULL,
  `_api` int(11) NOT NULL,
  `_insert` int(11) NOT NULL,
  `_update` int(11) NOT NULL
);

CREATE TABLE `_tweet_data` (
  `_id` int(11) NOT NULL,
  `_tweet_id` char(128) NOT NULL,
  `_creator` char(64) NOT NULL,
  `_sender` longtext NOT NULL,
  `_type` char(16) NOT NULL,
  `_video_low` char(255) NOT NULL,
  `_video_high` char(255) NOT NULL,
  `_date` char(32) NOT NULL
);

CREATE TABLE `_user_data` (
  `_id` int(11) NOT NULL,
  `_sign_date` char(64) NOT NULL,
  `_chatId` bigint(20) NOT NULL,
  `_invited` char(64) NOT NULL,
  `_lan` char(3) NOT NULL,
  `_dev` tinyint(1) NOT NULL,
  `_active` int(11) NOT NULL,
  `_score` int(11) NOT NULL,
  `_verified` char(24) NOT NULL,
  `_timeDirect` char(32) NOT NULL DEFAULT '0',
  `_timeProfile` char(32) NOT NULL DEFAULT '0',
  `_timeShadowban` char(32) NOT NULL DEFAULT '0',
  `_timeUnfollower` char(32) NOT NULL DEFAULT '0',
  `_timeUnfollowing` char(32) NOT NULL,
  `_timeVideo` char(32) NOT NULL,
  `_ignoreUnfollower` text NOT NULL,
  `_ignoreUnfollowing` text NOT NULL
);

CREATE TABLE `_user_entrances` (
  `_id` int(11) NOT NULL,
  `_time` bigint(11) NOT NULL,
  `_chatId` bigint(11) NOT NULL,
  `_lan` char(4) NOT NULL,
  `_entrace` char(255) NOT NULL
);

CREATE TABLE `_user_twitter` (
  `_id` int(11) NOT NULL,
  `_twitter_id` char(128) NOT NULL,
  `_telegram_id` char(128) NOT NULL,
  `_screen_name` char(64) NOT NULL,
  `_signup` char(24) NOT NULL,
  `_show` int(1) NOT NULL
);

ALTER TABLE `_bot_data`
  ADD PRIMARY KEY (`_id`);

ALTER TABLE `_tweet_data`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `_tweet_id` (`_tweet_id`);

ALTER TABLE `_user_data`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `_chatId` (`_chatId`);

ALTER TABLE `_user_entrances`
  ADD PRIMARY KEY (`_id`);

ALTER TABLE `_user_twitter`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `_twitter_id` (`_twitter_id`);

ALTER TABLE `_bot_data`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `_tweet_data`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `_user_data`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `_user_entrances`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `_user_twitter`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
