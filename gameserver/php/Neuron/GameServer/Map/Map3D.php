<?php
class Neuron_GameServer_Map_Map3D 
	extends Neuron_GameServer_Map_Map2D
{
	public function addObjectUpdate (Neuron_GameServer_Map_MapObject $object)
	{
		$db = Neuron_DB_Database::getInstance ();

		$db->query 
		("
			INSERT INTO
				n_map_object_updates
			SET
				mu_uoid = '{$db->escape ($object->getUOID ())}',
				mu_date = NOW()
		");
	}
}