<?php
class Neuron_GameServer_Map_Display_Model
	extends Neuron_GameServer_Map_Display_Mesh
{
	private $color;
	private $url;

	public function __construct ($url)
	{
		$this->url = $url;
	}

	public function getDisplayData ()
	{
		return array
		(
			'attributes' => array 
			(
				'model' => 'model',
				'url' => $this->url
			)
		);
	}	
}
?>
