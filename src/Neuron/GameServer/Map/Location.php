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

class Neuron_GameServer_Map_Location 
	extends Neuron_GameServer_Map_Vector3
{
	public static function getRandomLocation (Neuron_GameServer_Map_Area $area)
	{
		mt_srand ();
		return new self (mt_rand (-500, 500), mt_rand (-500, 500), mt_rand (-500, 500));
	}

	// Get a random int based on $x, $y and $base
	public function getRandomNumber ($base)
	{	
		$x = $this->x ();
		$y = $this->y ();

		$in = md5 ($x . $base . $y);

		$chars = 5;
		$in = substr ($in, ($x * $y) % (32 - $chars), $chars);
		
		return round ((base_convert ($in, 16, 10) % $base) + 1);
	}

	public function getDistance (Neuron_GameServer_Map_Location $location)
	{
		return sqrt (
			pow ($location->x () - $this->x (), 2) + 
			pow ($location->y () - $this->y (), 2) + 
			pow ($location->z () - $this->z (), 2)
		);
	}
}
?>
