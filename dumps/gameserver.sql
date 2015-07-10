-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 19 feb 2013 om 18:03
-- Serverversie: 5.5.28
-- PHP-Versie: 5.4.4-12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `dolumar`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_auth_openid`
--

CREATE TABLE IF NOT EXISTS `n_auth_openid` (
  `openid_url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notify_url` text NOT NULL,
  `profilebox_url` text NOT NULL,
  `userstats_url` text NOT NULL,
  PRIMARY KEY (`openid_url`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_chat_channels`
--

CREATE TABLE IF NOT EXISTS `n_chat_channels` (
  `c_c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_c_name` varchar(20) NOT NULL,
  PRIMARY KEY (`c_c_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_chat_messages`
--

CREATE TABLE IF NOT EXISTS `n_chat_messages` (
  `c_m_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `c_c_id` int(11) NOT NULL,
  `c_plid` int(11) NOT NULL,
  `c_date` datetime NOT NULL,
  `c_message` varchar(1000) NOT NULL,
  PRIMARY KEY (`c_m_id`),
  KEY `c_c_id` (`c_c_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_locks`
--

CREATE TABLE IF NOT EXISTS `n_locks` (
  `l_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `l_type` varchar(30) NOT NULL,
  `l_lid` int(11) NOT NULL,
  `l_date` int(11) NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `l_type` (`l_type`,`l_lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_logables`
--

CREATE TABLE IF NOT EXISTS `n_logables` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_name` varchar(50) NOT NULL,
  PRIMARY KEY (`l_id`),
  UNIQUE KEY `l_name` (`l_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_login_failures`
--

CREATE TABLE IF NOT EXISTS `n_login_failures` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_plid` int(11) DEFAULT NULL,
  `l_ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `l_username` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `l_date` datetime NOT NULL,
  PRIMARY KEY (`l_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_login_log`
--

CREATE TABLE IF NOT EXISTS `n_login_log` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_plid` int(11) DEFAULT NULL,
  `l_ip` varchar(20) NOT NULL,
  `l_datetime` datetime NOT NULL,
  PRIMARY KEY (`l_id`),
  KEY `l_plid` (`l_plid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_map_updates`
--

CREATE TABLE IF NOT EXISTS `n_map_updates` (
  `mu_id` int(11) NOT NULL AUTO_INCREMENT,
  `mu_action` enum('BUILD','DESTROY') NOT NULL,
  `mu_x` int(11) NOT NULL,
  `mu_y` int(11) NOT NULL,
  `mu_date` datetime NOT NULL,
  PRIMARY KEY (`mu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_mod_actions`
--

CREATE TABLE IF NOT EXISTS `n_mod_actions` (
  `ma_id` int(11) NOT NULL AUTO_INCREMENT,
  `ma_action` varchar(20) NOT NULL,
  `ma_data` text NOT NULL,
  `ma_plid` int(11) NOT NULL,
  `ma_date` datetime NOT NULL,
  `ma_reason` text NOT NULL,
  `ma_processed` tinyint(1) NOT NULL DEFAULT '0',
  `ma_executed` tinyint(1) DEFAULT NULL,
  `ma_target` int(11) NOT NULL,
  PRIMARY KEY (`ma_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_players`
--

CREATE TABLE IF NOT EXISTS `n_players` (
  `plid` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_cert` tinyint(4) NOT NULL DEFAULT '0',
  `email_cert_key` varchar(32) DEFAULT NULL,
  `password1` varchar(32) DEFAULT NULL,
  `password2` varchar(32) DEFAULT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `buildingClick` tinyint(4) NOT NULL DEFAULT '0',
  `minimapPosition` tinyint(4) NOT NULL DEFAULT '0',
  `creationDate` datetime DEFAULT NULL,
  `removalDate` datetime DEFAULT NULL,
  `lastRefresh` datetime DEFAULT NULL,
  `isRemoved` tinyint(1) NOT NULL DEFAULT '0',
  `isKillVillages` tinyint(1) NOT NULL DEFAULT '0',
  `isPlaying` tinyint(1) NOT NULL DEFAULT '0',
  `startX` int(11) DEFAULT NULL,
  `startY` int(11) DEFAULT NULL,
  `isPremium` tinyint(1) NOT NULL DEFAULT '0',
  `premiumEndDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sponsorEndDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `showSponsor` tinyint(1) NOT NULL DEFAULT '0',
  `showAdvertisement` tinyint(4) NOT NULL DEFAULT '0',
  `killCounter` tinyint(4) NOT NULL DEFAULT '0',
  `tmp_key` varchar(32) DEFAULT NULL,
  `tmp_key_end` datetime DEFAULT NULL,
  `startVacation` datetime DEFAULT NULL,
  `referee` varchar(20) NOT NULL,
  `p_referer` int(11) DEFAULT NULL,
  `p_admin` tinyint(1) NOT NULL DEFAULT '0',
  `p_lang` varchar(5) DEFAULT NULL,
  `p_score` int(11) NOT NULL,
  PRIMARY KEY (`plid`),
  KEY `nickname` (`nickname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_players_admin_cleared`
--

CREATE TABLE IF NOT EXISTS `n_players_admin_cleared` (
  `pac_id` int(11) NOT NULL AUTO_INCREMENT,
  `pac_plid1` int(11) NOT NULL,
  `pac_plid2` int(11) NOT NULL,
  `pac_reason` text NOT NULL,
  PRIMARY KEY (`pac_id`),
  KEY `pac_plid1` (`pac_plid1`,`pac_plid2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_players_banned`
--

CREATE TABLE IF NOT EXISTS `n_players_banned` (
  `pb_id` int(11) NOT NULL AUTO_INCREMENT,
  `plid` int(11) NOT NULL,
  `bp_channel` varchar(20) NOT NULL,
  `bp_end` datetime NOT NULL,
  PRIMARY KEY (`pb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_players_guide`
--

CREATE TABLE IF NOT EXISTS `n_players_guide` (
  `pg_id` int(11) NOT NULL AUTO_INCREMENT,
  `plid` int(11) NOT NULL,
  `pg_template` varchar(50) NOT NULL,
  `pg_character` varchar(20) NOT NULL,
  `pg_mood` varchar(20) NOT NULL,
  `pg_data` text NOT NULL,
  `pg_read` enum('0','1') NOT NULL,
  `pg_highlight` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pg_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_players_preferences`
--

CREATE TABLE IF NOT EXISTS `n_players_preferences` (
  `p_plid` int(11) NOT NULL,
  `p_key` varchar(15) NOT NULL,
  `p_value` text NOT NULL,
  PRIMARY KEY (`p_plid`,`p_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_players_quests`
--

CREATE TABLE IF NOT EXISTS `n_players_quests` (
  `pq_id` int(11) NOT NULL AUTO_INCREMENT,
  `plid` int(11) NOT NULL,
  `q_id` int(11) NOT NULL,
  `q_finished` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`pq_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_players_social`
--

CREATE TABLE IF NOT EXISTS `n_players_social` (
  `ps_plid` int(11) NOT NULL,
  `ps_targetid` int(11) NOT NULL,
  `ps_status` int(11) NOT NULL,
  PRIMARY KEY (`ps_plid`,`ps_targetid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_players_update`
--

CREATE TABLE IF NOT EXISTS `n_players_update` (
  `pu_id` int(11) NOT NULL AUTO_INCREMENT,
  `pu_plid` int(11) NOT NULL,
  `pu_key` varchar(20) NOT NULL,
  `pu_value` varchar(20) NOT NULL,
  PRIMARY KEY (`pu_id`),
  KEY `pu_plid` (`pu_plid`),
  KEY `pu_key` (`pu_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_privatechat_updates`
--

CREATE TABLE IF NOT EXISTS `n_privatechat_updates` (
  `pu_id` int(11) NOT NULL AUTO_INCREMENT,
  `pu_from` int(11) NOT NULL,
  `pu_to` int(11) NOT NULL,
  `c_m_id` int(11) NOT NULL,
  `pu_date` datetime NOT NULL,
  `pu_read` tinyint(4) NOT NULL,
  PRIMARY KEY (`pu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_quests`
--

CREATE TABLE IF NOT EXISTS `n_quests` (
  `q_id` int(11) NOT NULL AUTO_INCREMENT,
  `q_class` varchar(50) NOT NULL,
  PRIMARY KEY (`q_id`),
  UNIQUE KEY `q_class` (`q_class`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_server_data`
--

CREATE TABLE IF NOT EXISTS `n_server_data` (
  `s_name` varchar(10) NOT NULL,
  `s_value` varchar(20) NOT NULL,
  PRIMARY KEY (`s_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_server_text`
--

CREATE TABLE IF NOT EXISTS `n_server_text` (
  `s_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `s_lang` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `s_value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`s_id`,`s_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `n_temp_passwords`
--

CREATE TABLE IF NOT EXISTS `n_temp_passwords` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_plid` int(11) NOT NULL,
  `p_pass` varchar(8) NOT NULL,
  `p_expire` datetime NOT NULL,
  PRIMARY KEY (`p_id`),
  KEY `p_plid` (`p_plid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
