<?php

$villageid = Neuron_Core_Tools::getInput ('_GET', 'village', 'int');
$targetid = Neuron_Core_Tools::getInput ('_GET', 'target', 'int');

$village = Dolumar_Players_Village::getVillage ($villageid);
$target = Dolumar_Players_Village::getVillage ($targetid);

if ($villageid && $targetid && $village && $target)
{
	echo '<pre>';
	$percentage = Dolumar_Battle_Battle::getMaxStolenRunesPercentage ($target, true);
	
	echo '100% victory results in ' . $percentage . '% stolen runes.';
	echo '</pre>';
	
	echo '<pre>';
	
	$clanlose = Dolumar_Battle_Battle::didClanAttackTargetEarlier ($village, $target);
	
	if ($clanlose)
	{
		echo "Target clan will lose honour.\n";
	}
	else
	{
		echo "Target clan will not lose honour.\n";
	}
	echo '</pre>';
}
else
{
	echo '<p>Please provide village and target parameter</p>';
}

?>
