<?php
class Neuron_GameServer_Player_State
{
	private $temporary;

	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	private function loadTemporaryPlayerData ()
	{
		if (!isset ($this->temporary))
		{
			$this->temporary = Neuron_GameServer_Mappers_PlayerMapper::getTemporaryPlayerData ($this);
			if (!isset ($this->temporary))
			{
				$this->temporary = false;
			}
		}
	}

	public function isTemporaryAccount ()
	{
		$this->loadTemporaryPlayerData ();
		return $this->temporary ? true : false;
	}

	public function getTemporaryCode ()
	{
		return $this->temporary['pt_code'];
	}
	
	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
