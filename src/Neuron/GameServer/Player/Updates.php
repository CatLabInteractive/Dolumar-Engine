<?php
/**
 *  Dolumar engine, php/html MMORTS engine
 *  Copyright (C) 2009 Thijs Van der Schaeghe
 *  CatLab Interactive bvba, Gent, Belgium
 *  http://www.catlab.eu/
 *  http://www.dolumar.com/
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

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