<?php
abstract class Neuron_Core_ModuleFactory
{
	private $oModules = array ();

	/*
		__get is run for reading data from inaccessible memebers
	*/
	public function __get ($sModule)
	{
		$this->doLoadModule ($sModule);
		return $this->oModules[$sModule];
	}
	
	public function moduleExists ($sModule)
	{
		$this->doLoadModule ($sModule);
		if (!isset ($this->oModules[$sModule]))
		{
			print_r ($this);
			return false;
		}
		else
		{
			return true;
		}
	}
	
	private function doLoadModule ($sModule)
	{
		if (!isset ($this->oModules[$sModule]))
		{
			$this->oModules[$sModule] = $this->loadModule ($sModule);
			
			//var_dump ($this->oModules[$sModule]);
			
			if (!$this->oModules[$sModule])
			{
				throw new Neuron_Core_Error 
				(
					'ModuleFactory could not load module '.$sModule.' from class '.get_class ($this)
				);
			}
		}
	}

	protected abstract function loadModule ($sModule);
	
	public function __destruct ()
	{
		if (isset ($this->oModules) && is_array ($this->oModules))
		{
			foreach ($this->oModules as $k => $v)
			{
				unset ($this->oModules[$k]);
			}
		}
		
		unset ($this->oModules);
	}
}
?>
