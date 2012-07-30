<?php
class Neuron_GameServer_Map_Managers_VoidBackgroundManager
	implements Neuron_GameServer_Map_Managers_BackgroundManager
{
	/**
	*	Return an array of all display objects
	*/
	public function getLocation (Neuron_GameServer_Map_Location $location, $objectcount = 0)
	{
		return null;
	}
	
	public function getTileSize ()
	{
		return array (200, 100, 0);
	}
}