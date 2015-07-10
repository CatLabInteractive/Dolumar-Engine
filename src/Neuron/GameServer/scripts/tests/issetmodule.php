<?php

$village = Dolumar_Players_Village::getVillage (1);

if ($village->moduleExists ('buildings'))
{
	echo 'yep';
}
else
{
	echo 'nope';
}

?>
