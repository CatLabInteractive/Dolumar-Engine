<?php
/**
 *  Dolumar engine, php/html MMORTS engine
 *  Copyright (C) 2009 Thijs Van der Schaeghe
 *  CatLab Interactive bvba, Gent, Belgium
 *  http://www.catlab.eu/
 *  http://www.dolumar.com/
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

class Neuron_GameServer_Server
{
	public static function __getInstance ()
	{
		static $in;
		if (!isset ($in))
		{
			$in = new self ();
		}
		return $in;
	}
	
	public static function getInstance ()
	{
		return self::__getInstance ();
	}

	public static function getOnlineUser ($start = 0, $limit = 250)
	{
		//die ($start . ' - '. $limit);
	
		$start = (int)$start;

		$db = Neuron_Core_Database::__getInstance ();

		$l = $db->select
		(
			'n_players',
			array ('*'),
			"lastRefresh > '".Neuron_Core_Tools::timestampToMysqlDatetime ((time () - ONLINE_TIMEOUT))."' AND isPlaying = '1'",
			'nickname ASC',
			$start . ", " . $limit
		);

		$o = array ();
		foreach ($l as $v)
		{
			$o[] = Neuron_GameServer::getPlayer ($v['plid'], $v);
		}
		return $o;
	}

	public static function countOnlineUsers ($timeout = null)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		if ($timeout == null)
		{
			$timeout = ONLINE_TIMEOUT;
		}

		$l = $db->getDataFromQuery
		($db->customQuery(
		"
			SELECT
				COUNT(plid) as aantal
			FROM
				n_players
			WHERE
				lastRefresh > '".Neuron_Core_Tools::timestampToMysqlDatetime ((time () - $timeout))."'
				AND isPlaying = '1'
		"));

		return $l[0]['aantal'];
	}
	
	public static function countPremiumPlayers ($timeout = null)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		if ($timeout == null)
		{
			$timeout = ONLINE_TIMEOUT;
		}

		$l = $db->getDataFromQuery
		($db->customQuery(
		"
			SELECT
				COUNT(plid) as aantal
			FROM
				n_players
			WHERE
				lastRefresh > '".Neuron_Core_Tools::timestampToMysqlDatetime ((time () - $timeout))."'
				AND isPlaying = '1'
				AND UNIX_TIMESTAMP(creationDate) < ".time()."
				AND UNIX_TIMESTAMP(premiumEndDate) > ".time()."
		"));

		return $l[0]['aantal'];
	}
	
	public static function countSponsors ()
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$l = $db->getDataFromQuery
		($db->customQuery(
		"
			SELECT
				COUNT(plid) as aantal
			FROM
				n_players
			WHERE
				sponsorEndDate > '".Neuron_Core_Tools::timestampToMysqlDatetime (time ())."'
		"));

		return $l[0]['aantal'];
	}

	public static function countTotalPlayers ()
	{
		$db = Neuron_Core_Database::__getInstance ();

		$l = $db->getDataFromQuery
		($db->customQuery(
		"
			SELECT
				COUNT(plid) as aantal
			FROM
				n_players
		"));

		return $l[0]['aantal'];
	}
	
	public static function countPlayersFromAuth ($auth)
	{
		$db = Neuron_Core_Database::__getInstance ();

		if (!empty ($auth))
		{
			$where = "authType = '".$db->escape ($auth)."'";
		}
		else
		{
			$where = "authType IS NULL";
		}

		$l = $db->getDataFromQuery
		($db->customQuery(
		"
			SELECT
				COUNT(plid) as aantal
			FROM
				n_players
			WHERE
				$where
		"));

		return $l[0]['aantal'];
	}

	public static function searchPlayer ($sName, $iStart = 0, $iLength = 10, $literal = false)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		if ($literal)
		{
			$where = "nickname = '".$db->escape ($sName)."'";
		}
		else
		{
			$where = "nickname LIKE '".$db->escape ($sName)."'";
		}

		$l = $db->getDataFromQuery
		($db->customQuery(
		"
			SELECT
				*
			FROM
				n_players
			WHERE
				$where AND
				isRemoved = '0'
			LIMIT
				$iStart, $iLength
		"));

		$o = array ();
		foreach ($l as $v)
		{
			$o[] = Neuron_GameServer::getPlayer ($v['plid'], $v);
		}
		return $o;
	}
	
	public function getDonationUrl ($amount = null, $currency = 'EUR')
	{
		$login = Neuron_Core_Login::__getInstance ();
		$text = Neuron_Core_Text::__getInstance ();
		
		$url = DONATION_URL . "?amount=".$amount."&currency=".$currency."&language=".$text->getCurrentLanguage ();
		$url .= "&callback=".urlencode (API_FULL_URL."?action=processPayment&key=".API_MASTER_KEY);
		
		if ($login->isLogin ())
		{
			return $url . "&userid=".urlencode ($login->getUserId ());
		}
		else
		{
			return $url;
		}
	}
	
	public static function countAuthTypes ()
	{
		$out = array ();
		
		$db = Neuron_Core_Database::__getInstance ();
		
		// Select the different auth types
		$auths = $db->getDataFromQuery ($db->customQuery 
		("
			SELECT
				authType
			FROM
				n_players
			GROUP BY
				authType
		"));
		
		foreach ($auths as $v)
		{
			if (empty ($v['authType']))
			{
				$out['dolumar'] = self::countPlayersFromAuth ($v['authType']);
			}
			else
			{
				$out[$v['authType']] = self::countPlayersFromAuth ($v['authType']);
			}
		}
		
		return $out;
	}

	private static function getFilesToKeep ()
	{
		return array ('openid');
	}

	public static function removeDirRecursive ($dir, $emptyOnly = false)
	{
		$scanDir = scandir ($dir);
		$filesToKeep = self::getFilesToKeep ();

		foreach ($scanDir as $file)
		{
			if ($file != '.' && $file != '..' && array_search ($file, $filesToKeep) === false)
			{
				if (is_dir ($dir . '/' . $file))
				{
					self::removeDirRecursive ($dir . '/' . $file, true);
					rmdir ($dir . '/' . $file);
				}
				else
				{
					unlink ($dir . '/' . $file);
				}
			}
		}

		if (!$emptyOnly)
		{
			if (is_dir ($dir))
			{
				rmdir ($dir);
			}
			else
			{
				unlink ($dir);
			}
		}
	}

	public static function emptyDir ($dir)
	{
		self::removeDirRecursive ($dir, true);
	}

	public static function clearCache ($clearMapCache = true)
	{
		self::emptyDir (CACHE_DIR);
		/*
		self::emptyDir ('cache/minimap');
		self::emptyDir ('cache/mediawiki');
		self::emptyDir ('cache/hugemap');
		self::emptyDir ('cache/heightmap');
		*/
		
		//self::emptyDir (CACHE_DIR . 'openid');
		
		if ($clearMapCache)
		{
			$db = Neuron_Core_Database::__getInstance ();
			//$db->customQuery (" TRUNCATE TABLE `z_cache_tiles`  ");
		
			$server = self::__getInstance ();
			$server->setData ('prepRadD', 0);
		}
		
		$cache = Neuron_Core_Memcache::getInstance ();
		$cache->flush ();
	}
	
	public static function countInvitations ()
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$l = $db->select
		(
			'invitation_codes',
			array ('invCode', 'invLeft'),
			"i_infinite = '1'"
		);
		
		$o = array ();
		foreach ($l as $v)
		{
			$o[$v['invCode']] = $v['invLeft'];
		}
		return $o;
	}
	
	public static function getPremiumcallKey ()
	{
		return md5 (md5 (PREMIUM_API_KEY.date ('dDWY').PREMIUM_API_KEY));
	}
	
	public static function cleanServer ()
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$log = array ();
		
		// Kill players
		$last = Neuron_Core_Tools::timestampToMysqlDatetime (time () - 60*60*24*45);
		
		$l = $db->select
		(
			'n_players',
			array ('plid', 'killCounter'),
			"lastRefresh < '".$last."' AND isPlaying = 1"
		);
		
		foreach ($l as $v)
		{
			if ($v['killCounter'] < 5)
			{
				$db->update
				(
					'n_players',
					array
					(
						'killCounter' => '++'
					),
					"plid = '".$v['plid']."'"
				);
			}
			
			else
			{
				// Kill!
				$player = Neuron_GameServer::getPlayer ($v['plid']);
				
				if (!$player->isModerator ())
				{
					$player->doResetAccount ();
					
					/*
					$log[] = array
					(
						'action' => 'reset',
						'subject' => 'account',
						'name' => $player->getNickname ()
					);
					*/
					
					$player->__destruct ();
				}
			}
		}
		
		// Kill villages
		$last = Neuron_Core_Tools::timestampToMysqlDatetime (time () - 60*60*24*5);
		//$last = Neuron_Core_Tools::timestampToMysqlDatetime (time ());
		
		$l = $db->select
		(
			'villages',
			array ('vid'),
			"isActive = '0' AND (removalDate < '$last' OR removalDate IS NULL)"
		);
		
		foreach ($l as $v)
		{
			$village = Dolumar_Players_Village::getVillage ($v['vid'], false, true, true);
			$village->destroyVillage ();
			
			$village->__destruct ();
		}
		
		// Remove old login data (2 weeks old)
		$rem = $db->remove ('n_login_log', "l_datetime < '".date ('Y-m-d H:i:s', time () - 60*60*24*7*2)."'");
		$log[] = "Removed ".$rem." login logs.\n";
		
		// Remove old game log data (2 months old)
		//$db->remove ('game_log', "l_date < '".date ('Y-m-d H:i:s', time () - 60*60*31*2)."'");
		
		// Remove old battle reports (2 months old)
		$rem = $db->remove ('battle_report', "fightDate < '". (time () - 60*60*24*31*2)."'");
		$log[] = "Removed ".$rem." battle reports.\n";
		
		return $log;
	}
	
	private $data = null;
	private $error = null;
	
	public function __construct ()
	{
		
	}
	
	private function loadData ()
	{
		if ($this->data === null)
		{
			$this->data = array ();
		
			$db = Neuron_Core_Database::__getInstance ();
			
			$l = $db->select
			(
				'n_server_data',
				array ('*')
			);
			
			foreach ($l as $v)
			{
				$this->data[$v['s_name']] = $v['s_value'];
			}
		}
	}
	
	/*
		Return true if the server is online
	*/
	public function isOnline ()
	{
		$this->loadData ();
		$timecheck = isset ($this->data['lastDaily']) && intval ($this->data['lastDaily']) > (time () - 60*60*24*7);
		
		if (!$this->isInstalled ())
		{
			$this->error = 'not_installed';
			return false;
		}		
		elseif (!$timecheck)
		{
			$this->error = 'daily_cron_failed';
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function getServerId ()
	{
		$this->loadData ();
		return isset ($this->data['serverid']) ? $this->data['serverid'] : false;
	}
	
	public function getServerName ()
	{
		$this->loadData ();
		return isset ($this->data['servername']) ? $this->data['servername'] : null;
	}
	
	public function isInstalled ()
	{
		$this->loadData ();
		return isset ($this->data['servername']) && isset ($this->data['serverid']);
	}
	
	public function setServerName ($id, $sName)
	{
		$db = Neuron_Core_Database::__getInstance ();
		$db->insert
		(
			'n_server_data',
			array
			(
				's_name' => 'servername',
				's_value' => $sName
			)
		);
		
		$db->insert
		(
			'n_server_data',
			array
			(
				's_name' => 'serverid',
				's_value' => $id
			)
		);
	}
	
	public function updateServerName ($sName)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$db->update
		(
			'n_server_data',
			array
			(
				's_value' => $sName
			),
			"s_name = 'servername'"
		);
	}
	
	public function setData ($sKey, $sData)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		// Check if key exists
		$chk = $db->query
		("
			SELECT
				s_value
			FROM
				n_server_data
			WHERE
				s_name = '{$db->escape ($sKey)}'
		");
		
		if (count ($chk) > 0)
		{
			$db->query
			("
				UPDATE
					n_server_data
				SET
					s_value = '{$db->escape ($sData)}'
				WHERE
					s_name = '{$db->escape ($sKey)}'
			");
		}
		else
		{
			$db->query
			("
				INSERT INTO
					n_server_data
				SET
					s_value = '{$db->escape ($sData)}',
					s_name = '{$db->escape ($sKey)}'
			");
		}
		
		$this->data[$sKey] = $sData;
	}
	
	public function getData ($sKey)
	{
		$this->loadData ();
		return isset ($this->data[$sKey]) ? $this->data[$sKey] : null;
	}
	
	public function setLastDaily ($now = NOW)
	{
		$this->loadData ();
		
		$db = Neuron_Core_Database::__getInstance ();
		if (isset ($this->data['lastDaily']))
		{
			$db->update
			(
				'n_server_data',
				array ('s_value' => $now),
				"s_name = 'lastDaily'"
			);
		}
		else
		{
			$db->insert
			(
				'n_server_data',
				array
				(	
					's_name' => 'lastDaily',
					's_value' => $now
				)
			);
		}
	}
	
	private $textdata;
	
	private function loadTextData ($lang)
	{
		$lang = $this->getTextKey ($lang);
	
		if (!isset ($this->textdata))
		{
			$this->textdata = array ();
			
			if (!isset ($this->textdata[$lang]))
			{
				$this->textdata[$lang] = array ();
				
				$db = Neuron_DB_Database::getInstance ();
				
				$data = $db->query
				("
					SELECT
						*
					FROM
						n_server_text
					WHERE
						s_lang = '{$db->escape ($lang)}'
				");
				
				foreach ($data as $v)
				{
					$this->textdata[$lang][$v['s_id']] = $v['s_value'];
				}
			}
		}
	}
	
	private function getTextKey ($key)
	{
		$text = Neuron_Core_Text::getInstance ();
		
		if (!isset ($key))
		{
			return $text->getCurrentLanguage ();
		}
		
		return $key;
	}
	
	public function getText ($key, $lang = null)
	{
		$lang = $this->getTextKey ($lang);
	
		$this->loadTextData ($lang);
		return isset ($this->textdata[$lang][$key]) ? $this->textdata[$lang][$key] : null;
	}
	
	public function getError ()
	{
		return $this->error;
	}
}
?>
