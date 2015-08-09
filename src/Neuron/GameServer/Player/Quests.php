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

class Neuron_GameServer_Player_Quests
{
	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	/*
		Quests
	*/
	public function addQuest (Neuron_GameServer_Quest $quest)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		// Check if this is the only one
		$chk = $db->query
		("
			SELECT
				plid
			FROM
				n_players_quests
			WHERE
				plid = {$this->objProfile->getId ()} AND
				q_id = {$quest->getId ()}
		");
		
		if (count ($chk) == 0)
		{
			$db->query
			("
				INSERT INTO
					n_players_quests
				SET
					plid = {$this->objProfile->getId ()},
					q_id = {$quest->getId ()}
			");
		}
		
		$quest->onStart ($this->objProfile);
	}
	
	public function getPendingQuests ()
	{
		return $this->getQuests (true);
	}
	
	public function getFinishedQuests ()
	{
		return $this->getQuests (false);
	}
	
	public function getQuests ($pending = true)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$data = $db->query
		("
			SELECT
				*
			FROM
				n_players_quests
			WHERE
				plid = {$this->objProfile->getId ()} AND q_finished = '".($pending ? 0 : 1)."'
		");
		
		$out = array ();
		foreach ($data as $v)
		{
			$out[] = Neuron_GameServer_Quest::getFromId ($v['q_id']);
		}
		return $out;
	}
	
	public function removeQuests ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$db->query
		("
			DELETE FROM
				n_players_quests
			WHERE
				plid = {$this->objProfile->getId ()}
		");
	}
	
	public function evaluate ()
	{
		$db = Neuron_DB_Database::getInstance ();
	
		foreach ($this->getPendingQuests () as $v)
		{
			if ($v->isFinished ($this->objProfile))
			{
				$v->onComplete ($this->objProfile);
			
				$db->query
				("
					UPDATE
						n_players_quests
					SET
						q_finished = '1'
					WHERE
						q_id = {$v->getId ()} AND
						plid = {$this->objProfile->getId ()}
				");
			}
		}
	}
	
	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
