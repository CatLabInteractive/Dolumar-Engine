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

$_POST['regions'] = array
(
	array 
	(
		'x' => 0,
		'y' => 0,
		'z' => 0,
		'radius' => 100000
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