<?php
interface Neuron_GameServer_Map_Display_DisplayObject
{
	/**
		Return the URI of the object
	*/
	public function getURI ();
	
	/**
		Return the color to be used on the minimap
		(should be a Neuron_GameServer_Map_Color objects)
	*/
	public function getColor ();
}
?>
