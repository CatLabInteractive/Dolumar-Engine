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

class Neuron_GameServer_Player_Preferences
{
	private $sPreferences = array ();
	
	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	/*
		Preferences
	*/
	private function loadPreferences ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$data = $db->query
		("
			SELECT
				*
			FROM
				n_players_preferences
			WHERE
				p_plid = {$this->objProfile->getId()}
		");
		
		foreach ($data as $v)
		{
			$this->sPreferences[$v['p_key']] = $v['p_value'];
		}
	}

	public function setPreference ($sKey, $sValue)
	{
		$db = Neuron_DB_Database::getInstance ();
	
		// Check if exist
		$check = $db->query
		("
			SELECT
				*
			FROM
				n_players_preferences
			WHERE
				p_key = '{$db->escape($sKey)}'
				AND p_plid = {$this->objProfile->getId()}
		");
		
		if (count ($check) == 0)
		{
			$db->query
			("
				INSERT INTO
					n_players_preferences
				SET
					p_key = '{$db->escape($sKey)}',
					p_value = '{$db->escape($sValue)}',
					p_plid = {$this->objProfile->getId ()}
			");
		}
		else
		{
			$db->query
			("
				UPDATE
					n_players_preferences
				SET
					p_value = '{$db->escape($sValue)}'
				WHERE
					p_key = '{$db->escape($sKey)}' AND
					p_plid = {$this->objProfile->getId ()}
			");
		}
	}
	
	public function getPreference ($sKey, $default = false)
	{
		$this->loadPreferences ();
		if (isset ($this->sPreferences[$sKey]))
		{
			return $this->sPreferences[$sKey];
		}
		else
		{
			return $default;
		}
	}
	
	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
