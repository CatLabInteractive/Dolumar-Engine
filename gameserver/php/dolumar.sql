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
  `attackType` enum('attack') collate utf8_unicode_ci NOT NULL default 'attack',
  `isFought` tinyint(1) NOT NULL default '0',
  `bLogId` int(11) NOT NULL,
  PRIMARY KEY  (`battleId`),
  KEY `vid` (`vid`),
  KEY `targetId` (`targetId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `squads` text collate utf8_unicode_ci NOT NULL,
  `slots` text collate utf8_unicode_ci NOT NULL,
  `fightLog` text collate utf8_unicode_ci NOT NULL,
  `battleLog` text collate utf8_unicode_ci NOT NULL,
  `resultLog` text collate utf8_unicode_ci NOT NULL,
  `victory` float NOT NULL default '0',
  `execDate` datetime NOT NULL,
  `specialUnits` text collate utf8_unicode_ci,
  PRIMARY KEY  (`reportId`),
  KEY `battleId` (`battleId`),
  KEY `fromId` (`fromId`),
  KEY `targetId` (`targetId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `battle_specialunits`
--

CREATE TABLE IF NOT EXISTS `battle_specialunits` (
  `bsu_id` int(11) NOT NULL auto_increment,
  `bsu_bid` int(11) NOT NULL,
  `bsu_vsu_id` int(11) NOT NULL,
  `bsu_ba_id` varchar(10) collate utf8_unicode_ci NOT NULL,
  `bsu_vid` int(11) NOT NULL,
  PRIMARY KEY  (`bsu_id`),
  KEY `bsu_bid` (`bsu_bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `bonus_buildings`
--

CREATE TABLE IF NOT EXISTS `bonus_buildings` (
  `b_id` int(11) NOT NULL,
  `b_player_tile` int(11) NOT NULL,
  PRIMARY KEY  (`b_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `boosts`
--

CREATE TABLE IF NOT EXISTS `boosts` (
  `b_id` int(11) NOT NULL auto_increment,
  `b_targetId` int(11) NOT NULL,
  `b_fromId` int(11) NOT NULL,
  `b_type` enum('spell') collate utf8_unicode_ci NOT NULL,
  `b_ba_id` varchar(10) collate utf8_unicode_ci NOT NULL,
  `b_start` int(11) NOT NULL,
  `b_end` int(11) NOT NULL,
  `b_secret` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`b_id`),
  KEY `b_targetId` (`b_targetId`),
  KEY `b_fromId` (`b_fromId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `effects`
--

CREATE TABLE IF NOT EXISTS `effects` (
  `e_id` int(11) NOT NULL auto_increment,
  `e_name` varchar(40) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`e_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `equipment`
--

CREATE TABLE IF NOT EXISTS `equipment` (
  `e_id` int(11) NOT NULL auto_increment,
  `e_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`e_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `game_logables`
--

CREATE TABLE IF NOT EXISTS `game_logables` (
  `l_id` int(11) NOT NULL auto_increment,
  `l_name` varchar(50) character set latin1 NOT NULL,
  PRIMARY KEY  (`l_id`),
  UNIQUE KEY `l_name` (`l_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `game_log_scouts`
--

CREATE TABLE IF NOT EXISTS `game_log_scouts` (
  `ls_id` int(11) NOT NULL auto_increment,
  `ls_runes` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`ls_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `game_log_training`
--

CREATE TABLE IF NOT EXISTS `game_log_training` (
  `lt_id` int(11) NOT NULL auto_increment,
  `u_id` int(11) NOT NULL,
  `lt_amount` int(11) NOT NULL,
  PRIMARY KEY  (`lt_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `usedResources` text collate utf8_unicode_ci NOT NULL,
  `destroyDate` int(11) NOT NULL default '0',
  `bLevel` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`bid`),
  KEY `xas` (`xas`,`yas`),
  KEY `village` (`village`),
  KEY `buildingType` (`buildingType`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `players_tiles`
--

CREATE TABLE IF NOT EXISTS `players_tiles` (
  `t_id` int(11) NOT NULL auto_increment,
  `t_userid` int(11) NOT NULL,
  `t_imagename` varchar(50) NOT NULL,
  PRIMARY KEY  (`t_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------


--
-- Tabel structuur voor tabel `specialUnits`
--

CREATE TABLE IF NOT EXISTS `specialUnits` (
  `s_id` int(11) NOT NULL auto_increment,
  `s_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`s_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `specialUnits_effects`
--

CREATE TABLE IF NOT EXISTS `specialUnits_effects` (
  `s_id` int(11) NOT NULL auto_increment,
  `b_id` int(11) NOT NULL,
  `e_id` varchar(10) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`s_id`),
  KEY `b_id` (`b_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `squad_commands`
--

CREATE TABLE IF NOT EXISTS `squad_commands` (
  `sc_id` int(11) NOT NULL auto_increment,
  `s_id` int(11) NOT NULL,
  `s_action` enum('move') character set latin1 NOT NULL,
  `s_start` datetime NOT NULL,
  `s_end` datetime NOT NULL,
  `s_from` int(11) default NULL,
  `s_to` int(11) default NULL,
  PRIMARY KEY  (`sc_id`),
  KEY `s_id` (`s_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `squad_equipment`
--

CREATE TABLE IF NOT EXISTS `squad_equipment` (
  `se_id` int(11) NOT NULL auto_increment,
  `s_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `e_id` int(11) NOT NULL,
  `v_id` int(11) NOT NULL,
  `i_itid` int(11) NOT NULL,
  PRIMARY KEY  (`se_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `technology`
--

CREATE TABLE IF NOT EXISTS `technology` (
  `techId` int(11) NOT NULL auto_increment,
  `techName` varchar(25) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`techId`),
  UNIQUE KEY `techName` (`techName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `unitId` int(11) NOT NULL auto_increment,
  `unitName` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`unitId`),
  UNIQUE KEY `unitName` (`unitName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages`
--

CREATE TABLE IF NOT EXISTS `villages` (
  `vid` int(11) NOT NULL auto_increment,
  `isActive` enum('1','0') collate utf8_unicode_ci NOT NULL,
  `isDestroyed` tinyint(4) NOT NULL default '0',
  `plid` int(11) NOT NULL default '0',
  `race` tinyint(4) NOT NULL default '0',
  `vname` varchar(30) collate utf8_unicode_ci NOT NULL default '',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_blevel`
--

CREATE TABLE IF NOT EXISTS `villages_blevel` (
  `vid` int(11) NOT NULL default '0',
  `bid` int(11) NOT NULL default '0',
  `lvl` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`vid`,`bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_counters`
--

CREATE TABLE IF NOT EXISTS `villages_counters` (
  `c_id` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL,
  `c_start` int(11) NOT NULL,
  `c_end` int(11) NOT NULL,
  `c_text` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`c_id`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_items`
--

CREATE TABLE IF NOT EXISTS `villages_items` (
  `i_id` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL,
  `i_itemId` int(11) NOT NULL,
  `i_amount` int(11) NOT NULL,
  `i_startCraft` int(11) NOT NULL,
  `i_endCraft` int(11) NOT NULL,
  `i_removed` int(11) NOT NULL,
  `i_buildingId` int(11) NOT NULL,
  `i_bid` int(11) NOT NULL,
  PRIMARY KEY  (`i_id`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_runes`
--

CREATE TABLE IF NOT EXISTS `villages_runes` (
  `vid` int(11) NOT NULL default '0',
  `runeId` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `amount` int(11) NOT NULL default '0',
  `usedRunes` int(11) NOT NULL default '0',
  PRIMARY KEY  (`vid`,`runeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_scouting`
--

CREATE TABLE IF NOT EXISTS `villages_scouting` (
  `scoutId` int(11) NOT NULL auto_increment,
  `vid` int(11) NOT NULL default '0',
  `finishDate` int(11) NOT NULL default '0',
  `runes` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`scoutId`),
  UNIQUE KEY `vid` (`vid`,`finishDate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_slots`
--

CREATE TABLE IF NOT EXISTS `villages_slots` (
  `vs_vid` int(11) NOT NULL,
  `vs_slot` tinyint(4) NOT NULL,
  `vs_slotId` int(11) NOT NULL,
  PRIMARY KEY  (`vs_vid`,`vs_slot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `villages_squads`
--

CREATE TABLE IF NOT EXISTS `villages_squads` (
  `s_id` int(11) NOT NULL auto_increment,
  `v_id` int(11) NOT NULL,
  `v_type` int(11) NOT NULL,
  `s_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `s_village` int(11) NOT NULL,
  PRIMARY KEY  (`s_id`),
  KEY `v_id` (`v_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `z_cache_tiles`
--

CREATE TABLE IF NOT EXISTS `z_cache_tiles` (
  `t_ix` int(11) NOT NULL,
  `t_iy` int(11) NOT NULL,
  `t_tile` tinyint(4) NOT NULL,
  `t_random` tinyint(4) NOT NULL,
  `t_height` decimal(5,4) NOT NULL,
  `t_distance` float default NULL,
  PRIMARY KEY  (`t_ix`,`t_iy`),
  KEY `t_iy` (`t_iy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

