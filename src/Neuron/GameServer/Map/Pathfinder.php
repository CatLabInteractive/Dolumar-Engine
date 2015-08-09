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

class Neuron_GameServer_Map_Pathfinder
{
	private $map;

	public function __construct ()
	{
	
	}
	
	/**
	*	Set the map in an assoc array.
	*/
	public function setMap ($assocMapArray)
	{
		$this->map = $assocMapArray;
	}
	
	/**
	*	Check if a this location is passable
	*/
	private function isPassable ($x, $y)
	{
		return $this->getCost ($x, $y) > 0;
	}
	
	/**
	*	Get the amount of points required to 
	*	pass this point (or zero if impassable)
	*/
	private function getCost ($x, $y)
	{
		return isset ($this->map[$y]) && isset ($this->map[$y][$x]) 
			? $this->map[$y][$x] : 0;
	}
	
	/**
	*	Calculate and return the path
	*/
	public function getPath 
	(
		Neuron_GameServer_Map_Location $start, 
		Neuron_GameServer_Map_Location $end
	)
	{
		// Start here :-)
		$x1 = $start[0];
		$y1 = $start[1];
		
		$x2 = $end[0];
		$y2 = $end[1];
		
		if (!$this->isPassable ($x2, $y2))
		{
			return false;
		}
		
		$astar = $this->astar ($start, $end);

		//var_dump ($astar);
		//exit;

		return $astar;
	}

	private function getHeuristics ($loc1, $loc2)
	{
		$h = $this->distance ($loc1[0], $loc1[1], $loc2[0], $loc2[1]);		
		return $h;
	}

	private function distance ($x1, $y1, $x2, $y2)
	{
		return sqrt (pow ($x1 - $x2, 2) + pow ($y1 - $y2, 2) );
	}

	private function astar (Neuron_GameServer_Map_Location $start, Neuron_GameServer_Map_Location $destination)
	{
		$came_from = array ();
		
		$g = array ();
		$h = array ();
		$f = array ();
		$d = array ();

		$start = array ($start[0], $start[1]);
		$destination = array ($destination[0], $destination[1]);
		
		$k = $this->getKeyName ($start);
		
		$closed = array ();
		$open = array ($k => $start);
		
		$g[$k] = 0;
		$h[$k] = $this->distance ($start[0], $start[1], $destination[0], $destination[1]);
		$f[$k] = $h[$k];
		$d[$k] = 0;
		
		$i = 0;

		while (count ($open) > 0)
		{
			// Fetch the lowest $f
			$tmp = array_keys ($open);
			$fk = $tmp[0];
			
			foreach ($open as $k => $v)
			{
				if ($f[$k] < $f[$fk])
				{
					$fk = $k;
				}
			}
			
			$x = $open[$fk];
			
			// This is the end... my only friend, the end.
			if ($x[0] == $destination[0] && $x[1] == $destination[1])
			{
				$route = array ($destination);	
				$route[0]['d'] = 0;
				
				$this->getRoute ($came_from, $fk, $route);
				
				//return array ($g[$fk], $route);
				return new Neuron_GameServer_Map_Path ($route, $g[$fk]);
			}
			
			else
			{
				// Remove from open, put in closed.
				unset ($open[$fk]);
				$closed[$fk] = $x;
				
				$neighbours = $this->getNeighbourNodes ($x, $destination);
				
				foreach ($neighbours as $k => $v)
				{
					$cost = $g[$fk] + $v['cost'];
					
					if (isset ($open[$k]) && $cost < $g[$k])
					{
						unset ($open[$k]);
					}
					
					if (isset ($closed[$k]) && $cost < $g[$k])
					{
						unset ($closed[$k]);
					}
					
					if (!isset ($open[$k]) && !isset ($closed[$k]))
					{	
						$g[$k] = $cost;				
						$open[$k] = $v['key'];
						$f[$k] = $g[$k] + $v['heuristics'];

						
						$came_from[$k] = $x;
						$came_from[$k]['d'] = $v['cost'];
					}
				}
			}

			$i ++;
		}
		
		return false;
	}

	private function getNeighbourNodes ($l, $destination)
	{
		$out = array ();

		$x = $l[0];
		$y = $l[1];

		$this->addNeighbourNode ($out, $x + 1, $y + 0, $destination);
		$this->addNeighbourNode ($out, $x - 1, $y + 0, $destination);
		$this->addNeighbourNode ($out, $x + 0, $y + 1, $destination);
		$this->addNeighbourNode ($out, $x + 0, $y - 1, $destination);

		return $out;
	}

	private function addNeighbourNode (&$out, $x, $y, $destination)
	{
		$c = $this->getCost ($x, $y);
		if ($c > 0)
		{
			$out[$this->getKeyName (array ($x, $y))] = array (
				'key' => array ($x, $y),
				'cost' => $c,
				'heuristics' => $this->getHeuristics (array ($x, $y), $destination)
			);
		}
	}

	private function getRoute ($came_from, $k, &$array)
	{
		if (isset ($came_from[$k]))
		{
			$array[] = $came_from[$k];			
			$this->getRoute ($came_from, $this->getKeyName ($came_from[$k]), $array);
		}
		return;
	}

	private function getKeyName ($loc)
	{
		return $loc[0] . '|' . $loc[1];
	}

	private function straightline (Neuron_GameServer_Map_Location $start, Neuron_GameServer_Map_Location $end)
	{
		// Make array of path and calculate the total cost
		$path = array ();

		// Start here :-)
		$x1 = $start[0];
		$y1 = $start[1];
		
		$x2 = $end[0];
		$y2 = $end[1];
		
		$x = $x1;
		$y = $y1;
		
		while (($x != $x2 || $y != $y2) && count ($path) < 2500)
		{
			$tx = $x - $x2;
			$ty = $y - $y2;
			
			if (abs ($tx) > abs ($ty))
			{
				$x -= $tx > 0 ? 1 : -1;
			}
			else
			{
				$y -= $ty > 0 ? 1 : -1;
			}
			
			$path[] = array ($x, $y);
		}
		
		$cost = count ($path);
		
		return new Neuron_GameServer_Map_Path ($path, $cost);
	}
}
