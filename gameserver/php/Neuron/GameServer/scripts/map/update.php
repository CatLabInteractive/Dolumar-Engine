<?php

// Update all map areas that have been changed



$db = Neuron_DB_Database::getInstance ();

$lastlog = Neuron_Core_Tools::getInput ('_REQUEST', 'from', 'int');
$ll = intval ($lastlog);

$out = array ();

if ($ll < 1)
{
	$db = Neuron_DB_Database::getInstance ();
	
	$last = $db->query
	("
		SELECT
			MAX(mu_id) AS laatste
		FROM
			n_map_object_updates
	");

	$out = array ();
	$out['objects'] = array ();
	$out['objects']['attributes'] = array ();
	
	$out['objects']['attributes']['last'] = intval ($last[0]['laatste']);
}

else
{
	$q = $db->query
	("
		SELECT
			*
		FROM
			n_map_object_updates
		WHERE
			mu_id > {$ll}
	");

	$last = $ll;
	
	$out['objects'] = array ();
	$out['objects']['attributes']['last'] = $last;

	foreach ($q as $v)
	{
		//$this->alert ('reloading ' . $v['mu_x'] . ',' . $v['mu_y']);
		$object = Neuron_GameServer::getInstance ()->getMap ()->getMapObjectManager ()->getFromUOID ($v['mu_uoid']);

		if ($last < $v['mu_id'])
		{
			$last = $v['mu_id'];
		}

		$out['objects'][] = $object->getExportData ();
	}
}

echo Neuron_Core_Tools::output_xml ($out);