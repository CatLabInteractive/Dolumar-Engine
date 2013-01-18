<?php
class Neuron_GameServer_Map_Movement
{
	private $startTime;
	private $endTime;

	private $locations;

	private $acceleration = 0;
	private $startSpeed = 0;

	/**
	* A bezier curved movement description
	* @param $startTime: Start timestamp
	* @param $endTime: End timestamp
	* @param $more: locations
	*/
	public function __construct 
	(
		$startTime, 
		$endTime, 
		Neuron_GameServer_Map_Location $l1, 
		Neuron_GameServer_Map_Location $l2
	)
	{
		$arguments = func_get_args ();

		$this->setStartTime (array_shift ($arguments));
		$this->setEndTime (array_shift ($arguments));
		
		$this->setPoints ($arguments);
	}

	protected function setStartTime ($startTime)
	{
		$this->startTime = $startTime;
	}

	protected function setEndTime ($endTime)
	{
		$this->endTime = $endTime;
	}

	protected function setPoints ($arguments)
	{
		if (count ($arguments) < 2)
		{
			throw new Exception ("Path must have at least two points.");	
		}
		else if (count ($arguments) > 4)
		{
			throw new Exception ("The maximal amount of points is 4 right now.");
		}

		$this->locations = $arguments;	
	}

	public static function getFromPath ($startTime, $endTime, $points)
	{
		$path = array ();
		foreach ($points as $v)
		{
			$path[] = new Neuron_GameServer_Map_Location ($v[0], $v[1], $v[2]);
		}

		if (count ($path) < 2)
		{
			throw new Exception ("Path must have at least two points.");
		}

		else if (count ($path) === 2)
		{
			return new self ($startTime, $endTime, $path[0], $path[1]);
		}

		else if (count ($path) === 3)
		{
			return new self ($startTime, $endTime, $path[0], $path[1], $path[2]);
		}

		else if (count ($path) === 4)
		{
			return new self ($startTime, $endTime, $path[0], $path[1], $path[2], $path[3]);
		}

		else
		{
			throw new Exception ("The maximal amount of points is 4 right now.");
		}
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
		$values = array_values ($this->locations);
		return array_shift ($values);
	}

	public function getEndLocation ()
	{
		$values = array_values ($this->locations);
		return array_pop ($values);
	}

	public function getDuration ()
	{
		return $this->endTime - $this->startTime;	
	}

	public function getDistance ()
	{
		// TODO! Fix this, currently we just calculate the distance between the start & end point
		return $this->getEndLocation ()->getDistance ($this->getStartLocation ());
	}

	public function setAcceleration ($startSpeed, $acceleration)
	{
		$this->startSpeed = $startSpeed;
		$this->acceleration = $acceleration;
	}

	public function getInitialSpeed ()
	{
		$a = $this->calculateSpeed ();
		return $a['start'];
	}

	public function getAcceleration ()
	{
		$a = $this->calculateSpeed ();
		return $a['acceleration'];
	}

	/**
	* Return an array of speed & acceleration
	* Speed is defined in pixels / second
	*/
	private function calculateSpeed ()
	{
		$startSpeed;
		$endSpeed;
		$acceleration = 0;

		// Accelerated speed
		if (abs ($this->acceleration) > 0)
		{
			$startSpeed = $this->startSpeed;
			$acceleration = $this->acceleration;
			$endSpeed = $this->startSpeed + ($acceleration * $this->getDuration ());
		}

		// Constant velocity
		else
		{
			$startSpeed = $this->getDistance () / $this->getDuration ();
			$endSpeed = $startSpeed;
		}

		return array 
		(
			'start' => $startSpeed,
			'end' => $endSpeed,
			'acceleration' => $acceleration
		);
	}

	public function getPath ()
	{
		$out = array ();

		foreach ($this->locations as $v)
		{
			$out[] = $v->getData (false);
		}

		return $out;
	}

	public function getData ()
	{
		$out = array ();

		$out['attributes'] = array
		(
			'duration' => $this->getDuration (),
			'distance' => $this->getDistance ()
		);

		$start = $this->getStartLocation ()->getData ();
		$start['timestamp'] = $this->getStartTime ();

		$end = $this->getEndLocation ()->getData ();
		$end['timestamp'] = $this->getEndTime ();

		$out['start'] = array  ( 'attributes' => $start );
		$out['end'] = array ( 'attributes' => $end );

		$path = array ();
		foreach ($this->locations as $v)
		{
			$path[] = array ('attributes' => $v->getData (true));
		}

		$out['points'] = $path;

		$out['velocity'] = array 
		(
			'attributes' => $this->calculateSpeed ()
		);

		return $out;
	}
}