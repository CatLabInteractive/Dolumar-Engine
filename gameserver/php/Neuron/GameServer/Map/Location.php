<?php
class Neuron_GameServer_Map_Location implements ArrayAccess
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
	*	For our array acces
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
	
	public function transform ($x, $y, $z = 0)
	{
		return new self ($this->x + $x, $this->y + $y, $this->z + $z);
	}
	
	public function getDistance (Neuron_GameServer_Map_Location $location)
	{
		$x1 = $this->x ();
		$y1 = $this->y ();
		$z1 = $this->z ();
		
		$x2 = $location->x ();
		$y2 = $location->y ();
		$z2 = $location->z ();
		
		return sqrt (pow ($x1 - $x2, 2) + pow ($y1 - $y2, 2) + pow ($z1 - $z2, 2));
	}
	
	public function equals (Neuron_GameServer_Map_Location $location)
	{
		return $this->x () == $location->x () 
			&& $this->y () == $location->y () 
			&& $this->z () == $location->z ();
	}

	// Get a random int based on $x, $y and $base
	public function getRandomNumber ($base)
	{	
		$x = $this->x ();
		$y = $this->y ();

		$in = md5 ($x . $base . $y);

		$chars = 5;
		$in = substr ($in, ($x * $y) % (32 - $chars), $chars);
		
		return round ((base_convert ($in, 16, 10) % $base) + 1);
	}
	
	public function __tostring ()
	{
		return '(' . $this->x () . ',' . $this->y () . ')';
	}
}
?>
