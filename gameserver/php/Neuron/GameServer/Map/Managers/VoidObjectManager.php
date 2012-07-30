<?php
class Neuron_GameServer_Map_Managers_VoidObjectManager
	extends Neuron_GameServer_Map_Managers_MapObjectManager
{
	/**
	*	Return all display objects that are within $radius
	*	of $location (so basically loading a bubble)
	*/
	public function getDisplayObjects (Neuron_GameServer_Map_Area $area)
	{
		return array ();
	}
}