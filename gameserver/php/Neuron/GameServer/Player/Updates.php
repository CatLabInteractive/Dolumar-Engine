<?php
/**
* Updates:
* 
* Updates are flags that can be set and checked.
* Updates are session based, but are also kept
* in a database. Flags are cancelled right after
* they have been requested, so if you set a flag
* it will stay available until it gets requested.
* (It is available on all sessions and all sessions
* will receive it once.)
*/
class Neuron_GameServer_Player_Updates
{
	private $triggers = array ();

	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	/**
	* This method refreshes the session and makes sure we have
	* the latest data from the database.
	*/
	private function refreshSession ()
	{
		$mapper = Neuron_GameServer_Mappers_UpdateMapper::getInstance ();

		// First check if we have a last id
		if (!isset ($_SESSION['ngpu_lastlog']))
		{
			// New session, can't have updates. No flags set.
			$_SESSION['ngpu_lastlog'] = $mapper->getLastLogId ($this->objProfile);
			$_SESSION['ngpu_data'] = array ();
		}

		else
		{
			$lastLogId = $_SESSION['ngpu_lastlog'];

			// Check for updates
			$updates = $mapper->getUpdates ($this->objProfile, $lastLogId);

			// Process these updates
			foreach ($updates as $v)
			{
				$_SESSION['ngpu_data'][$v['key']] = $v['value'];
				$lastLogId = max ($v['id'], $lastLogId);
			}

			$_SESSION['ngpu_lastlog'] = $lastLogId;
		}
	}

	public function setFlag ($flagname, $value = 1)
	{
		$mapper = Neuron_GameServer_Mappers_UpdateMapper::getInstance ();
		$mapper->addUpdate ($this->objProfile, $flagname, $value);

		$this->refreshSession ();
	}

	public function getFlag ($flagname)
	{
		// Reload the session
		$this->refreshSession ();

		if (isset ($_SESSION['ngpu_data'][$flagname]))
		{
			$output = $_SESSION['ngpu_data'][$flagname];
			unset ($_SESSION['ngpu_data'][$flagname]);
			return $output;
		}

		return null;
	}
	
	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}