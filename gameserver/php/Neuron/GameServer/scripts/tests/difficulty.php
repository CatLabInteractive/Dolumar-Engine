<?php

$spell = new Dolumar_Effects_Battle_Fireball ();

$objUnit = new Dolumar_SpecialUnits_Mages ();
$village = new Dolumar_Players_Village (1);

echo $spell->countSpecialUnits ($objUnit, $village);

?>
