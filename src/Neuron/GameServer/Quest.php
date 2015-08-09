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

abstract class Neuron_GameServer_Quest
{
	public final function __construct ()
	{
	
	}

	public function getId ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$id = $db->query
		("
			SELECT
				q_id
			FROM
				n_quests
			WHERE
				q_class = '".$db->escape (get_class ($this))."'
		");
		
		if (count ($id) == 1)
		{
			return intval ($id[0]['q_id']);
		}
		else
		{
			return intval ($db->query
			("
				INSERT INTO
					n_quests
				SET
					q_class = '".$db->escape (get_class ($this))."'
			"));
		}
	}
	
	private static function getAllQuests ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$data = $db->query
		("
			SELECT
				*
			FROM
				n_quests
		");
		
		$out = array ();
		foreach ($data as $v)
		{
			$out[$v['q_id']] = $v['q_class'];
		}
		return $out;
	}
	
	public static function getFromId ($id)
	{
		static $quests;
		
		if (!isset ($quests) || !isset ($quests[$id]))
		{
			$quests = self::getAllQuests ();
		}
		
		if (isset ($quests[$id]))
		{
			$classname = $quests[$id];
			if (class_exists ($classname))
			{
				$foo = new $classname ();
				if ($foo instanceof self)
				{
					return $foo;
				}
			}
		}
		
		throw new Neuron_Core_Error ('Quest not found: ' . $id);
	}

	public function onStart (Neuron_GameServer_Player $player)
	{
		
	}

	public function onComplete (Neuron_GameServer_Player $player)
	{
	
	}
	
	public function isFinished (Neuron_GameServer_Player $player)
	{
		return false;
	}
}
?>
