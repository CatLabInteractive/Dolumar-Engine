<?php
if (!defined ('USE_PROFILE'))
{
	define ('USE_PROFILE', false);
}

class Neuron_Profiler_Profiler
{
	public static function __getInstance ($forceusage = false)
	{
		static $in;
		if (!isset ($in))
		{
			$in = new self ();
		}
		
		if ($forceusage)
		{
			$in->setForceActivate ($forceusage);
		}
		
		return $in;
	}
	
	public static function getInstance ($forceusage = false)
	{
		return self::__getInstance ($forceusage);
	}
	
	private $oRoot;
	private $oCurAction;
	
	public function __construct ()
	{
		$this->oRoot = new Neuron_Profiler_Action ('Initializing profiler', null);
		$this->oCurAction = $this->oRoot;
		$this->active = USE_PROFILE;
	}
	
	public function setForceActivate ($activate = true)
	{
		$this->active = true;
	}
	
	public function start ($sAction)
	{
		if ($this->active)
		{
			// Make new action
			$oAction = new Neuron_Profiler_Action ($sAction, $this->oCurAction);
		
			// Append action
			$this->oCurAction->appendChild ($oAction);
		
			// Set current action
			$this->oCurAction = $oAction;
		}
	}
	
	public function stop ()
	{
		if ($this->active)
		{
			$this->oCurAction->stop ();
			$this->oCurAction = $this->oCurAction->getParent ();
		}
	}
	
	public function message ($sMessage)
	{
		$this->start ($sMessage);
		$this->stop ();
	}
	
	public function getTotalDuration ()
	{
		return $this->oRoot->getDuration ();
	}
	
	public function getOutput ()
	{
		return (string)$this->oCurAction;
	}
	
	public function __toString ()
	{
		return $this->getOutput ();
	}
}
?>
