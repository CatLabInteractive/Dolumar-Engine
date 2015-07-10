<?php
class Neuron_GameServer_Pages_Admin_Searchplayers extends Neuron_GameServer_Pages_Admin_Page
{
	public function getBody ()
	{
		$search = new Dolumar_Windows_Search ();
		$search->setPerPages (50);
		$search->setInputData (array_merge ($_POST, $_GET));
		return $search->getContent ();
	}
}
?>
