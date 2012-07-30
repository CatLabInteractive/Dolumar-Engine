<?php
interface Neuron_GameServer_Interfaces_Map
{
	/*
		Return a "location" object
	*/
	public function getLocation ($x, $y, $hasObject);
	
	public function getObjects ($points, $radius);
}
?>
