<?php
class Neuron_GameServer_Map_Area
{
	private $center;
	private $radius;

	public function __construct (Neuron_GameServer_Map_Location $center, $radius)
	{
		$this->setCircleArea ($center, $radius);
	}
	
	private function setCircleArea (Neuron_GameServer_Map_Location $center, $radius)
	{
		$this->center = $center;
		$this->radius = $radius;	
	}
	
	public function getCenter ()
	{
		return $this->center;
	}
	
	public function getRadius ()
	{
		return $this->radius;
	}
}
?>
