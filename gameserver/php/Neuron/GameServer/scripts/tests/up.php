<?php

$start = 1000;
$end = 2000;

$movement = new Neuron_GameServer_Map_Movement 
(
	$start, 
	$end, 
	new Neuron_GameServer_Map_Location (0, 0, 0),
	new Neuron_GameServer_Map_Location (10, 0, 0),
	new Neuron_GameServer_Map_Location (0, 5, 5),
	new Neuron_GameServer_Map_Location (-100, 0, 10)
);

$movement->setUp (new Neuron_GameServer_Map_Vector3 (0, 1, 0), new Neuron_GameServer_Map_Vector3 (0, 0, 1));

echo '<table>';

echo '<tr>';
echo '<td>T</td>';
echo '<td>Loc</td>';
echo '<td>UP</td>';
echo '<td>Direction</td>';
echo '</td>';

for ($i = $start; $i <= $end; $i += 100)
{
	echo '<tr>';
	echo '<td>' . $i . '</td>';
	echo '<td>' . $movement->getCurrentLocation ($i) . '</td>';
	echo '<td>' . $movement->getCurrentUp ($i) . '</td>';
	echo '<td>' . $movement->getCurrentDirection ($i) . '</td>';
	echo '</tr>';
}
echo '</table>';