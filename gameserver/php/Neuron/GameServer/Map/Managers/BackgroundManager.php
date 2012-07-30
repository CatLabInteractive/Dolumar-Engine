<?php
interface Neuron_GameServer_Map_Managers_BackgroundManager
{
	/**
		Return an array of all display objects
	*/
	public function getLocation (Neuron_GameServer_Map_Location $location, $objectcount = 0);
	
	public function getTileSize ();
}
?>
