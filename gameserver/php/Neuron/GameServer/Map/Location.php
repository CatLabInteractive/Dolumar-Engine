<?php
class Neuron_GameServer_Map_Location implements ArrayAccess
{
	private $x, $y, $z;

	public function __construct ($x, $y, $z = null)
	{
		if (!is_numeric ($x) || !is_numeric ($y) || ($z !== null && !is_numeric ($z)))
		{
			throw new Neuron_Exceptions_InvalidParameter ("Coordinates should be numeric.");
		}
		
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}

	public static function getRandomLocation (Neuron_GameServer_Map_Area $area)
	{
		mt_srand ();
		return new self (mt_rand (-500, 500), mt_rand (-500, 500), mt_rand (-500, 500));
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
	
	public function transform ($x, $y = 0, $z = 0)
	{
		if ($x instanceof Neuron_GameServer_Map_Location)
		{
			$z = $x->z ();
			$y = $x->y ();
			$x = $x->x ();
		}

		return new self ($this->x + $x, $this->y + $y, $this->z + $z);
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

	public function scale ($scale)
	{
		$x = $this->x * $scale;
		$y = $this->y * $scale;
		$z = $this->z * $scale;

		return new self ($x, $y, $z);
	}

	public function getDistance (Neuron_GameServer_Map_Location $location)
	{
		return sqrt (
			pow ($location->x () - $this->x (), 2) + 
			pow ($location->y () - $this->y (), 2) + 
			pow ($location->z () - $this->z (), 2)
		);
	}

	public function getData ($assoc = true)
	{
		if ($assoc)
		{
			return array ('x' => $this->x (), 'y' => $this->y (), 'z' => $this->z ());
		}
		else
		{
			return array ($this->x (), $this->y (), $this->z ());
		}
	}
	
	public function __tostring ()
	{
		if ($this->z !== null)
		{
			return '(' . round ($this->x ()) . ',' . round ($this->y ()) . ',' . round ($this->z ()) . ')';
		}
		else
		{
			return '(' . round ($this->x ()) . ',' . round ($this->y ()) . ')';
		}
	}
}
?>
