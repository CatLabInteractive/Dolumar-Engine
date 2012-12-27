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

	public function isActive ($time = NOW)
	{
		return $this->startTime <= $time && $this->endTime > $time;
	}

	public function getCurrentLocation ($time = NOW)
	{
		$diff = $this->endTime - $this->startTime;
		$percentage = ($time - $this->startTime) / $diff;

		//customMail ('daedelson@gmail.com', 'coortest', $percentage);

		$s = $this->getStartLocation ();
		$e = $this->getEndLocation ();

		$dx = $e->x () - $s->x ();
		$dy = $e->y () - $s->y ();
		$dz = $e->z () - $s->z ();

		$x = $s->x () + ($dx * $percentage);
		$y = $s->y () + ($dy * $percentage);
		$z = $s->z () + ($dz * $percentage);

		return new Neuron_GameServer_Map_Location ($x, $y, $z);
	}

	public function getStartLocation ()
	{
		return $this->startLocation;
	}

	public function getEndLocation ()
	{
		return $this->endLocation;
	}

	public function getPath ()
	{
		return array ($this->getStartLocation ()->getData (false), $this->getEndLocation ()->getData (false));
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