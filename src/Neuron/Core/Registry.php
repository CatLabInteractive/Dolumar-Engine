<?php
class Neuron_Core_Registry
{
	public static function getInstance ($classname)
	{
		static $in;
		
		if (!isset ($in))
		{
			$in = array ();
		}
		
		if (!isset ($in[$classname]))
		{
			$in[$classname] = new $classname;
		}
		
		return $in[$classname];
	}
	
	private $objects;
	private $counters;
	
	private function __construct ()
	{
		$this->objects = array ();
		$this->counters = array ();
	}

	public function get ($id)
	{
		if (!isset ($this->objects[$id]))
		{
			$this->objects[$id] = $this->getNewObject ($id);
			$this->counters[$id] = 1;
		}
		else
		{
			$this->counters[$id] ++;
		}
		
		return $this->objects[$id];
	}
	
	public function destroy ($id)
	{
		if (isset ($this->counters[$id]))
		{
			$this->counters[$id] --;
			if ($this->counters[$id] < 1)
			{
				$this->objects[$id]->__destruct ();
				
				unset ($this->objects[$id]);
				unset ($this->counters[$id]);
			}
		}
	}
}
?>
