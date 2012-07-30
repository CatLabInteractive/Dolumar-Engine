-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 03 Dec 2009 om 19:21
-- Server versie: 5.0.51
-- PHP Versie: 5.2.6-1+lenny4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dolumar`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `auth_openid`
--

CREATE TABLE IF NOT EXISTS `auth_openid` (
  `openid_url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notify_url` text NOT NULL,
  `profilebox_url` text NOT NULL,
  `userstats_url` text NOT NULL,
  PRIMARY KEY  (`openid_url`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `battle`
--

CREATE TABLE IF NOT EXISTS `battle` (
  `battleId` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL default '0',
  `targetId` int(11) NOT NULL default '0',
  `startDate` int(11) NOT NULL default '0',
  `arriveDate` int(11) NOT NULL,
  `fightDate` int(11) NOT NULL default '0',
  `endFightDate` int(11) NOT NULL,
  `endDate` int(11) NOT NULL default '0',
  `goHomeDuration` int(11) NOT NULL,
  `attackType` enum('attack') character set utf8 collate utf8_general_ci NOT NULL default 'attack',
  `isFought` tinyint(1) NOT NULL default '0',
  `bLogId` int(11) NOT NULL,
  `iHonourLose` int(11) default NULL,
  PRIMARY KEY  (`battleId`),
  KEY `vid` (`vid`),
  KEY `targetId` (`targetId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `battle_report`
--

