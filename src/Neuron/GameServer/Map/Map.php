<?php
interface Neuron_GameServer_Map_Map
{
	/**
	*	Return a list of all images to preload
	*	(DisplayObject objects)
	*/
	public function getPreloadDisplayObjects ();

	/**
	*	Return a background loader object
	*/
	public function getBackgroundManager ();
	
	/**
	*	Return a map object loader
	*/
	public function getMapObjectManager ();
	
	/**
	*	Add an update to the map
	*/
	public function addMapUpdate (Neuron_GameServer_Map_Location $location, $action);
	
	/**
	*	Get initial location
	*/
	public function getInitialLocation ();
}
?>
