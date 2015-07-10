<?php
class Neuron_GameServer_Pages_Admin_Index extends Neuron_GameServer_Pages_Admin_Page
{
	public function getBody ()
	{
		$myself = Neuron_GameServer::getPlayer ();
		return '<p>Welcome, '.$myself->getName ().'.</p>';
	}
}
?>
