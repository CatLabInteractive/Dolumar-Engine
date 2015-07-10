<?php
class Neuron_GameServer_Logable_String implements Neuron_GameServer_Interfaces_Logable
{
	private $string;

	public function __construct ($string)
	{
		$this->string = $string;
	}
	
	public static function getFromId ($id)
	{
		return new self ($id);
	}
	
	
	public function getName ()
	{
		return $this->getDisplayName ();
	}
	
	// Get the serialized object
	public function getId ()
	{
		return $this->string;
	}
	
	public function getLogArray ()
	{
		return array ('value' => $this->string);
	}
	
	public function getDisplayName ()
	{
		return $this->string;
	}
	
	public function __toString ()
	{
		return $this->getDisplayName ();
	}
}
?>
