<?php
class Neuron_GameServer_Map_Color
{
	private $r, $g, $b;

	public function __construct ($r, $g = null, $b = null)
	{
		if (!isset ($g) && !isset ($b))
		{
			if (strlen ($r) == 7 && substr ($r, 0, 1) == '#')
			{
				$r = intval (substr ($r, 1, 2));
				$g = intval (substr ($g, 3, 2));
				$b = intval (substr ($b, 5, 2));
			}
			else
			{
				throw new Neuron_Exceptions_InvalidParameter ("Please provide a valid color.");
			}
		}
		
		if ($r >= 0 && $r <= 255 
			&& $g >= 0 && $g <= 255 
			&& $b >= 0 && $b <= 255)
		{
			$this->r = $r;
			$this->g = $g;
			$this->b = $b;
		}
		else
		{
			throw new Neuron_Exceptions_InvalidParameter ("Please provide a valid color.");
		}
	}

	public static function getRandomColor ()
	{
		return new self (mt_rand (0, 255), mt_rand (0, 255), mt_rand (0, 255));
	}
	
	public function r ()
	{
		return $this->r;
	}
	
	public function g ()
	{
		return $this->g;
	}
	
	public function b ()
	{
		return $this->b;
	}
	
	public function getHex ()
	{
		$r = dechex ($this->r);
		$g = dechex ($this->g);
		$b = dechex ($this->b);
		
		if (strlen ($r) < 2)
			$r = '0' . $r;
		
		if (strlen ($g) < 2)
			$g = '0' . $g;
		
		if (strlen ($b) < 2)
			$b = '0' . $b;
		
		return '#' . $r . $g . $b;
	}
}
?>
