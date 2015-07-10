<?php
class Neuron_GameServer_Map_Display_Sprite
	implements Neuron_GameServer_Map_Display_DisplayObject
{
	private $uri;
	private $offset;
	
	private $color;

	public function __construct ($uri, Neuron_GameServer_Map_Offset $offset = null)
	{
		$this->uri = $uri;
		
		if (isset ($offset))
		{
			$this->offset = $offset;
		}
		else
		{
			$this->offset = new Neuron_GameServer_Map_Offset (0, 0, 0);
		}
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

	/**
	*	Return the URI of the object
	*/
	public function getURI ()
	{
		return $this->uri;
	}
	
	/**
	*	Return pixel offset (Neuron_GameServer_Map_Offset)
	*	for this sprite
	*/
	public function getOffset ()
	{
		return $this->offset;
	}
	
	public function getDisplayData ()
	{
		$offset = $this->getOffset ();
	
		return array
		(
			'id' => $this->getURI (),
			'url' => $this->getURI (),
			'offsetX' => $offset[0],
			'offsetY' => $offset[1]
		);
	}
}
?>
