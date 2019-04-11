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

class Neuron_GameServer_Player_Social
{
	private $iSocialStatuses = null;
	
	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	private function loadSocialStatuses ()
	{
		if (!isset ($this->iSocialStatuses))
		{
			$db = Neuron_DB_Database::getInstance ();
			$data = $db->query
			("
				SELECT
					ps_targetid,
					ps_status
				FROM
					n_players_social
				WHERE
					ps_plid = {$this->objProfile->getId()}
			");
			
			$this->iSocialStatuses = array ();
			foreach ($data as $v)
			{
				$this->iSocialStatuses[$v['ps_targetid']] = $v['ps_status'];
			}
		}
	}

	public function setSocialStatus (Neuron_GameServer_Player $player, $status)
	{		
		$db = Neuron_DB_Database::getInstance ();
		
		$status = intval ($status);
		
		// Check if already in here.
		$chk = $db->query
		("
			SELECT
				ps_status
			FROM
				n_players_social
			WHERE
				ps_plid = {$this->objProfile->getId()} 
				AND ps_targetid = {$player->getId ()}
		");
		
		if (count ($chk) == 0)
		{
			$db->query
			("
				INSERT INTO
					n_players_social
				SET
					ps_plid = {$this->objProfile->getId()},
					ps_targetid = {$player->getId ()},
					ps_status = '{$status}'
			");
		}
		else
		{
			$db->query
			("
				UPDATE
					n_players_social
				SET
					ps_status = '{$status}'
				WHERE
					ps_targetid = {$player->getId ()} AND
					ps_plid = {$this->objProfile->getId()}
			");
		}
	}

	public function getSocialStatus (Neuron_GameServer_Player $objUser)
	{
		if ($objUser instanceof Neuron_GameServer_Player)
		{
			$objUser = $objUser->getId ();
		}
		
		$objUser = intval ($objUser);
	
		$this->loadSocialStatuses ();
		
		if (isset ($this->iSocialStatuses[$objUser]))
		{
			return $this->iSocialStatuses[$objUser];
		}
		else
		{
			return false;
		}
	}

	public function getSocialStatuses ($iStatus)
	{
		$this->loadSocialStatuses ();
		
		$out = array ();
		foreach ($this->iSocialStatuses as $k => $v)
		{
			if ($v == $iStatus)
			{
				$out[] = Neuron_GameServer::getPlayer ($k);
			}
		}
		
		return $out;
	}
	
	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
