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

class Neuron_GameServer_Player_Guide
{
	public static function addPublicMessage ($template, $data, $character = 'guide', $mood = 'neutral', $highlight = '')
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$data = Neuron_GameServer_LogSerializer::encode ($data);
		
		$players = $db->query
		("
			SELECT
				plid
			FROM
				n_players 
			WHERE
				removalDate IS NULL
		");

		foreach ($players as $v)
		{
			$db->query
			("
				INSERT INTO
					n_players_guide
				SET
					plid = {$v['plid']},
					pg_template = '{$db->escape ($template)}',
					pg_character = '{$db->escape ($character)}',
					pg_mood = '{$db->escape ($mood)}',
					pg_data = '{$db->escape ($data)}',
					pg_highlight = '{$db->escape ($highlight)}'
			");
		}
	}

	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	/*
		Quests
	*/
	public function addMessage ($template, $data, $character = 'guide', $mood = 'neutral', $highlight = '')
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$data = Neuron_GameServer_LogSerializer::encode ($data);
		
		$db->query
		("
			INSERT INTO
				n_players_guide
			SET
				plid = {$this->objProfile->getId ()},
				pg_template = '{$db->escape ($template)}',
				pg_character = '{$db->escape ($character)}',
				pg_mood = '{$db->escape ($mood)}',
				pg_data = '{$db->escape ($data)}',
				pg_highlight = '{$db->escape ($highlight)}'
		");
	}
	
	public function setAllRead ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$db->query
		("
			UPDATE
				n_players_guide
			SET
				pg_read = '1'
			WHERE
				plid = {$this->objProfile->getId ()}
		");
	}
	
	public function removeMessages ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$db->query
		("
			DELETE FROM
				n_players_guide
			WHERE
				plid = {$this->objProfile->getId ()}
		");
	}

	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
