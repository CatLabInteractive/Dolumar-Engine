<?php
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
