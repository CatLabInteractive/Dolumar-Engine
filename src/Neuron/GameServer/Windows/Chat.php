<?php

class Neuron_GameServer_Windows_Chat 
	extends Neuron_GameServer_Windows_BaseChat
{
	protected function additionalSettings ()
	{
		$this->setPosition (null, 40, 15);
	}

	protected function getTitle ()
	{
		return 'General chat';
	}

	protected function getHeader ()
	{
		return '<p>Please respect the chat rules.</p>';
	}

	protected function getChannelName ()
	{
		$language = Neuron_Core_Text::getInstance ();
		return 'public:' . $language->getCurrentLanguage ();
	}
}