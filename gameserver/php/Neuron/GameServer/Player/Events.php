<?php
class Neuron_GameServer_Player_Events
{
	private $triggers = array ();

	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	public function observe ($trigger, $callback)
	{
		if (!isset ($this->triggers[$trigger]))
		{
			$this->triggers[$trigger] = array ();
		}
		
		$this->triggers[$trigger][] = $callback;
	}
	
	public function invoke ($trigger)
	{
		$params = func_get_args ();
		array_shift ($params);
		
		array_unshift ($params, $this->objProfile);
	
		if (isset ($this->triggers[$trigger]))
		{
			foreach ($this->triggers[$trigger] as $v)
			{
				call_user_func_array ($v, $params);
			}
		}
	}
	
	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
