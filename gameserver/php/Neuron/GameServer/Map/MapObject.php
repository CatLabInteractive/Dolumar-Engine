<?php
abstract class Neuron_GameServer_Map_MapObject
{
	/**
	*	Return a displayobject reprecenting this object
	*/
	public abstract function getDisplayObject ();
	
	public abstract function getName ();
	
	private $location;
	private $status;
	private $events = array ();
	private $movements = array ();
	
	public function getId ()
	{
		return $this->getLocation ()->toString ();
	}

	public function getUOID ()
	{
		return $this->getId ();
	}

	public function setLocation (Neuron_GameServer_Map_Location $location)
	{
		$this->location = $location;
	}
	
	public function getLocation ($time = NOW)
	{
		// Go trough the paths to update
		foreach ($this->movements as $v)
		{
			if ($v->isActive ($time))
			{
				return $v->getCurrentLocation ($time);
			}
		}

		return $this->location;
	}

	/**
	* Goes true all paths and checks the final location
	*/
	public function getEndLocation ()
	{
		$lastLocation = $this->getLocation ();
		$lastDate = NOW;

		foreach ($this->movements as $v)
		{
			if ($v->getEndTime () > $lastDate)
			{
				$lastDate = $v->getEndTime ();
				$lastLocation = $v->getEndLocation ();
			}
		}

		return $lastLocation;
	}

	public function getEndUp ()
	{
		$lastUp = $this->getLocation ();
		$lastDate = NOW;

		foreach ($this->movements as $v)
		{
			if ($v->getEndTime () > $lastDate)
			{
				$lastDate = $v->getEndTime ();
				$lastUp = $v->getEndUp ();
			}
		}

		return $lastUp;
	}
	
	public function setMapObjectStatus (Neuron_GameServer_Map_MapObjectStatus $status)
	{
		$this->status = $status;
	}
	
	public function getMapObjectStatus ()
	{
		if ($this->status == null)
		{
			$this->setMapObjectStatus (new Neuron_GameServer_Map_MapObjectStatus ());
		}
		
		return $this->status;
	}
	
	/**
	*	Events
	*/
	private function checkValidEvent ($method)
	{
		switch ($method)
		{
			case 'click':
			
			break;
			
			default:
				throw new Neuron_Exceptions_InvalidParameter ("method not suitable for observation: " . $method);
			break;
		}		
	}
	
	public function observe ($method, $action)
	{
		$this->checkValidEvent ($method);
		
		if (!isset ($this->events[$method]))
		{
			$this->events[$method] = array ();
		}
		
		$this->events[$method][] = $action;
	}
	
	public function getEvents ($method)
	{
		$this->checkValidEvent ($method);
	
		if (isset ($this->events[$method]))
		{
			return $this->events[$method];
		}
		
		return array ();
	}

	public function move (Neuron_GameServer_Map_Movement $movement)
	{
		// To be defined by clients
	}

	public function addMovement (Neuron_GameServer_Map_Movement $movement)
	{
		$this->movements[] = $movement;
	}

	/**
	* The up vector can be manipulated by the movements.
	*/
	public function getUp ($time = NOW)
	{
		// Go trough the paths to update
		foreach ($this->movements as $v)
		{
			if ($v->isActive ($time))
			{
				$up = $v->getCurrentUp ($time);
				if (isset ($up))
				{
					return $v->getCurrentUp ($time);
				}
			}
		}

		return new Neuron_GameServer_Map_Vector3 (0, 1, 0);
	}

	/**
	* Rotation can also be manipulated by the movement.
	* By default, an object always "looks" in the direction of the movement
	*/
	public function getDirection ()
	{
		return new Neuron_GameServer_Map_Vector3 (0, 0, 0);
	}

	/**
	* Return the default rotation (use this to turn your object around)
	*/
	public function getDefaultRotation ()
	{
		return new Neuron_GameServer_Map_Vector3 (0, 0, 0);
	}

	public function getExportData ()
	{
		$location = $this->getLocation ();

		$path = array ();
		foreach ($this->movements as $v)
		{
			$path[] = $v->getData ();
		}

		$displayobject = $this->getDisplayObject ();

		$out = array
		(
			'attributes' => array
			(
				'x' => $location->x (),
				'y' => $location->y (),
				'z' => $location->z (),
				'id' => $this->getUOID (),
				'name' => $this->getName ()
			),
			'up' => array ('attributes' => $this->getUp ()->getData ()),
			'model' => $displayobject->getDisplayData (),
			'defaultRotation' => array ('attributes' => $this->getDefaultRotation ()->getData ()),
			'direction' => array ('attributes' => $this->getDirection ()->getData ()),
			'paths' => $path
		);

		return $out;
	}
}