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

class Neuron_GameServer_Map_Path
{
	private $path;
	private $cost;
	
	private $dx = 0;
	private $dy = 0;
	private $dz = 0;

	public function __construct ($path, $cost)
	{
		$this->path = $path;
		$this->cost = $cost;
	}
	
	public function getCost ()
	{
		return $this->cost;
	}
	
	public function transform ($x, $y, $z = 0)
	{
		$this->dx = $x;
		$this->dy = $y;
		$this->dz = $z;
	}
	
	public function getPath ()
	{
		$out = array ();
		
		foreach ($this->path as $v)
		{
			$out[] = new Neuron_GameServer_Map_Location ($v[0] + $this->dx, $v[1] + $this->dy);
		}
	
		return $out;
	}

	public function serialize ()
	{
		$out = array ();

		foreach ($this->path as $v)
		{
			$out[] = array ($v[0], $v[1]);
		}

		return json_encode ($out);
	}
}
