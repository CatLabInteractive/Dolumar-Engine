<?php

$player = Neuron_GameServer::getPlayer (isset ($_GET['player']) ? $_GET['player'] : 1);

$village = $player->getMainVillage ();

echo $village->getName ();

echo '<pre>';
print_r ($village->resources->getUnitConsumption ());
echo '</pre>';

$dbi = Neuron_DB_Database::getInstance ();

echo $dbi->getQueryCounter ();

?>
