<?php
class Neuron_GameServer_Map_Map2D 
	implements Neuron_GameServer_Map_Map
{
	private $backgroundloader;
	private $mapobjectloader;
	
	/**
		Use these methods to declare the loaders.
	*/
	public function setBackgroundManager (Neuron_GameServer_Map_Managers_BackgroundManager $loader)
	{
		$this->backgroundloader = $loader;
	}
	
	public function setMapObjectManager (Neuron_GameServer_Map_Managers_MapObjectManager $loader)
	{
		$this->mapobjectloader = $loader;
	}
	
	/**
		Getters (as defined in the interface)
	*/
	
	/**
		Return a list of all images to preload
		(DisplayObject objects)
		
		(overload to use!)	
	*/
	public function getPreloadDisplayObjects ()
	{
		return array ();
	}
	
	public function getBackgroundManager ()
	{
		return $this->backgroundloader;
	}
	
	public function getMapObjectManager ()
	{
		return $this->mapobjectloader;
	}
	
	public function getInitialLocation ()
	{
		return null;
	}
	
	/*
		Map logs
	*/
	public function addMapUpdate (Neuron_GameServer_Map_Location $location, $action)
	{
		$x = $location->x ();
		$y = $location->y ();
	
		switch ($action)
		{
			case 'BUILD':
			case 'DESTROY':
				
			break;
			
			default:
				$action = 'BUILD';
			break;
		}
		
		$db = Neuron_DB_Database::getInstance ();
		
		$x = intval ($x);
		$y = intval ($y);
		
		$db->query
		("
			INSERT INTO
				n_map_updates
			SET
				mu_action = '{$action}',
				mu_x = {$x},
				mu_y = {$y},
				mu_date = FROM_UNIXTIME(".NOW.")
		");
	}
}
?>
