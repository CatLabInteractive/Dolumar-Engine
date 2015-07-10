<?php
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