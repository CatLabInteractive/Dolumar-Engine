<?php
class Neuron_GameServer_Map_Radius
{
	private $radius;

	public function __construct ($radius)
	{
		if (!is_numeric ($radius))
		{
			throw new Neuron_Exceptions_InvalidParameter ("Radius should be numeric.");
		}
	
		$this->radius = $radius;
	}
	
	public function radius ()
	{
		return $this->radius;
	}
}
?>
