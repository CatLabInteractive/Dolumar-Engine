<?php
class Neuron_Core_Lock
{
	public static function __getInstance ()
	{
		static $in;
		
		if (!isset ($in))
		{
			$in = new Neuron_Core_Lock ();
		}
		
		return $in;
	}
	
	public static function getInstance ()
	{
		return self::__getInstance ();
	}
	
	private $softlocks;
	private $locks;
	
	public function __construct ()
	{
		$this->softlocks = array ();
		$this->locks = array ();
	}
	
	/*
		Set a lock and make sure nobody gets in.
	*/
	public function setLock ($sType, $id, $iTimeout = 300)
	{
		// First check cache and jump out of function if found
		if (isset ($this->locks[$sType]) && isset ($this->locks[$sType][$id]))
		{
			return false;
		}
		
		// Do a sessions lock just for the sake of fast loadyness ;)
		if ($this->setSoftLock ($sType, $id))
		{
			$db = Neuron_Core_Database::__getInstance ();
		
			// Lock the table
			$db->customQuery ("LOCK TABLES n_locks WRITE");
		
			$chk = $db->select
			(
				'n_locks',
				array ('l_id', 'l_date'),
				"l_type = '".$sType."' AND l_lid = ".intval ($id)
			);
		
			if (count ($chk) == 0)
			{
				$id = $db->insert
				(
					'n_locks',
					array
					(
						'l_type' => $sType,
						'l_lid' => intval ($id),
						'l_date' => time ()
					)
				);
			
				$db->customQuery ("UNLOCK TABLES");
			
				if ($id > 0)
				{
					if (!isset ($this->locks[$sType]))
					{
						$this->locks[$sType] = array ();
					}
				
					$this->locks[$sType][$id] = true;
				
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				$db->customQuery ("UNLOCK TABLES");
			
				// Check if this lock is timed out
				foreach ($chk as $v)
				{
					if ($v['l_date'] < time() - $iTimeout)
					{
						$this->releaseLock ($sType, $id);
						return $this->setLock ($sType, $id);
					}
				}
			
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function releaseLock ($sType, $id)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$db->remove 
		(
			'n_locks',
			"l_type = '".$sType."' AND l_lid = ".intval ($id)
		);
		
		$this->releaseSoftLock ($sType, $id);
		
		if (isset ($this->locks[$sType]))
		{
			unset ($this->locks[$sType][$id]);
		}
	}
	
	/*
		Soft lock: doesn't use MySQL and works only during the script.
		It does allow multiple $id types
	*/
	public function setSoftLock ($sType, $id)
	{
		if (!isset ($this->softlocks[$sType]))
		{
			$this->softlocks[$sType] = array ();
		}
		
		if (!isset ($this->softlocks[$sType][$id]))
		{
			$this->softlocks[$sType][$id] = true;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function releaseSoftLock ($sType, $id)
	{
		if (isset ($this->softlocks[$sType]))
		{
			unset ($this->softlocks[$sType][$id]);
		}
	}
}
?>
