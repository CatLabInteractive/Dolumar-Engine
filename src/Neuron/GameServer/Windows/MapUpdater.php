<?php
/*
	Windows_MapUpdater
	
	This (invisible) window will make sure the map is up to date.
*/
class Neuron_GameServer_Windows_MapUpdater extends Neuron_GameServer_Windows_Window
{	
	public function setSettings ()
	{
		$this->setClassName ('invisible');
		$this->setType ('invisible');
		$this->setAllowOnlyOnce ();

		//$this->setPool ('mapupdater');
		
		$this->setAjaxPollSeconds (5);
	}
	
	public function getContent ()
	{
		$data = $this->getRequestData ();
		return '<p>'.print_r ($data, true).'</p>';
	}
	
	private function getInitialLogID ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$last = $db->query
		("
			SELECT
				MAX(mu_id) AS laatste
			FROM
				n_map_updates
		");
		
		return $last[0]['laatste'];
	}
	
	public function processInput ()
	{
		$this->getRefresh ();
	}
	
	public function getRefresh ()
	{
		$data = $this->getRequestData ();
		
		if (!isset ($data['lastlog']))
		{
			$data['lastlog'] = $this->getInitialLogID ();
		}
		else
		{
			// Update all map areas that have been changed
			$db = Neuron_DB_Database::getInstance ();
			
			$ll = intval ($data['lastlog']);
			
			$q = $db->query
			("
				SELECT
					*
				FROM
					n_map_updates
				WHERE
					mu_id > {$ll}
			");
			
			foreach ($q as $v)
			{
				//$this->alert ('reloading ' . $v['mu_x'] . ',' . $v['mu_y']);
			
				$this->reloadLocation ($v['mu_x'], $v['mu_y']);
				
				if ($v['mu_id'] > $data['lastlog'])
				{
					$data['lastlog'] = $v['mu_id'];
				}
			}
		}
		
		$this->updateRequestData ($data);
		//$this->updateContent ();
	}
}
?>
