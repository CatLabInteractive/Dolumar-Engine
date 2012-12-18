<?php
class Neuron_GameServer_Map_Area
{
	private $center;
	private $radius;

	private $minima;
	private $maxima;

	public function __construct (Neuron_GameServer_Map_Location $center, $radius)
	{
		if (!$radius instanceof Neuron_GameServer_Map_Radius)
		{
			$radius = new Neuron_GameServer_Map_Radius ($radius);
		}

		$this->setSquareArea ($center, $radius);
	}
	
	private function setSquareArea (Neuron_GameServer_Map_Location $center, Neuron_GameServer_Map_Radius $radius)
	{
		$this->center = $center;
		$this->radius = $radius->radius ();

		$this->minima = array ($center[0] - $radius->radius (), $center[1] - $radius->radius (), $center[2] - $radius->radius ());
		$this->maxima = array ($center[0] + $radius->radius (), $center[1] + $radius->radius (), $center[2] + $radius->radius ());
	}
	
	public function getCenter ()
	{
		return $this->center;
	}
	
	public function getRadius ()
	{
		return $this->radius->radius ();
	}

	public function isWithin (Neuron_GameServer_Map_Location $location)
	{
		foreach ($this->minima as $k => $v)
		{
			if ($location[$k] < $v)
			{
				return false;
			}
		}

		foreach ($this->maxima as $k => $v)
		{
			if ($location[$k] > $v)
			{
				return false;
			}
		}

		return true;
	}

	public function overlaps (Neuron_GameServer_Map_Area $area)
	{
		$chk = true;
		foreach ($this->minima as $k => $v)
		{
			if ($area->maxima[$k] < $v)
			{
				$chk = false;
			}
		}

		foreach ($this->maxima as $k => $v)
		{
			if ($area->minima[$k] > $v)
			{
				$chk = false;
			}
		}

		return $chk;
	}
}
?>
