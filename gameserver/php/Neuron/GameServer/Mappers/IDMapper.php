<?php
class Neuron_GameServer_Mappers_IDMapper
{
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
			return $chk[0]['ai_id'];
		}

		else
		{
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
}