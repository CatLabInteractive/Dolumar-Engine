<?php
class Neuron_GameServer_Windows_Session 
	extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{		
		$this->setAllowOnlyOnce ();
		$this->setTitle ('Session');
	}
	
	public function getContent ()
	{	
		return '<pre>' . print_r ($_SESSION, true) . '</pre>';
	}
}
?>
