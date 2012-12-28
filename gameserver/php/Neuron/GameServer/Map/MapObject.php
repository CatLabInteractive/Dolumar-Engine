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
	
	public function getLocation ()
	{
		// Go trough the paths to update
		foreach ($this->movements as $v)
		{
			if ($v->isActive ())
			{
				return $v->getCurrentLocation ();
			}
		}

		return $this->location;
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
			'top' => array 
			(
				'attributes' => array
				(
					'x' => 0,
					'y' => 1,
					'z' => 0
				)
			),
			'paths' => $path,
			'model' => $displayobject->getDisplayData ()
		);

		return $out;
	}
}