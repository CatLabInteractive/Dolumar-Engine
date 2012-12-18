<?php
interface Neuron_GameServer_Map_Display_DisplayObject
{	
	/**
	*	Return the color to be used on the minimap
	*	(should be a Neuron_GameServer_Map_Color objects)
	*/
	public function getColor ();

	/**
	* Return the display data to be sent to the client.
	*/
	public function getDisplayData ();
}
?>
