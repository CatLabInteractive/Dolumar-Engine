<?php
class Neuron_GameServer_Windows_Iframe 
	extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();

		$data = $this->getRequestData ();

		$center = isset ($data['centered']) ? $data['centered'] : true;
		$width = isset ($data['width']) ? $data['width'] . 'px' : '600px';
		$height = isset ($data['height']) ? $data['width'] . 'px' : '500px';
		$title = isset ($data['title']) ? $data['title'] : null;
	
		// Window settings
		$this->setSize ($width, $height);
		$this->setTitle ($title);

		$this->setClass ('small-border no-overflow');

		if ($center)
		{
			$this->setCentered ();
		}
	}
	
	public function getContent ()
	{
		$data = $this->getRequestData ();
		
		$url = $data['url'];

		return '<iframe src="'.$url.'" style="width: 100%; height: 100%; border: 0px none black;" border="0"></iframe>';
	}
	
	public function reloadContent ()
	{
	
	}
}
?>
