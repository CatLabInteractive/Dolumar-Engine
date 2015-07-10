<?php

// Update all map areas that have been changed



$db = Neuron_DB_Database::getInstance ();

$lastlog = Neuron_Core_Tools::getInput ('_REQUEST', 'from', 'int');
$ll = intval ($lastlog);

$out = array ();
$attributes = array ();

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
	$attributes = array ('last' => intval ($last[0]['laatste']));
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
	$out['removes'] = array ();

	foreach ($q as $v)
	{
		if ($last < $v['mu_id'])
		{
			$last = $v['mu_id'];
		}
		
		if ($v['mu_action'] == 'REMOVE')
		{
			$out['removes'][] = array ('attributes' => array ('id' => $v['mu_uoid']));
		}
		else
		{
			//$this->alert ('reloading ' . $v['mu_x'] . ',' . $v['mu_y']);
			$object = Neuron_GameServer::getInstance ()->getMap ()->getMapObjectManager ()->getFromUOID ($v['mu_uoid']);
			$out['objects'][] = $object->getExportData ();
		}
	}

	$attributes['last'] = $last;
}

echo Neuron_Core_Tools::output_xml ($out, '1', 'updates', $attributes);