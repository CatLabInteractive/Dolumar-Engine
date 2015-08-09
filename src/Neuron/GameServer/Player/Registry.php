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

class Neuron_GameServer_Player_Registry
{
	private $hashmap;

	public function __construct (Neuron_GameServer_Player $profile)
	{
		$this->objProfile = $profile;
	}

	private function load ()
	{
		if (!isset ($this->hashmap))
		{
			$this->hashmap = array ();
			$data = Neuron_GameServer_Mappers_PlayerRegistryMapper::load ($this->objProfile);

			foreach ($data as $v)
			{
				$this->hashmap[$v['pr_name']] = $v['pr_value'];
			}
		}
	}

	public function set ($key, $value)
	{
		Neuron_GameServer_Mappers_PlayerRegistryMapper::set ($this->objProfile, $key, $value);
	}

	public function get ($key)
	{
		$this->load ();
		return isset ($this->hashmap[$key]) ? $this->hashmap[$key] : null;
	}
	
	public function __destruct ()
	{
		unset ($this->objProfile);
	}
}
?>
