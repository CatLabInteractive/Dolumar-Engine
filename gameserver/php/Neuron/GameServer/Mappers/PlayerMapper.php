<?php
class Neuron_GameServer_Mappers_PlayerMapper
{
	private static function generateCode ()
	{
		$code = '';
		for ($i = 0; $i < 32; $i ++)
		{
			$code .= dechex (mt_rand (0, 15));
		}
		return $code;
	}

	public static function createTemporaryPlayer ()
	{
		$db = Neuron_DB_Database::getInstance ();

		$player = self::createPlayer ();

		$code = self::generateCode ();

		$db->query 
		("
			INSERT INTO
				n_players_temporary
			SET
				plid = {$player->getId ()},
				pt_code = '{$db->escape ($code)}'
		");

		return $player;
	}

	public static function createPlayer ()
	{
		$db = Neuron_DB_Database::getInstance ();

		$id = $db->query 
		("
			INSERT INTO
				players
			SET
				creationDate = NOW()
		");

		return Neuron_GameServer::getPlayer ($id);
	}

	public static function getTemporaryPlayerData (Neuron_GameServer_Player $player)
	{
		$db = Neuron_DB_Database::getInstance ();

		$data = $db->query 
		("
			SELECT
				*
			FROM
				n_players_temporary
			WHERE
				plid = {$player->getId ()}
		");

		if (count ($data) > 0)
		{
			return $data[0];
		}
		else
		{
			return null;
		}
	}
}