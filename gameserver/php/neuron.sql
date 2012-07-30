-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 28 Aug 2009 om 17:09
-- Server versie: 5.0.51
-- PHP Versie: 5.2.6-1+lenny3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dolumar`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `clans`
--

CREATE TABLE IF NOT EXISTS `clans` (
  `c_id` int(11) NOT NULL auto_increment,
  `c_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `c_description` text collate utf8_unicode_ci NOT NULL,
  `c_password` varchar(32) collate utf8_unicode_ci default NULL,
  `c_score` int(11) NOT NULL,
  PRIMARY KEY  (`c_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `clan_members`
--

CREATE TABLE IF NOT EXISTS `clan_members` (
  `cm_id` int(11) NOT NULL auto_increment,
  `plid` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `c_status` enum('member','captain','leader') collate utf8_unicode_ci NOT NULL,
  `cm_active` enum('1','0') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`cm_id`),
  KEY `plid` (`plid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `auth_openid`
--

CREATE TABLE IF NOT EXISTS `auth_openid` (
  `openid_url` varchar(1000) character set latin1 collate latin1_bin NOT NULL,
  `user_id` int(11) NOT NULL,
  `notify_url` text NOT NULL,
  `profilebox_url` text NOT NULL,
  `userstats_url` text NOT NULL,
  PRIMARY KEY  (`openid_url`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `msgId` int(11) NOT NULL auto_increment,
  `msg` varchar(160) collate utf8_unicode_ci NOT NULL default '',
  `datum` int(11) NOT NULL default '0',
  `plid` int(11) NOT NULL default '0',
  `target_group` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `mtype` tinyint(4) NOT NULL,
  PRIMARY KEY  (`msgId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `chat_channels`
--

CREATE TABLE IF NOT EXISTS `chat_channels` (
  `cc_id` int(11) NOT NULL auto_increment,
  `cc_name` varchar(40) NOT NULL,
  PRIMARY KEY  (`cc_id`),
  UNIQUE KEY `cc_name` (`cc_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_bans`
--

CREATE TABLE IF NOT EXISTS `forum_bans` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `user` tinytext collate utf8_unicode_ci NOT NULL,
  `forumID` tinytext collate utf8_unicode_ci NOT NULL,
  `time` int(11) NOT NULL,
  `reason` tinytext collate utf8_unicode_ci NOT NULL,
  `by` smallint(6) NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_boards`
--

CREATE TABLE IF NOT EXISTS `forum_boards` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `forum_id` tinytext collate utf8_unicode_ci NOT NULL,
  `order` tinyint(4) NOT NULL,
  `title` text collate utf8_unicode_ci NOT NULL,
  `desc` text collate utf8_unicode_ci NOT NULL,
  `private` tinyint(1) NOT NULL,
  `guestable` tinyint(1) NOT NULL default '1',
  `last_post` mediumint(9) NOT NULL,
  `last_topic_id` smallint(6) NOT NULL,
  `last_topic_title` text collate utf8_unicode_ci NOT NULL,
  `last_post_id` mediumint(9) NOT NULL,
  `last_poster` smallint(6) NOT NULL,
  `post_count` smallint(6) NOT NULL,
  `topic_count` smallint(6) NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_forums`
--

CREATE TABLE IF NOT EXISTS `forum_forums` (
  `type` mediumint(9) NOT NULL,
  `ID` mediumint(9) NOT NULL,
  `banned` text collate utf8_unicode_ci NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_modlog`
--

CREATE TABLE IF NOT EXISTS `forum_modlog` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `mod_user_id` smallint(6) NOT NULL,
  `timestamp` mediumint(9) NOT NULL,
  `desc` text collate utf8_unicode_ci NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_posts`
--

CREATE TABLE IF NOT EXISTS `forum_posts` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `forum_id` tinytext collate utf8_unicode_ci NOT NULL,
  `topic_id` mediumint(9) NOT NULL,
  `board_id` mediumint(9) NOT NULL,
  `number` smallint(6) NOT NULL,
  `poster_id` mediumint(9) NOT NULL,
  `created` int(11) NOT NULL,
  `edited_time` int(11) NOT NULL,
  `edits` tinyint(4) NOT NULL,
  `edit_by` mediumint(9) NOT NULL,
  `post_content` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `forum_topics`
--

CREATE TABLE IF NOT EXISTS `forum_topics` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `forum_id` tinytext collate utf8_unicode_ci NOT NULL,
  `board_id` mediumint(9) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `lastpost` int(11) NOT NULL,
  `lastposter` mediumint(9) NOT NULL,
  `title` text collate utf8_unicode_ci NOT NULL,
  `postcount` smallint(6) NOT NULL,
  `type` tinyint(4) NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `game_log`
--

CREATE TABLE IF NOT EXISTS `game_log` (
  `l_id` int(11) NOT NULL auto_increment,
  `l_vid` int(11) NOT NULL,
  `l_action` varchar(20) collate utf8_unicode_ci NOT NULL,
  `l_subId` int(11) NOT NULL,
  `l_date` datetime NOT NULL,
  `l_data` varchar(250) collate utf8_unicode_ci NOT NULL,
  `l_notification` tinyint(1) NOT NULL,
  PRIMARY KEY  (`l_id`),
  KEY `l_vid` (`l_vid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `locks`
--

CREATE TABLE IF NOT EXISTS `locks` (
  `l_id` bigint(20) NOT NULL auto_increment,
  `l_type` varchar(30) collate utf8_unicode_ci NOT NULL,
  `l_lid` int(11) NOT NULL,
  `l_date` int(11) NOT NULL,
  PRIMARY KEY  (`l_id`),
  KEY `l_type` (`l_type`,`l_lid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `login_log`
--

CREATE TABLE IF NOT EXISTS `login_log` (
  `l_id` int(11) NOT NULL auto_increment,
  `l_plid` int(11) default NULL,
  `l_ip` bigint(20) NOT NULL,
  `l_datetime` datetime NOT NULL,
  PRIMARY KEY  (`l_id`),
  KEY `l_plid` (`l_plid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `m_id` int(11) NOT NULL auto_increment,
  `m_from` int(11) NOT NULL,
  `m_target` int(11) NOT NULL,
  `m_subject` varchar(100) collate utf8_unicode_ci NOT NULL,
  `m_text` text collate utf8_unicode_ci NOT NULL,
  `m_isRead` tinyint(1) NOT NULL default '0',
  `m_date` datetime NOT NULL,
  `m_removed_sender` tinyint(1) NOT NULL default '0',
  `m_removed_target` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`m_id`),
  KEY `m_from` (`m_from`),
  KEY `m_target` (`m_target`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `plid` int(11) NOT NULL auto_increment,
  `nickname` varchar(20) collate utf8_unicode_ci default NULL,
  `email` varchar(255) collate utf8_unicode_ci default NULL,
  `email_cert` tinyint(4) NOT NULL default '0',
  `email_cert_key` varchar(32) collate utf8_unicode_ci default NULL,
  `password1` varchar(32) collate utf8_unicode_ci default NULL,
  `password2` varchar(32) collate utf8_unicode_ci default NULL,
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
  `tmp_key` varchar(32) collate utf8_unicode_ci default NULL,
  `tmp_key_end` datetime default NULL,
  `startVacation` datetime default NULL,
  `referee` varchar(20) collate utf8_unicode_ci NOT NULL,
  `p_referer` int(11) default NULL,
  `p_admin` tinyint(1) NOT NULL default '0',
  `p_lang` varchar(5) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`plid`),
  KEY `nickname` (`nickname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_banned`
--

CREATE TABLE IF NOT EXISTS `players_banned` (
  `pb_id` int(11) NOT NULL auto_increment,
  `plid` int(11) NOT NULL,
  `bp_channel` varchar(20) character set utf8 collate utf8_unicode_ci NOT NULL,
  `bp_end` datetime NOT NULL,
  PRIMARY KEY  (`pb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_preferences`
--

CREATE TABLE IF NOT EXISTS `players_preferences` (
  `p_plid` int(11) NOT NULL,
  `p_key` varchar(15) NOT NULL,
  `p_value` text NOT NULL,
  PRIMARY KEY  (`p_plid`,`p_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_social`
--

CREATE TABLE IF NOT EXISTS `players_social` (
  `ps_plid` int(11) NOT NULL,
  `ps_targetid` int(11) NOT NULL,
  `ps_status` int(11) NOT NULL,
  PRIMARY KEY  (`ps_plid`,`ps_targetid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `server_data`
--

CREATE TABLE IF NOT EXISTS `server_data` (
  `s_name` varchar(10) collate utf8_unicode_ci NOT NULL,
  `s_value` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`s_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `temp_passwords`
--

CREATE TABLE IF NOT EXISTS `temp_passwords` (
  `p_id` int(11) NOT NULL auto_increment,
  `p_plid` int(11) NOT NULL,
  `p_pass` varchar(8) collate utf8_unicode_ci NOT NULL,
  `p_expire` datetime NOT NULL,
  PRIMARY KEY  (`p_id`),
  KEY `p_plid` (`p_plid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

