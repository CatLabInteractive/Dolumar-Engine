<?php
class Neuron_GameServer_Map_Movement
{
	private $startTime;
	private $endTime;

	private $startLocation;
	private $endLocation;

	public function __construct 
	(
		$startTime, 
		Neuron_GameServer_Map_Location $startLocation, 
		$endTime, 
		Neuron_GameServer_Map_Location $endLocation
	)
	{
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->startLocation = $startLocation;
		$this->endLocation = $endLocation;
	}

	public function getStartTime ()
	{
		return $this->startTime;
	}

	public function getEndTime ()
	{
		return $this->endTime;
	}

	public function getStartLocation ()
	{
		return $this->startLocation;
	}

	public function getEndLocation ()
	{
		return $this->endLocation;
	}

	public function getData ()
	{
		$out = array ();

		$start = $this->getStartLocation ()->getData ();
		$start['timestamp'] = $this->getStartTime ();

		$end = $this->getEndLocation ()->getData ();
		$end['timestamp'] = $this->getEndTime ();

		$out['start'] = array  ( 'attributes' => $start );
		$out['end'] = array ( 'attributes' => $end );

		return $out;
	}
}