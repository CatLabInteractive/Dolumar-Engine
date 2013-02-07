<?php
class Neuron_GameServer_Map_Vector3 implements ArrayAccess
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
	
	public function transform ($x, $y = 0, $z = 0)
	{
		$classname = get_class ($this);

		if ($x instanceof Neuron_GameServer_Map_Vector3)
		{
			$z = $x->z ();
			$y = $x->y ();
			$x = $x->x ();
		}

		return new $classname ($this->x + $x, $this->y + $y, $this->z + $z);
	}
	
	public function equals (Neuron_GameServer_Map_Vector3 $location)
	{
		return $this->x () == $location->x () 
			&& $this->y () == $location->y () 
			&& $this->z () == $location->z ();
	}

	public function scale ($scale)
	{
		$classname = get_class ($this);

		$x = $this->x * $scale;
		$y = $this->y * $scale;
		$z = $this->z * $scale;

		return new $classname ($x, $y, $z);
	}

	public function getLength ()
	{
		return sqrt ($this->x * $this->x + $this->y * $this->y + $this->z * $this->z);
	}

	public function normalize ()
	{
		return $this->scale (1 / $this->getLength ());
	}

	public function inverse ()
	{
		return $this->scale (-1);
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
	
	public function __tostring ()
	{
		if ($this->z !== null)
		{
			return '(' .  ($this->x ()) . ',' .  ($this->y ()) . ',' .  ($this->z ()) . ')';
		}
		else
		{
			return '(' . round ($this->x ()) . ',' . round ($this->y ()) . ')';
		}
	}
}
?>
