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

class Neuron_GameServer_Mappers_UpdateMapper
{
	/**
	* We need one instance per player.
	*/
	public static function getInstance ()
	{
		static $in;
		if (!isset ($in))
		{
			$in = new self ();
		}
		return $in;
	}

	protected function __construct ()
	{

	}

	public function getLastLogId (Neuron_GameServer_Player $player)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$data = $db->query
		("
			SELECT
				pu_id
			FROM
				n_players_update
			ORDER BY
				pu_id DESC
			LIMIT
				1
		");

		if (count ($data) > 0)
		{
			return $data[0]['pu_id'];
		}
		else
		{
			return 0;
		}
	}

	public function getUpdates (Neuron_GameServer_Player $player, $sinceLogId)
	{
		$db = Neuron_DB_Database::getInstance ();

		$sinceLogId = intval ($sinceLogId);

		$data = $db->query
		("
			SELECT
				*
			FROM
				n_players_update
			WHERE
				pu_plid = {$player->getId ()} AND
				pu_id > $sinceLogId
			GROUP BY
				pu_key
		");

		return $this->getFromReader ($data);
	}

	private function getFromReader ($data)
	{
		$out = array ();
		foreach ($data as $v)
		{
			$out[] = array
			(
				'id' => $v['pu_id'],
				'key' => $v['pu_key'],
				'value' => $v['pu_value']
			);
		}
		return $out;
	}

	public function addUpdate (Neuron_GameServer_Player $player, $key, $value)
	{
		$db = Neuron_DB_Database::getInstance ();

		$db->query
		("
			DELETE FROM
				n_players_update
			WHERE
				pu_plid = {$player->getId ()} AND 
				pu_key = '{$db->escape ($key)}'				
		");

		return $db->query
		("
			INSERT INTO
				n_players_update
			SET
				pu_plid = {$player->getId ()},
				pu_key = '{$db->escape ($key)}',
				pu_value = '{$db->escape ($value)}'
		");
	}
}