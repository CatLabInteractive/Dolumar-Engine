<?php

$village = Dolumar_Registry_Village::getInstance ()->get (1);
echo $village->getName () . '<br />';

$village = Dolumar_Registry_Village::getInstance ()->get (1);
echo $village->getName () . '<br />';

Dolumar_Registry_Village::getInstance ()->destroy (1);
echo $village->getName () . '<br />';

Dolumar_Registry_Village::getInstance ()->destroy (1);


$village = Dolumar_Registry_Village::getInstance ()->get (2);
echo $village->getName () . '<br />';

?>
