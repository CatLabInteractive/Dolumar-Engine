<?php
define ('NOTIFICATION_DEBUG', true);

$player = Dolumar_Players_Player::getFromId (Neuron_Core_Tools::getInput ('_GET', 'user', 'int'));

//echo '<h1>With sender & private</h1>';
//$player->sendNotification ('test', 'system', array ('user' => $other), $other);

echo '<h1>Updating profilebox</h1>';

/*
	$pl_attacker->sendNotification 
	(
		'attacking', 
		'battle', 
		array
		(
			'defender' => $oTarget,
			'pl_defender' => $oTarget->getOwner (),
			'village' => $this
		),
		$pl_attacker,
		true // This is a public message
	);
*/

$player->updateScore ();

echo '<p style="color: green;">Players '.$player->getName ().' profilebox has been updated.</p>';

?>
