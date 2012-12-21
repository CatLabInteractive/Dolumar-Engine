<?php
abstract class Neuron_GameServer_Map_Managers_MapObjectManager
{
	/**
	*	Return all display objects that are within $radius
	*	of $location (so basically loading a bubble)
	*/
	public abstract function getDisplayObjects (Neuron_GameServer_Map_Area $area);
	
	/**
	*	Move an object to another position
	*/	
	public function move 
	(
		Neuron_GameServer_Map_MapObject $object,
		Neuron_GameServer_Map_Location $location,
		Neuron_GameServer_Map_Date $start, 
		Neuron_GameServer_Map_Date $end
	)
	{
		throw new Neuron_Exceptions_NotImplemented ("The move method is not implemented in this map.");
	}
	
	/**
	*	Return all objects on one specific location
	*/
	public function getFromLocation (Neuron_GameServer_Map_Location $location)
	{
		$area = new Neuron_GameServer_Map_Area ($location, 1);
		
		$objects = $this->getDisplayObjects ($area);
		$out = array ();
		
		foreach ($objects as $v)
		{
			if ($v->getLocation ()->equals ($location))
			{
				$out[] = $v;
			}
		}
		
		return $out;
	}

	public function getFromUOID ($id)
	{
		return Neuron_GameServer_Mappers_IDMapper::getObject ($id);
	}
	
	/**
	*	Load all objects with the radius of the points
	*	This method combines a set of getDisplayObjects calls
	*	
	*	$requests contains an array of boundaries
	*
	*	Overload this method to increase performance
	*/	
	public function getMultipleDisplayObjects ($areas)
	{
		$out = array ();
		foreach ($areas as $v)
		{
			if (! ($v instanceof Neuron_GameServer_Map_Area))
			{
				throw new Neuron_Exceptions_InvalidParameter ("Parameters must be an array of area objects.");
			}
		
			foreach ($this->getDisplayObjects ($v) as $v)
			{
				if (! $v instanceof Neuron_GameServer_Map_MapObject)
				{
					throw new Neuron_Core_Error ("All map objects MUST implement Neuron_GameServer_Map_MapObject");
				}
				
				$out[] = $v;
			}
		}
		
		return $out;
	}
}
?>
