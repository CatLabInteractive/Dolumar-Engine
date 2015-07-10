<?php

$village1 = Neuron_Core_Tools::getInput ('_GET', 'village1', 'int');
$village2 = Neuron_Core_Tools::getInput ('_GET', 'village2', 'int');

$v1 = Dolumar_Players_Village::getVillage ($village1);
$v2 = Dolumar_Players_Village::getVillage ($village2);

echo 'Calculating distance between '.$v1->getName ().' to '.$v2->getName ().'<br />';

$l1 = $v1->buildings->getTownCenterLocation ();
$l2 = $v2->buildings->getTownCenterLocation ();

$distance = Dolumar_Map_Map::getDistanceBetweenVillages ($v1, $v2);
$straight = Dolumar_Map_Map::calculateDistance ($l1[0], $l1[1], $l2[0], $l2[1]);

$profiler = Neuron_Profiler_Profiler::getInstance ();

echo '<h2>Profiler</h2>';
echo '<pre>';
echo $profiler;
echo '</pre>';

echo '<p>Distance: <strong>'.$distance.'</strong><br />';
echo 'Straight line distance: <strong>'.$straight.'</strong></p>';
?>
