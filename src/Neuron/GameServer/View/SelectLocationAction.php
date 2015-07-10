<?php
class Neuron_GameServer_View_SelectLocationAction
{
	private $sendformdata = false;
	private $value;
	private $sprite;

	public function __construct ($data, $value, Neuron_GameServer_Map_Display_Sprite $sprite = null)
	{
		$this->value = $value;
		if (isset ($sprite))
		{
			$this->setSprite ($sprite);
		}
	}
	
	public function setSendFormData ()
	{
		$this->sendformdata = true;
	}
	
	public function setSprite (Neuron_GameServer_Map_Display_Sprite $sprite)
	{
		$this->sprite = $sprite;
	}
	
	public function getAction ()
	{
		$data = htmlentities (json_encode ($this->data), ENT_COMPAT);
		
		$image = null;
		if ($this->sprite)
			$image = htmlentities (json_encode ($this->sprite), ENT_COMPAT);
		
		$sendformdata = $this->sendformdata ? 'true' : 'false';
	
		return 'selectLocation (this, '.$data.', '.$sendformdata.', '.$image.');';
	}
	
	public function getHTML ()
	{
		$data = htmlentities (json_encode ($this->data), ENT_COMPAT);
		
		$image = null;
		if ($this->sprite)
			$image = htmlentities (json_encode ($this->sprite->getDisplayData ()), ENT_COMPAT);
		
		$sendformdata = $this->sendformdata ? 'true' : 'false';
	
		return '<a href="javascript:void(0);" ' .
			'onclick="'.$this->getAction ().'">' . $this->value . '</a>';
	}
	
	public function __toString ()
	{
		return $this->getHTML ();
	}
}
?>
