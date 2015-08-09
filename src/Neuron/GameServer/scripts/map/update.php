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