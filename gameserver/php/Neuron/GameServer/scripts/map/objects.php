<?php

$_POST['regions'] = array
(
	array 
	(
		'x' => 0,
		'y' => 0,
		'z' => 0,
		'radius' => 10000
	)
);

$out = array ();
$out['regions'] = array ();

if (isset ($_POST['regions']) && is_array ($_POST['regions']))
{
	$objectManager = $this->getMap ()->getMapObjectManager ();

	$objectManager->clean ();

	foreach ($_POST['regions'] as $v)
	{
		$x = isset ($v['x']) ? $v['x'] : 0;
		$y = isset ($v['y']) ? $v['y'] : 0;
		$z = isset ($v['z']) ? $v['z'] : 0;
		$radius = isset ($v['radius']) ? $v['radius'] : 1000;

		$location = new Neuron_GameServer_Map_Location ($x, $y, $z);
		$rObject = new Neuron_GameServer_Map_Radius ($radius);

		$area = new Neuron_GameServer_Map_Area ($location, $rObject);

		$region = array ();

		$region['attributes'] = array 
		(
			'x' => $x,
			'y' => $y,
			'z' => $z,
			'radius' => $radius
		);

		$region['objects'] = array ();
		foreach ($objectManager->getDisplayObjects ($area) as $v)
		{
			$region['objects'][] = $v->getExportData ();
		}

		$out['regions'][] = $region;
	}
}

echo Neuron_Core_Tools::output_xml ($out);