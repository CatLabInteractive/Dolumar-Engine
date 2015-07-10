<?php
class Neuron_GameServer_Map_ObjectStatus implements ArrayAccess
{
	private $x, $y, $z;

	public function __construct ($x, $y, $z = 0)
	{
		if (!is_numeric ($x) || !is_numeric ($y) || !is_numeric ($z))
		{
			throw new Neuron_Exceptions_InvalidParameter ("Coordinates should be numeric.");
		}
		
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}
	
	public function x ()
	{
		return $this->x;
	}
	
	public function y ()
	{
		return $this->y;
	}
	
	public function z ()
	{
		return $this->z;
	}
	
	/**
		For our array acces
	*/
	public function offsetExists ( $offset )
	{
		return offsetGet ($offset) != null;
	}
	
	public function offsetGet ( $offset )
	{
		switch ($offset)
		{
			case 0:
			case 'x':
				return $this->x;
			break;
			
			case 1:
			case 'y':
				return $this->y;
			break;
			
			case 2:
			case 'z':
				return $this->z;
			break;
		}
		
		return null;
	}
	
	public function offsetSet ( $offset , $value )
	{
		throw new Neuron_Exceptions_InvalidParameter ("You cannot change the location parameters.");
	}
	
	public function offsetUnset ( $offset )
	{
		throw new Neuron_Exceptions_InvalidParameter ("You cannot change the location parameters.");
	}
}
?>
