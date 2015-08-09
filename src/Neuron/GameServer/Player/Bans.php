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

class Neuron_GameServer_Player_Bans
{
	private $bans = null;

	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	private function loadBans ()
	{
		if (!isset ($this->bans))
		{
			$this->bans = array ();
			
			$db = Neuron_DB_Database::getInstance ();
			
			$chk = $db->query
			("
				SELECT
					bp_channel,
					UNIX_TIMESTAMP(bp_end) AS datum
				FROM
					n_players_banned
				WHERE
					plid = {$this->objProfile->getId ()}
			");
			
			foreach ($chk as $v)
			{
				$this->bans[$v['bp_channel']] = $v['datum'];
				
				if ($v['datum'] < time ())
				{
					$this->unban ($v['bp_channel']);
				}
			}
		}
	}

	public function isBanned ($sChannel = 'chat')
	{
		$this->loadBans ();
		return isset ($this->bans[$sChannel]) ? true : false;
	}
	
	public function getBanDuration ($sChannel)
	{
		$this->loadBans ();
		return isset ($this->bans[$sChannel]) ? $this->bans[$sChannel] : false;
	}
	
	public function ban ($sChannel = 'chat', $duration = 3600, $ban = true)
	{
		$db = Neuron_DB_Database::getInstance ();

		$db->query
		("
			DELETE FROM
				n_players_banned
			WHERE
				plid = {$this->objProfile->getId()} AND
				bp_channel = '{$db->escape ($sChannel)}'
		");
		
		$this->bans = null;
		
		// First unban
		if ($ban)
		{
			$db->query
			("
				INSERT INTO
					n_players_banned
				SET
					plid = {$this->objProfile->getId ()},
					bp_channel = '{$db->escape ($sChannel)}',
					bp_end = FROM_UNIXTIME(".(time() + $duration).")
			");
		}
	}
	
	public function unban ($sChannel = 'chat')
	{
		$this->ban ($sChannel, null, false);
	}

	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
