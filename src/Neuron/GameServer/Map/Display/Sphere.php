<?php
class Neuron_GameServer_Map_Display_Sphere
	extends Neuron_GameServer_Map_Display_Mesh
{
	private $radius;

	public function __construct ($radius, Neuron_GameServer_Map_Color $color)
 	{
 		$this->radius = $radius;
 		$this->setColor ($color);
 	}

 	public function getRadius ()
 	{
 		return $this->radius;
 	}

	public function getDisplayData ()
	{
		return array
		(
			'attributes' => array 
			(
				'model' => 'sphere',
				'radius' => $this->radius,
				'color' => $this->getColor ()->getHex ()
			)
		);
	}	
}