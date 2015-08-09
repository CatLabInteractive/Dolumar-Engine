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

class Neuron_GameServer_LogSerializer
{
	public static function encode ($data)
	{
		// Collect thze data
		$out = array ();
		foreach ($data as $v)
		{
			if (is_object ($v))
			{
				if ($v instanceof Neuron_GameServer_Interfaces_Logable)
				{
					$out[] = self::getClassId ($v).':'.$v->getId();
				}
				else
				{
					$out[] = 'unknown';
				}
			}
			else
			{
				$out[] = $v;
			}
		}
		
		return json_encode ($out);
	}
	
	public static function decode ($sData)
	{
		if (is_array ($sData))
		{
			$data = json_decode ($sData['l_data'], true);
		}
	
		elseif (empty ($sData))
		{
			$data = array ();
		}
		else
		{
			$data = json_decode ($sData, true);
		}
		
		$out = array ();
		
		if (!is_array ($data))
		{
			throw new Neuron_Core_Error ('getObjectsFromLog expects an array.');
		}
		
		foreach ($data as $v)
		{
			$classn = explode (':', $v);
			
			if (count ($classn) >= 2)
			{
				$class = self::getClassName ($classn[0]);
				
				if ($class)
				{
					array_shift ($classn);
					$object = call_user_func (array ($class, 'getFromId'), implode (':', $classn));
					$out[] = $object;
				}
				else
				{
					$out[] = null;
				}
			}
			else
			{
				$out[] = $v;
			}
		}
		
		return $out;
	}
	
	private static function & getClasses ($reload = false)
	{
		static $classes;
	
		// Check if this log is found
		if (!isset ($classes) || $reload)
		{
			$classes = array ();
		
			// Load the classnames
			$db = Neuron_DB_Database::__getInstance ();
			
			$data = $db->query
			("
				SELECT
					*
				FROM
					n_logables
			");
			
			$classes['names'] = array ();
			$classes['ids'] = array ();
			
			// Put them in thze array
			foreach ($data as $v)
			{
				$classes['names'][$v['l_name']] = $v['l_id'];
				$classes['ids'][$v['l_id']] = $v['l_name'];
			}
		}
		
		return $classes;
	}
	
	/*
		Get classname ID
	*/
	private static function getClassId ($object)
	{
		$name = get_class ($object);
		
		$classes = self::getClasses ();
		
		$classnames = $classes['names'];
		
		// Check if isset!
		if (!isset ($classnames[$name]))
		{
			// Insert a new ID
			$db = Neuron_DB_Database::__getInstance ();
			
			$db->query
			("
				INSERT INTO
					n_logables
				SET
					l_name = '".$name."'
			");
			
			/*
			$id = $db->getInsertId ();
			
			$classes['names'][$name] = $id;
			$classes['ids'][$id] = $name;
			*/
			
			$classes = self::getClasses (true);
		}
		
		return $classes['names'][$name];
	}
	
	private static function getClassName ($id)
	{
		$classes = self::getClasses ();
	
		if (isset ($classes['ids'][$id]))
		{
			return $classes['ids'][$id];
		}
		return false;
	}
}
?>