CREATE TABLE IF NOT EXISTS `battle_report` (
  `reportId` int(11) NOT NULL auto_increment,
  `battleId` int(11) NOT NULL default '0',
  `fightDate` int(11) NOT NULL default '0',
  `fightDuration` int(11) NOT NULL,
  `fromId` int(11) NOT NULL default '0',
  `targetId` int(11) NOT NULL default '0',
  `squads` text character set utf8 collate utf8_general_ci NOT NULL,
  `slots` text character set utf8 collate utf8_general_ci NOT NULL,
  `fightLog` text character set utf8 collate utf8_general_ci NOT NULL,
  `battleLog` text character set utf8 collate utf8_general_ci NOT NULL,
  `resultLog` text character set utf8 collate utf8_general_ci NOT NULL,
  `victory` float NOT NULL default '0',
  `execDate` datetime NOT NULL,
  `specialUnits` text character set utf8 collate utf8_general_ci,
  PRIMARY KEY  (`reportId`),
  KEY `battleId` (`battleId`),
  KEY `fromId` (`fromId`),
  KEY `targetId` (`targetId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `battle_specialunits`
--

CREATE TABLE IF NOT EXISTS `battle_specialunits` (
  `bsu_id` int(11) NOT NULL auto_increment,
  `bsu_bid` int(11) NOT NULL,
  `bsu_vsu_id` int(11) NOT NULL,
  `bsu_ba_id` varchar(10) character set utf8 collate utf8_general_ci NOT NULL,
  `bsu_vid` int(11) NOT NULL,
  PRIMARY KEY  (`bsu_id`),
  KEY `bsu_bid` (`bsu_bid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `battle_squads`
--

CREATE TABLE IF NOT EXISTS `battle_squads` (
  `bs_id` int(11) NOT NULL auto_increment,
  `bs_bid` int(11) NOT NULL,
  `bs_squadId` int(11) NOT NULL,
  `bs_unitId` int(11) NOT NULL,
  `bs_vid` int(11) NOT NULL,
  `bs_slot` tinyint(4) NOT NULL,
  PRIMARY KEY  (`bs_id`),
  UNIQUE KEY `bs_bid` (`bs_bid`,`bs_squadId`,`bs_unitId`),
  KEY `bs_bid_2` (`bs_bid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `bonus_buildings`
--

CREATE TABLE IF NOT EXISTS `bonus_buildings` (
  `b_id` int(11) NOT NULL,
  `b_player_tile` int(11) NOT NULL,
  PRIMARY KEY  (`b_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `boosts`
--

CREATE TABLE IF NOT EXISTS `boosts` (
  `b_id` int(11) NOT NULL auto_increment,
  `b_targetId` int(11) NOT NULL,
  `b_fromId` int(11) NOT NULL,
  `b_type` enum('spell') character set utf8 collate utf8_general_ci NOT NULL,
  `b_ba_id` varchar(10) character set utf8 collate utf8_general_ci NOT NULL,
  `b_start` int(11) NOT NULL,
  `b_end` int(11) NOT NULL,
  `b_secret` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`b_id`),
  KEY `b_targetId` (`b_targetId`),
  KEY `b_fromId` (`b_fromId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `msgId` int(11) NOT NULL auto_increment,
  `msg` varchar(160) character set utf8 collate utf8_general_ci NOT NULL default '',
  `datum` int(11) NOT NULL default '0',
  `plid` int(11) NOT NULL default '0',
  `target_group` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `mtype` tinyint(4) NOT NULL,
  PRIMARY KEY  (`msgId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `chat_channels`
--

CREATE TABLE IF NOT EXISTS `chat_channels` (
  `cc_id` int(11) NOT NULL auto_increment,
  `cc_name` varchar(40) NOT NULL,
  PRIMARY KEY  (`cc_id`),
  UNIQUE KEY `cc_name` (`cc_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `clans`
--

CREATE TABLE IF NOT EXISTS `clans` (
  `c_id` int(11) NOT NULL auto_increment,
  `c_name` varchar(20) character set utf8 collate utf8_general_ci NOT NULL,
  `c_description` text character set utf8 collate utf8_general_ci NOT NULL,
  `c_password` varchar(32) character set utf8 collate utf8_general_ci default NULL,
  `c_score` int(11) NOT NULL,
  `c_isFull` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`c_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `clan_members`
--

CREATE TABLE IF NOT EXISTS `clan_members` (
  `cm_id` int(11) NOT NULL auto_increment,
  `plid` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `c_status` enum('member','captain','leader') character set utf8 collate utf8_general_ci NOT NULL,
  `cm_active` enum('1','0') character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`cm_id`),
  KEY `plid` (`plid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `effects`
--

CREATE TABLE IF NOT EXISTS `effects` (
  `e_id` int(11) NOT NULL auto_increment,
  `e_name` varchar(40) character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`e_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `effect_report`
--

CREATE TABLE IF NOT EXISTS `effect_report` (
  `er_id` int(11) NOT NULL auto_increment,
  `er_vid` int(11) NOT NULL,
  `er_target_v_id` int(11) default NULL,
  `er_type` varchar(20) NOT NULL,
  `er_date` datetime NOT NULL,
  `er_data` text NOT NULL,
  PRIMARY KEY  (`er_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `equipment`
--

CREATE TABLE IF NOT EXISTS `equipment` (
  `e_id` int(11) NOT NULL auto_increment,
  `e_name` varchar(20) character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`e_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_bans`
--

CREATE TABLE IF NOT EXISTS `forum_bans` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `user` tinytext character set utf8 collate utf8_general_ci NOT NULL,
  `forumID` tinytext character set utf8 collate utf8_general_ci NOT NULL,
  `time` int(11) NOT NULL,
  `reason` tinytext character set utf8 collate utf8_general_ci NOT NULL,
  `by` smallint(6) NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_boards`
--

CREATE TABLE IF NOT EXISTS `forum_boards` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `forum_id` tinytext character set utf8 collate utf8_general_ci NOT NULL,
  `order` tinyint(4) NOT NULL,
  `title` text character set utf8 collate utf8_general_ci NOT NULL,
  `desc` text character set utf8 collate utf8_general_ci NOT NULL,
  `private` tinyint(1) NOT NULL,
  `guestable` tinyint(1) NOT NULL default '1',
  `last_post` mediumint(9) NOT NULL,
  `last_topic_id` smallint(6) NOT NULL,
  `last_topic_title` text character set utf8 collate utf8_general_ci NOT NULL,
  `last_post_id` mediumint(9) NOT NULL,
  `last_poster` smallint(6) NOT NULL,
  `post_count` smallint(6) NOT NULL,
  `topic_count` smallint(6) NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_forums`
--

CREATE TABLE IF NOT EXISTS `forum_forums` (
  `type` mediumint(9) NOT NULL,
  `ID` mediumint(9) NOT NULL,
  `banned` text collate utf8_general_ci NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_modlog`
--

CREATE TABLE IF NOT EXISTS `forum_modlog` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `mod_user_id` smallint(6) NOT NULL,
  `timestamp` mediumint(9) NOT NULL,
  `desc` text character set utf8 collate utf8_general_ci NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_posts`
--

CREATE TABLE IF NOT EXISTS `forum_posts` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `forum_id` tinytext character set utf8 collate utf8_general_ci NOT NULL,
  `topic_id` mediumint(9) NOT NULL,
  `board_id` mediumint(9) NOT NULL,
  `number` smallint(6) NOT NULL,
  `poster_id` mediumint(9) NOT NULL,
  `created` int(11) NOT NULL,
  `edited_time` int(11) NOT NULL,
  `edits` tinyint(4) NOT NULL,
  `edit_by` mediumint(9) NOT NULL,
  `post_content` text character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_topics`
--

CREATE TABLE IF NOT EXISTS `forum_topics` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `forum_id` tinytext character set utf8 collate utf8_general_ci NOT NULL,
  `board_id` mediumint(9) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `lastpost` int(11) NOT NULL,
  `lastposter` mediumint(9) NOT NULL,
  `title` text character set utf8 collate utf8_general_ci NOT NULL,
  `postcount` smallint(6) NOT NULL,
  `type` tinyint(4) NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `game_log`
--

CREATE TABLE IF NOT EXISTS `game_log` (
  `l_id` int(11) NOT NULL auto_increment,
  `l_vid` int(11) NOT NULL,
  `l_action` varchar(20) character set utf8 collate utf8_general_ci NOT NULL,
  `l_subId` int(11) NOT NULL,
  `l_date` datetime NOT NULL,
  `l_data` varchar(250) character set utf8 collate utf8_general_ci NOT NULL,
  `l_notification` tinyint(1) NOT NULL,
  PRIMARY KEY  (`l_id`),
  KEY `l_vid` (`l_vid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `game_logables`
--

CREATE TABLE IF NOT EXISTS `game_logables` (
  `l_id` int(11) NOT NULL auto_increment,
  `l_name` varchar(50) NOT NULL,
  PRIMARY KEY  (`l_id`),
  UNIQUE KEY `l_name` (`l_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `game_log_scouts`
--

CREATE TABLE IF NOT EXISTS `game_log_scouts` (
  `ls_id` int(11) NOT NULL auto_increment,
  `ls_runes` varchar(50) character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`ls_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `game_log_training`
--

CREATE TABLE IF NOT EXISTS `game_log_training` (
  `lt_id` int(11) NOT NULL auto_increment,
  `u_id` int(11) NOT NULL,
  `lt_amount` int(11) NOT NULL,
  PRIMARY KEY  (`lt_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `locks`
--

CREATE TABLE IF NOT EXISTS `locks` (
  `l_id` bigint(20) NOT NULL auto_increment,
  `l_type` varchar(30) character set utf8 collate utf8_general_ci NOT NULL,
  `l_lid` int(11) NOT NULL,
  `l_date` int(11) NOT NULL,
  PRIMARY KEY  (`l_id`),
  KEY `l_type` (`l_type`,`l_lid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `login_log`
--

CREATE TABLE IF NOT EXISTS `login_log` (
  `l_id` int(11) NOT NULL auto_increment,
  `l_plid` int(11) default NULL,
  `l_ip` varchar(20) character set utf8 collate utf8_general_ci NOT NULL,
  `l_datetime` datetime NOT NULL,
  PRIMARY KEY  (`l_id`),
  KEY `l_plid` (`l_plid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `map_buildings`
--

CREATE TABLE IF NOT EXISTS `map_buildings` (
  `bid` int(11) NOT NULL auto_increment,
  `xas` float NOT NULL default '0',
  `yas` float NOT NULL default '0',
  `sizeX` float NOT NULL default '0',
  `sizeY` float NOT NULL default '0',
  `buildingType` int(11) NOT NULL default '0',
  `village` int(11) NOT NULL default '0',
  `startDate` int(11) NOT NULL default '0',
  `readyDate` int(11) NOT NULL default '0',
  `lastUpgradeDate` int(11) NOT NULL default '0',
  `usedResources` text character set utf8 collate utf8_general_ci NOT NULL,
  `destroyDate` int(11) NOT NULL default '0',
  `bLevel` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`bid`),
  KEY `xas` (`xas`,`yas`),
  KEY `village` (`village`),
  KEY `buildingType` (`buildingType`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `map_portals`
--

CREATE TABLE IF NOT EXISTS `map_portals` (
  `p_id` int(11) NOT NULL auto_increment,
  `p_caster_v_id` int(11) NOT NULL,
  `p_target_v_id` int(11) NOT NULL,
  `p_caster_x` int(11) NOT NULL,
  `p_caster_y` int(11) NOT NULL,
  `p_target_x` int(11) NOT NULL,
  `p_target_y` int(11) NOT NULL,
  `p_caster_b_id` int(11) NOT NULL,
  `p_target_b_id` int(11) NOT NULL,
  `p_endDate` datetime NOT NULL,
  PRIMARY KEY  (`p_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `m_id` int(11) NOT NULL auto_increment,
  `m_from` int(11) NOT NULL,
  `m_target` int(11) NOT NULL,
  `m_subject` varchar(100) character set utf8 collate utf8_general_ci NOT NULL,
  `m_text` text character set utf8 collate utf8_general_ci NOT NULL,
  `m_isRead` tinyint(1) NOT NULL default '0',
  `m_date` datetime NOT NULL,
  `m_removed_sender` tinyint(1) NOT NULL default '0',
  `m_removed_target` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`m_id`),
  KEY `m_from` (`m_from`),
  KEY `m_target` (`m_target`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `mod_actions`
--

CREATE TABLE IF NOT EXISTS `mod_actions` (
  `ma_id` int(11) NOT NULL auto_increment,
  `ma_action` varchar(20) NOT NULL,
  `ma_data` text NOT NULL,
  `ma_plid` int(11) NOT NULL,
  `ma_date` datetime NOT NULL,
  `ma_reason` text NOT NULL,
  `ma_processed` tinyint(1) NOT NULL default '0',
  `ma_executed` tinyint(1) default NULL,
  PRIMARY KEY  (`ma_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `plid` int(11) NOT NULL auto_increment,
  `nickname` varchar(20) character set utf8 collate utf8_general_ci default NULL,
  `email` varchar(255) character set utf8 collate utf8_general_ci default NULL,
  `email_cert` tinyint(4) NOT NULL default '0',
  `email_cert_key` varchar(32) character set utf8 collate utf8_general_ci default NULL,
  `password1` varchar(32) character set utf8 collate utf8_general_ci default NULL,
  `password2` varchar(32) character set utf8 collate utf8_general_ci default NULL,
  `activated` tinyint(1) NOT NULL default '1',
  `buildingClick` tinyint(4) NOT NULL default '0',
  `minimapPosition` tinyint(4) NOT NULL default '0',
  `creationDate` datetime default NULL,
  `removalDate` datetime default NULL,
  `lastRefresh` datetime default NULL,
  `isRemoved` tinyint(1) NOT NULL default '0',
  `isKillVillages` tinyint(1) NOT NULL default '0',
  `isPlaying` tinyint(1) NOT NULL default '0',
  `startX` int(11) default NULL,
  `startY` int(11) default NULL,
  `isPremium` tinyint(1) NOT NULL default '0',
  `premiumEndDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `sponsorEndDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `showSponsor` tinyint(1) NOT NULL default '0',
  `showAdvertisement` tinyint(4) NOT NULL default '0',
  `killCounter` tinyint(4) NOT NULL default '0',
  `tmp_key` varchar(32) character set utf8 collate utf8_general_ci default NULL,
  `tmp_key_end` datetime default NULL,
  `startVacation` datetime default NULL,
  `referee` varchar(20) character set utf8 collate utf8_general_ci NOT NULL,
  `p_referer` int(11) default NULL,
  `p_admin` tinyint(1) NOT NULL default '0',
  `p_lang` varchar(5) character set utf8 collate utf8_general_ci default NULL,
  `p_score` int(11) NOT NULL,
  PRIMARY KEY  (`plid`),
  KEY `nickname` (`nickname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_banned`
--

CREATE TABLE IF NOT EXISTS `players_banned` (
  `pb_id` int(11) NOT NULL auto_increment,
  `plid` int(11) NOT NULL,
  `bp_channel` varchar(20) character set utf8 collate utf8_general_ci NOT NULL,
  `bp_end` datetime NOT NULL,
  PRIMARY KEY  (`pb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_preferences`
--

CREATE TABLE IF NOT EXISTS `players_preferences` (
  `p_plid` int(11) NOT NULL,
  `p_key` varchar(15) NOT NULL,
  `p_value` text NOT NULL,
  PRIMARY KEY  (`p_plid`,`p_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_social`
--

CREATE TABLE IF NOT EXISTS `players_social` (
  `ps_plid` int(11) NOT NULL,
  `ps_targetid` int(11) NOT NULL,
  `ps_status` int(11) NOT NULL,
  PRIMARY KEY  (`ps_plid`,`ps_targetid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_tiles`
--

CREATE TABLE IF NOT EXISTS `players_tiles` (
  `t_id` int(11) NOT NULL auto_increment,
  `t_userid` int(11) NOT NULL,
  `t_imagename` varchar(50) NOT NULL,
  PRIMARY KEY  (`t_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `premium_queue`
--

CREATE TABLE IF NOT EXISTS `premium_queue` (
  `pq_id` int(11) NOT NULL auto_increment,
  `pq_vid` int(11) NOT NULL,
  `pq_action` varchar(10) NOT NULL,
  `pq_data` text NOT NULL,
  `pq_date` datetime NOT NULL,
  `pq_lastcheck` datetime NOT NULL,
  PRIMARY KEY  (`pq_id`),
  KEY `pq_vid` (`pq_vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `server_data`
--

CREATE TABLE IF NOT EXISTS `server_data` (
  `s_name` varchar(10) collate utf8_general_ci NOT NULL,
  `s_value` varchar(20) collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`s_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `specialUnits`
--

CREATE TABLE IF NOT EXISTS `specialUnits` (
  `s_id` int(11) NOT NULL auto_increment,
  `s_name` varchar(20) character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`s_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `specialUnits_effects`
--

CREATE TABLE IF NOT EXISTS `specialUnits_effects` (
  `s_id` int(11) NOT NULL auto_increment,
  `b_id` int(11) NOT NULL,
  `e_id` varchar(10) character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`s_id`),
  KEY `b_id` (`b_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `squad_commands`
--

CREATE TABLE IF NOT EXISTS `squad_commands` (
  `sc_id` int(11) NOT NULL auto_increment,
  `s_id` int(11) NOT NULL,
  `s_action` enum('move') NOT NULL,
  `s_start` datetime NOT NULL,
  `s_end` datetime NOT NULL,
  `s_from` int(11) default NULL,
  `s_to` int(11) default NULL,
  PRIMARY KEY  (`sc_id`),
  KEY `s_id` (`s_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `squad_equipment`
--

CREATE TABLE IF NOT EXISTS `squad_equipment` (
  `se_id` int(11) NOT NULL auto_increment,
  `s_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `e_id` varchar(10) character set utf8 collate utf8_general_ci NOT NULL,
  `v_id` int(11) NOT NULL,
  `i_itid` int(11) NOT NULL,
  PRIMARY KEY  (`se_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `squad_units`
--

CREATE TABLE IF NOT EXISTS `squad_units` (
  `su_id` int(11) NOT NULL auto_increment,
  `s_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `s_amount` int(11) NOT NULL,
  `v_id` int(11) NOT NULL,
  `s_slotId` tinyint(4) NOT NULL default '0',
  `s_priority` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`su_id`),
  UNIQUE KEY `s_id` (`s_id`,`u_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `technology`
--

CREATE TABLE IF NOT EXISTS `technology` (
  `techId` int(11) NOT NULL auto_increment,
  `techName` varchar(25) character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`techId`),
  UNIQUE KEY `techName` (`techName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `temp_passwords`
--

CREATE TABLE IF NOT EXISTS `temp_passwords` (
  `p_id` int(11) NOT NULL auto_increment,
  `p_plid` int(11) NOT NULL,
  `p_pass` varchar(8) character set utf8 collate utf8_general_ci NOT NULL,
  `p_expire` datetime NOT NULL,
  PRIMARY KEY  (`p_id`),
  KEY `p_plid` (`p_plid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `unitId` int(11) NOT NULL auto_increment,
  `unitName` varchar(20) character set utf8 collate utf8_general_ci NOT NULL default '',
  PRIMARY KEY  (`unitId`),
  UNIQUE KEY `unitName` (`unitName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages`
--

CREATE TABLE IF NOT EXISTS `villages` (
  `vid` int(11) NOT NULL auto_increment,
  `isActive` enum('1','0') character set utf8 collate utf8_general_ci NOT NULL,
  `isDestroyed` tinyint(4) NOT NULL default '0',
  `plid` int(11) NOT NULL default '0',
  `race` tinyint(4) NOT NULL default '0',
  `vname` varchar(30) character set utf8 collate utf8_general_ci NOT NULL default '',
  `gold` double NOT NULL default '250',
  `wood` double NOT NULL default '750',
  `stone` double NOT NULL default '750',
  `iron` double NOT NULL default '750',
  `grain` double NOT NULL default '750',
  `gems` double NOT NULL default '10',
  `lastResRefresh` int(11) NOT NULL default '0',
  `networth` int(11) NOT NULL default '0',
  `networth_date` int(11) NOT NULL default '0',
  `runeScoutsDone` int(11) NOT NULL default '0',
  `removalDate` datetime default NULL,
  PRIMARY KEY  (`vid`),
  KEY `plid` (`plid`),
  KEY `vname` (`vname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_blevel`
--

CREATE TABLE IF NOT EXISTS `villages_blevel` (
  `vid` int(11) NOT NULL default '0',
  `bid` int(11) NOT NULL default '0',
  `lvl` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`vid`,`bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_counters`
--

CREATE TABLE IF NOT EXISTS `villages_counters` (
  `c_id` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL,
  `c_start` int(11) NOT NULL,
  `c_end` int(11) NOT NULL,
  `c_text` varchar(100) character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`c_id`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_itemlevels`
--

CREATE TABLE IF NOT EXISTS `villages_itemlevels` (
  `v_id` int(11) NOT NULL,
  `e_id` int(11) NOT NULL,
  `vi_level` tinyint(4) NOT NULL,
  PRIMARY KEY  (`v_id`,`e_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_items`
--

CREATE TABLE IF NOT EXISTS `villages_items` (
  `i_id` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL,
  `i_itemId` varchar(10) character set utf8 collate utf8_general_ci NOT NULL,
  `i_amount` int(11) NOT NULL,
  `i_startCraft` int(11) NOT NULL,
  `i_endCraft` int(11) NOT NULL,
  `i_removed` int(11) NOT NULL,
  `i_buildingId` int(11) NOT NULL,
  `i_bid` int(11) NOT NULL,
  PRIMARY KEY  (`i_id`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_morale`
--

CREATE TABLE IF NOT EXISTS `villages_morale` (
  `m_id` int(11) NOT NULL auto_increment,
  `m_vid` int(11) NOT NULL,
  `m_amount` tinyint(4) NOT NULL,
  `m_start` datetime NOT NULL,
  `m_end` datetime NOT NULL,
  PRIMARY KEY  (`m_id`),
  KEY `m_vid` (`m_vid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_runes`
--

CREATE TABLE IF NOT EXISTS `villages_runes` (
  `vid` int(11) NOT NULL default '0',
  `runeId` varchar(10) collate utf8_general_ci NOT NULL default '',
  `amount` int(11) NOT NULL default '0',
  `usedRunes` int(11) NOT NULL default '0',
  PRIMARY KEY  (`vid`,`runeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_scouting`
--

CREATE TABLE IF NOT EXISTS `villages_scouting` (
  `scoutId` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL default '0',
  `finishDate` int(11) NOT NULL default '0',
  `runes` text character set utf8 collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`scoutId`),
  UNIQUE KEY `vid` (`vid`,`finishDate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_slots`
--

CREATE TABLE IF NOT EXISTS `villages_slots` (
  `vs_vid` int(11) NOT NULL,
  `vs_slot` tinyint(4) NOT NULL,
  `vs_slotId` int(11) NOT NULL,
  PRIMARY KEY  (`vs_vid`,`vs_slot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_specialunits`
--

CREATE TABLE IF NOT EXISTS `villages_specialunits` (
  `vsu_id` int(11) NOT NULL auto_increment,
  `v_id` int(11) NOT NULL,
  `vsu_bid` int(11) NOT NULL,
  `vsu_tStartDate` int(11) NOT NULL,
  `vsu_tEndDate` int(11) NOT NULL,
  `vsu_location` int(11) default NULL,
  `vsu_moveStart` datetime default NULL,
  `vsu_moveEnd` datetime default NULL,
  PRIMARY KEY  (`vsu_id`),
  KEY `v_id` (`v_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_squads`
--

CREATE TABLE IF NOT EXISTS `villages_squads` (
  `s_id` int(11) NOT NULL auto_increment,
  `v_id` int(11) NOT NULL,
  `v_type` int(11) NOT NULL,
  `s_name` varchar(20) character set utf8 collate utf8_general_ci NOT NULL,
  `s_village` int(11) NOT NULL,
  PRIMARY KEY  (`s_id`),
  KEY `v_id` (`v_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_tech`
--

CREATE TABLE IF NOT EXISTS `villages_tech` (
  `id` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL,
  `techId` tinyint(4) NOT NULL,
  `startDate` int(11) NOT NULL,
  `endDate` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_units`
--

CREATE TABLE IF NOT EXISTS `villages_units` (
  `uid` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL default '0',
  `unitId` int(11) NOT NULL default '0',
  `buildingId` int(11) NOT NULL default '0',
  `village` int(11) NOT NULL default '0',
  `amount` int(11) NOT NULL default '0',
  `startTraining` int(11) NOT NULL default '0',
  `endTraining` int(11) NOT NULL default '0',
  `killedAmount` int(11) NOT NULL default '0',
  `bid` int(11) NOT NULL,
  PRIMARY KEY  (`uid`),
  KEY `vid` (`vid`,`village`),
  KEY `village` (`village`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_visits`
--

CREATE TABLE IF NOT EXISTS `villages_visits` (
  `vi_id` int(11) NOT NULL auto_increment,
  `v_id` int(11) NOT NULL,
  `vi_v_id` int(11) NOT NULL,
  `vi_date` datetime NOT NULL,
  PRIMARY KEY  (`vi_id`),
  KEY `v_id` (`v_id`,`vi_v_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
