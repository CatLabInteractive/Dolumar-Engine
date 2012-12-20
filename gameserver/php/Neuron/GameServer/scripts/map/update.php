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
			n_map_updates
	");
	
	$out['attributes']['last'] = intval ($last[0]['laatste']);
}

else
{
	$q = $db->query
	("
		SELECT
			*
		FROM
			n_map_updates
		WHERE
			mu_id > {$ll}
	");

	$last = $ll;

	$out['attributes']['last'] = $last;
	$out['update'] = array ();

	foreach ($q as $v)
	{
		//$this->alert ('reloading ' . $v['mu_x'] . ',' . $v['mu_y']);
		if ($last < $v['mu_id'])
		{
			$last = $v['mu_id'];
		}

		$out['update'][] = $v;
	}
}

echo Neuron_Core_Tools::output_xml (array ('updates' => $out));