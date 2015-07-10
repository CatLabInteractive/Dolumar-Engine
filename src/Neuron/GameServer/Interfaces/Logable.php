<?php
interface Neuron_GameServer_Interfaces_Logable
{
	public static function getFromId ($id);
	public function getName ();
	public function getId ();
	public function getLogArray ();
	public function getDisplayName ();
	
	public function __toString ();
}
?>
