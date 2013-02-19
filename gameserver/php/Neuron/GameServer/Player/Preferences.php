<?php
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
				players_preferences
			WHERE
				p_plid = {$this->getId()}
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
				players_preferences
			WHERE
				p_key = '{$db->escape($sKey)}'
				AND p_plid = {$this->objProfile->getId()}
		");
		
		if (count ($check) == 0)
		{
			$db->query
			("
				INSERT INTO
					players_preferences
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
					players_preferences
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
