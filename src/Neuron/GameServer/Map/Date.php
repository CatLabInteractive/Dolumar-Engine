<?php
class Neuron_GameServer_Map_Date
{
	private $timestamp;

	public function __construct ($timestamp)
	{
		$this->timestamp = $timestamp;
	}
	
	public function date ()
	{
		return $this->timestamp;
	}
}
?>
