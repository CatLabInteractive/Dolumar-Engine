<?php
interface Neuron_GameServer_Interfaces_Game
{
	/*
		getWindow SHOULD return a Window object if the name matches.
		If this function returns FALSE (or NOT a Window object), the engine
		will check it's own library for a suiting window.
		
		@param $sWindowName: the name of the window.
	*/
	public function getWindow ($sWindowName);
	
	public function getPlayer ($id);
	
	public function getInitialWindows ($objServer);
	
	public function getMap ();

	/**
	* If this function returns content, the page will be reloaded & the output will be showed;
	*/
	public function getCustomOutput ();
}
?>
