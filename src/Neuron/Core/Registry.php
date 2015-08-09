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

class Neuron_Core_Registry
{
	public static function getInstance ($classname)
	{
		static $in;
		
		if (!isset ($in))
		{
			$in = array ();
		}
		
		if (!isset ($in[$classname]))
		{
			$in[$classname] = new $classname;
		}
		
		return $in[$classname];
	}
	
	private $objects;
	private $counters;
	
	private function __construct ()
	{
		$this->objects = array ();
		$this->counters = array ();
	}

	public function get ($id)
	{
		if (!isset ($this->objects[$id]))
		{
			$this->objects[$id] = $this->getNewObject ($id);
			$this->counters[$id] = 1;
		}
		else
		{
			$this->counters[$id] ++;
		}
		
		return $this->objects[$id];
	}
	
	public function destroy ($id)
	{
		if (isset ($this->counters[$id]))
		{
			$this->counters[$id] --;
			if ($this->counters[$id] < 1)
			{
				$this->objects[$id]->__destruct ();
				
				unset ($this->objects[$id]);
				unset ($this->counters[$id]);
			}
		}
	}
}
?>
