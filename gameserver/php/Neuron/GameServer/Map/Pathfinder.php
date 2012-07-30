<?php
class Neuron_GameServer_Map_Pathfinder
{
	private $map;

	public function __construct ()
	{
	
	}
	
	/**
		Set the map in an assoc array.
	*/
	public function setMap ($assocMapArray)
	{
		$this->map = $assocMapArray;
	}
	
	/**
		Check if a this location is passable
	*/
	private function isPassable ($x, $y)
	{
		return $this->getCost ($x, $y) > 0;
	}
	
	/**
		Get the amount of points required to 
		pass this point (or zero if impassable)
	*/
	private function getCost ($x, $y)
	{
		return isset ($this->map[$x]) && isset ($this->map[$x][$y]) 
			? $this->map[$x][$y] : 0;
	}
	
	/**
		Calculate and return the path
	*/
	public function getPath 
	(
		Neuron_GameServer_Map_Location $start, 
		Neuron_GameServer_Map_Location $end
	)
	{
		// Start here :-)
		$x1 = $start[0];
		$y1 = $start[1];
		
		$x2 = $end[0];
		$y2 = $end[1];
		
		if (!$this->isPassable ($x2, $y2))
		{
			return false;
		}
		
		// Make array of path and calculate the total cost
		$path = array ();
		
		$x = $x1;
		$y = $y1;
		
		while (($x != $x2 || $y != $y2) && count ($path) < 200)
		{
			$tx = $x - $x2;
			$ty = $y - $y2;
			
			if (abs ($tx) > abs ($ty))
			{
				$x -= $tx > 0 ? 1 : -1;
			}
			else
			{
				$y -= $ty > 0 ? 1 : -1;
			}
			
			$path[] = array ($x, $y);
		}
		
		$cost = count ($path);
		
		return new Neuron_GameServer_Map_Path ($path, $cost);
		
		// If no path found, return false
		return false;
	}
}
