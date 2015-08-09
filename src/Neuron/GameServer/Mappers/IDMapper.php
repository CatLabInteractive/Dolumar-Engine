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

class Neuron_GameServer_Mappers_IDMapper
{
	private static $JUST_ADDED = false;

	public static function getId (Andromeda_Interfaces_Identifiable $object)
	{
		$db = Neuron_DB_Database::getInstance ();

		$classId = Andromeda_Mappers_ClassMapper::getId ($object);
		$identification = $object->getIdentifiableString ();

		$db = Neuron_DB_Database::getInstance ();

		$chk = $db->query 
		("
			SELECT
				ai_id,
				ai_identifier,
				ai_class
			FROM
				n_id
			WHERE
				ai_class = {$classId} &&
				ai_identifier = '{$db->escape ($identification)}'
		");

		if (count ($chk) > 0)
		{
			self::$JUST_ADDED = false;
			return $chk[0]['ai_id'];
		}

		else
		{
			self::$JUST_ADDED = true;
			$id = $db->query 
			("
				INSERT INTO
					n_id
				SET
					ai_class = {$classId},
					ai_identifier = '{$db->escape ($identification)}'
			");

			return $id;
		}
	}

	public static function getObject ($id)
	{
		$db = Neuron_DB_Database::getInstance ();

		$id = intval ($id);

		$data = $db->query 
		("
			SELECT
				ai_class,
				ai_identifier
			FROM
				n_id
			WHERE
				ai_id = {$id}
		");

		if (count ($data) > 0)
		{
			$class = Andromeda_Mappers_ClassMapper::getClass ($data[0]['ai_class']);
			return $class::getFromIdentifiableString ($data[0]['ai_identifier']);
		}

		else
		{
			throw new Exception ("Object not found in IDMapper.");
		}
	}

	public static function isJustAdded ()
	{
		return self::$JUST_ADDED;
	}
}