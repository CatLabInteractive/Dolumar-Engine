<?php
abstract class Neuron_GameServer_Map_SimpleMapObject
	extends Neuron_GameServer_Map_MapObject
{
	/**
	*	Instantiate a new object from a given id
	*/
	public abstract function getFromId ($id);
	
	/**
	*	Get the id from this object
	*/
	public abstract function getId ();
	
	
	/*
		Begin of the implemented class
	*/
	private $location;
	
	/**
	*	Implemented methods
	*/
	public function setLocation (Neuron_GameServer_Map_Location $location)
	{
		$this->location = $location;
	}
	
	public function getLocation ()
	{
		return $this->location;
	}
}
?>
