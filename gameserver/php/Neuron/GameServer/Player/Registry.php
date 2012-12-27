<?php
class Neuron_GameServer_Player_Registry
{
	private $hashmap;

	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	private function load ()
	{
		if (!isset ($this->hashmap))
		{
			$this->hashmap = array ();
			$data = Neuron_GameServer_Mappers_PlayerRegistryMapper::load ($this->objProfile);

			foreach ($data as $v)
			{
				$this->hashmap[$v['pr_name']] = $v['pr_value'];
			}
		}
	}

	public function set ($key, $value)
	{
		Neuron_GameServer_Mappers_PlayerRegistryMapper::set ($this->objProfile, $key, $value);
	}

	public function get ($key)
	{
		$this->load ();
		return isset ($this->hashmap[$key]) ? $this->hashmap[$key] : null;
	}
	
	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
