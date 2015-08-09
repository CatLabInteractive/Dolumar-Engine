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

class Neuron_GameServer_Mappers_PlayerRegistryMapper
{
	public static function load (Neuron_GameServer_Player $player)
	{
		$db = Neuron_DB_Database::getInstance ();

		$data = $db->query 
		("
			SELECT
				*
			FROM
				n_players_registry
			WHERE
				plid = {$player->getId ()}
		");

		return $data;
	}

	public static function set (Neuron_GameServer_Player $player, $key, $value)
	{
		$db = Neuron_DB_Database::getInstance ();

		$data = $db->query 
		("
			SELECT
				*
			FROM
				n_players_registry
			WHERE
				plid = {$player->getId ()} AND 
				pr_name = '{$db->escape ($key)}'
		");

		if (count ($data) == 0)
		{
			$db->query 
			("
				INSERT INTO
					n_players_registry
				SET
					plid = {$player->getId ()},
					pr_name = '{$db->escape ($key)}',
					pr_value = '{$db->escape ($value)}'
			");
		}

		else
		{
			$db->query 
			("
				UPDATE
					n_players_registry
				SET
					pr_value = '{$db->escape ($value)}'
				WHERE
					plid = {$player->getId ()} AND
					pr_name = '{$db->escape ($key)}'
					
			");
		}
	}
}