<?php
class Neuron_GameServer_Map_Location 
	extends Neuron_GameServer_Map_Vector3
{
	public static function getRandomLocation (Neuron_GameServer_Map_Area $area)
	{
		mt_srand ();
		return new self (mt_rand (-500, 500), mt_rand (-500, 500), mt_rand (-500, 500));
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

	public function getDistance (Neuron_GameServer_Map_Location $location)
	{
		return sqrt (
			pow ($location->x () - $this->x (), 2) + 
			pow ($location->y () - $this->y (), 2) + 
			pow ($location->z () - $this->z (), 2)
		);
	}
}
?>
