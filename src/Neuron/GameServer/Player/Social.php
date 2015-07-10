<?php
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
					ps_plid = {$this->getId()}
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
				ps_plid = {$this->getId()} 
				AND ps_targetid = {$player->getId ()}
		");
		
		if (count ($chk) == 0)
		{
			$db->query
			("
				INSERT INTO
					n_players_social
				SET
					ps_plid = {$this->getId()},
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
					ps_plid = {$this->getId()}
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

	private function getSocialStatuses ($iStatus)
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
