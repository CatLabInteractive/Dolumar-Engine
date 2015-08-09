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

class Neuron_GameServer_Map_Map2D 
	implements Neuron_GameServer_Map_Map
{
	private $backgroundloader;
	private $mapobjectloader;
	
	/**
	*	Use these methods to declare the loaders.
	*/
	public function setBackgroundManager (Neuron_GameServer_Map_Managers_BackgroundManager $loader)
	{
		$this->backgroundloader = $loader;
	}
	
	public function setMapObjectManager (Neuron_GameServer_Map_Managers_MapObjectManager $loader)
	{
		$this->mapobjectloader = $loader;
	}
	
	/**
	*	Getters (as defined in the interface)
	*/
	
	/**
	*	Return a list of all images to preload
	*	(DisplayObject objects)
	*	
	*	(overload to use!)	
	*/
	public function getPreloadDisplayObjects ()
	{
		return array ();
	}
	
	public function getBackgroundManager ()
	{
		if (!isset ($this->backgroundloader))
		{
			$this->backgroundloader = new Neuron_GameServer_Map_Managers_VoidBackgroundManager ();
		}

		return $this->backgroundloader;
	}
	
	public function getMapObjectManager ()
	{
		if (!isset ($this->mapobjectloader))
		{
			$this->mapobjectloader = new Neuron_GameServer_Map_Managers_VoidObjectManager ();
		}

		return $this->mapobjectloader;
	}
	
	public function getInitialLocation ()
	{
		return null;
	}
	
	/*
		Map logs
	*/
	public function addMapUpdate (Neuron_GameServer_Map_Location $location, $action)
	{
		$x = $location->x ();
		$y = $location->y ();
	
		switch ($action)
		{
			case 'BUILD':
			case 'DESTROY':
				
			break;
			
			default:
				$action = 'BUILD';
			break;
		}
		
		$db = Neuron_DB_Database::getInstance ();
		
		$x = intval ($x);
		$y = intval ($y);
		
		$db->query
		("
			INSERT INTO
				n_map_updates
			SET
				mu_action = '{$action}',
				mu_x = {$x},
				mu_y = {$y},
				mu_date = FROM_UNIXTIME(".NOW.")
		");
	}
}
?>
