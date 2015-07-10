<?php
class Neuron_GameServer_Map_Display_Cuboid
	extends Neuron_GameServer_Map_Display_Mesh
{
	private $width, $height, $depth;

	public function __construct ($width, $height, $depth, Neuron_GameServer_Map_Color $color)
 	{
 		$this->width = $width;
 		$this->height = $height;
 		$this->depth = $depth;

 		$this->setColor ($color);
 	}

 	public function getWidth ()
 	{
 		return $this->width;
 	}

 	public function getHeight ()
 	{
 		return $this->height;
 	}

	public function getDisplayData ()
	{
		return array
		(
			'attributes' => array 
			(
				'model' => 'cuboid',
				'width' => $this->width,
				'height' => $this->height,
				'depth' => $this->depth,
				'color' => $this->getColor ()->getHex ()
			)
		);
	}	
}