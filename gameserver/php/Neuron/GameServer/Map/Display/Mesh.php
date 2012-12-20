<?php
class Neuron_GameServer_Map_Display_Mesh
	implements Neuron_GameServer_Map_Display_DisplayObject
{
	private $color;

	public function __construct ()
	{
		
	}
	
	public function setColor (Neuron_GameServer_Map_Color $color)
	{
		$this->color = $color;
	}
	
	public function getColor ()
	{
		if (!isset ($this->color))
		{
			$this->setColor (new Neuron_GameServer_Map_Color (0, 0, 0));
		}
		
		return $this->color;
	}
	
	public function getDisplayData ()
	{
		return array
		(
			
		);
	}
}
?>
