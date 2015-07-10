<?php
/*
	This class constructs a notification and sends it to the server.
*/
class BrowserGamesHub_Invitation extends BBGS_Invite
{
	public function __construct ($senderMessage, $receiverMessage, $maxPerInterval = 1, $maxReceiverPerInterval = 0, $interval = 604800, $language = 'en')
	{
		parent::__construct ($senderMessage, $receiverMessage, $maxPerInterval, $maxReceiverPerInterval, $interval, $language);
		
		$this->setPrivateKey (file_get_contents (CATLAB_BASEPATH . 'php/Neuron/GameServer/certificates/credits_private.cert'));
		//$this->setPrivateKey ('bla bla bla');
	}
}