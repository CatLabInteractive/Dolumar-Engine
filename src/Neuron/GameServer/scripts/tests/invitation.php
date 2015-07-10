<?php
define ('NOTIFICATION_DEBUG', true);

//echo '<h1>With sender & private</h1>';
//$player->sendNotification ('test', 'system', array ('user' => $other), $other);

$plid = Neuron_Core_Tools::getInput ('_GET', 'id', 'int', 0);

$player = $plid > 0 ? Dolumar_Players_Player::getFromId ($plid) : false;

if ($player)
{
	sendInvitation ($player);
}
else
{
	echo '<p style="color: red;">User not found! Append id=USER_ID to the URL.</p>';
}

function sendInvitation ($player)
{	
	$player->invitePeople 
	(
		'testinvitesender', 
		'system', 
		'testinvitereceiver',
		'system'
	);
	
	echo '<p style="color: green;">A notification has been sent to user '.$player->getName ().'.</p>';
}

?>
