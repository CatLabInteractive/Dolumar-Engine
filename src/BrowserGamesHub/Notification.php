<?php
/*
	This class constructs a notification and sends it to the server.
*/
class BrowserGamesHub_Notification extends BBGS_Notification
{
	public function __construct ($sMessage, $iTimestamp = null, $language = 'en')
	{
		parent::__construct ($sMessage, $iTimestamp, $language);
		
		$this->setPrivateKey (file_get_contents (CATLAB_BASEPATH . 'php/Neuron/GameServer/certificates/credits_private.cert'));
		//$this->setPrivateKey ('bla bla bla');
	}
}