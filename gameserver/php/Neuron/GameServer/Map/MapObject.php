<?php
abstract class Neuron_GameServer_Map_MapObject
{
	/**
		Return a displayobject reprecenting this object
	*/
	public abstract function getDisplayObject ();
	
	public abstract function getName ();
	
	private $location;
	private $status;
	private $events = array ();
	
	public function setLocation (Neuron_GameServer_Map_Location $location)
	{
		$this->location = $location;
	}
	
	public function getLocation ()
	{
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
		Events
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
}
?>
