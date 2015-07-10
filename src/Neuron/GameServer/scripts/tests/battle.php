<?php

$village1 = new Dolumar_Players_DummyVillage ();
$village2 = new Dolumar_Players_DummyVillage ();

$slots = $village1->getAttackSlots ($village2);

$attacking = array ();
$defending = array ();

$logger = new Dolumar_Battle_Logger ();

foreach ($slots as $k => $v)
{
	$tmp = new Dolumar_Units_Archers ($village1);
	$tmp->setBattleSlot ($v);
	$tmp->addAmount (5000, 5000, 5000);
	$attacking[$k] = $tmp;
	
	
	
	$tmp = new Dolumar_Units_Archers ($village2);
	$tmp->setBattleSlot ($v);
	$tmp->addAmount (5000, 5000, 5000);
	$defending[$k] = $tmp;
}

// Let's do the fight
$fight = new Dolumar_Battle_Fight
(
	$village1,
	$village2,
	$attacking,
	$defending,
	$slots,
	array (),
	$logger
);

echo '<h2>Combat result</h2>';
echo $fight->getResult ();

echo '<h2>Debugger</h2>';
echo '<pre>'.$logger->getDebugLog ().'</pre>';

unset ($fight);
unset ($village1);
unset ($village2);

unset ($attacking);
unset ($defending);
unset ($logger);

echo '<h2>Profiler</h2>';
$profiler = Neuron_Profiler_Profiler::getInstance ();

//$profiler->stop ();

echo '<pre>'.$profiler.'</pre>';

?>
