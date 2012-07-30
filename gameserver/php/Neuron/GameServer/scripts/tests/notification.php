<?php
define ('NOTIFICATION_DEBUG', true);

//echo '<h1>With sender & private</h1>';
//$player->sendNotification ('test', 'system', array ('user' => $other), $other);

$plid = Neuron_Core_Tools::getInput ('_GET', 'id', 'int', 0);
$action = Neuron_Core_Tools::getInput ('_GET', 'action', 'varchar', false);

$player = $plid > 0 ? Dolumar_Players_Player::getFromId ($plid) : false;

if ($player)
{
	echo '<h1>How do you want to send to '.$player->getNickname ().'?</h1>';
	echo '<ul>';
	echo '<li><a href="'.ABSOLUTE_URL.'test/notification?action=privatesender&id='.$plid.'">Private with sender</a> (<strong>notification</strong>)</li>';
	echo '<li><a href="'.ABSOLUTE_URL.'test/notification?action=privatenone&id='.$plid.'">Private without sender</a> (<strong>notification</strong>)</li>';
	echo '<li><a href="'.ABSOLUTE_URL.'test/notification?action=publicsender&id='.$plid.'">Public with sender</a> (<strong>news</strong>)</li>';
	echo '<li><a href="'.ABSOLUTE_URL.'test/notification?action=publicnone&id='.$plid.'">Public without sender</a> (<strong>news</strong>)</li>';
	echo '</ul>';

	switch ($action)
	{
		case 'privatesender':
			sendNotification ($player->getId (), true, false);
		break;
	
		case 'privatenone':
			sendNotification ($player->getId (), false, false);
		break;
	
		case 'publicsender':
			sendNotification ($player->getId (), true, true);
		break;
	
		case 'publicnone':
			sendNotification ($player->getId (), false, true);
		break;
	}
}
else
{
	echo '<p style="color: red;">User not found! Append id=USER_ID to the URL.</p>';
}

function sendNotification ($id, $sender, $public)
{
	echo '<h1>';
	echo $sender ? 'With sender' : 'Without sender';
	echo ' and ';
	echo $public ? 'public' : 'private';
	echo '</h1>';
	
	$player = Dolumar_Players_Player::getFromId ($id);
	$other = Dolumar_Players_Player::getFromId (Neuron_Core_Tools::getInput ('_GET', 'from', 'int', 1));
	
	if ($sender)
	{
		echo '<p>Message was sent from '.$other->getNickname ().'.</p>';
	}

	$player->sendNotification 
	(
		'test', 
		'system', 
		array 
		(
			'target' => $other,
			'defender' => $other->getMainVillage (),
			'pl_defender' => $other,
			'village' => $player->getMainVillage (),
			'player' => $player
		), 
		$sender ? $other : null, 
		$public
	);
	
	echo '<p style="color: green;">A notification has been sent to user '.$player->getName ().'.</p>';
}

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

?>
