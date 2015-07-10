<?php
class Neuron_GameServer_Mappers_CachedUpdateMapper
	extends Neuron_GameServer_Mappers_UpdateMapper
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
		parent::__construct ();
	}

	
}