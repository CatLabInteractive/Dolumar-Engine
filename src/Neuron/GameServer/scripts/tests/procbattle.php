<?php
$village = Dolumar_Players_Village::getVillage (Neuron_Core_Tools::getInput ('_GET', 'village', 'int'));
$village->processBattles ();
?>
