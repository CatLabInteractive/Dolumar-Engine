<?php
class Neuron_GameServer_Pages_Admin_Invalid extends Neuron_GameServer_Pages_Page
{
	public function getBody ()
	{
		return '<p class="false"><strong>Invalid input:</strong> You are not authorized to access this page.</p>';
	}
}
?>
