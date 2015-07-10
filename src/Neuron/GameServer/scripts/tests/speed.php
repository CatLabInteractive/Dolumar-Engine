<?php

$movement = new Neuron_GameServer_Map_SpeedMovement
(
	NOW,
	100,
	10,
	new Neuron_GameServer_Map_Location (0, 0, 0),
	new Neuron_GameServer_Map_Location (10, 0, 0)
);

echo '<pre>';
print_r ($movement->getData ());
echo '</pre>';