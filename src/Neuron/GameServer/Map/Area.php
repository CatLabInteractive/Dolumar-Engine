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

class Neuron_GameServer_Map_Area
{
	private $center;
	private $radius;

	private $minima;
	private $maxima;

	public function __construct (Neuron_GameServer_Map_Location $center, $radius)
	{
		if (! ($radius instanceof Neuron_GameServer_Map_Radius))
		{
			$radius = new Neuron_GameServer_Map_Radius ($radius);
		}

		$this->setSquareArea ($center, $radius);
	}
	
	private function setSquareArea (Neuron_GameServer_Map_Location $center, Neuron_GameServer_Map_Radius $radius)
	{
		$this->center = $center;
		$this->radius = $radius;

		$this->minima = array ($center[0] - $radius->radius (), $center[1] - $radius->radius (), $center[2] - $radius->radius ());
		$this->maxima = array ($center[0] + $radius->radius (), $center[1] + $radius->radius (), $center[2] + $radius->radius ());
	}
	
	public function getCenter ()
	{
		return $this->center;
	}
	
	public function getRadius ()
	{
		return $this->radius->radius ();
	}

	public function isWithin (Neuron_GameServer_Map_Location $location)
	{
		foreach ($this->minima as $k => $v)
		{
			if ($location[$k] < $v)
			{
				return false;
			}
		}

		foreach ($this->maxima as $k => $v)
		{
			if ($location[$k] > $v)
			{
				return false;
			}
		}

		return true;
	}

	public function overlaps (Neuron_GameServer_Map_Area $area)
	{
		$chk = true;
		foreach ($this->minima as $k => $v)
		{
			if ($area->maxima[$k] < $v)
			{
				$chk = false;
			}
		}

		foreach ($this->maxima as $k => $v)
		{
			if ($area->minima[$k] > $v)
			{
				$chk = false;
			}
		}

		return $chk;
	}
}
?>
