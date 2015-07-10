<?php
/*
	This class constructs a notification and sends it to the server.
*/
class BrowserGamesHub_Statistics extends BBGS_Statistics
{
	public function __construct ($statistics, $information)
	{
		parent::__construct ($statistics, $information);
		
		$this->setPrivateKey (file_get_contents (CATLAB_BASEPATH . 'php/Neuron/GameServer/certificates/credits_private.cert'));
		//$this->setPrivateKey ('bla bla bla');
	}
}