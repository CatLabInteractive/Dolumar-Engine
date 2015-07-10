<?php
class Neuron_GameServer_Windows_Messages
	extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
	
		// Window settings
		$this->setSize ('500px', '300px');
		$this->setTitle ($text->get ('messages', 'menu', 'main'));
		
		$this->setClass ('messages');
		
		$this->setAllowOnlyOnce ();

		$this->setAjaxPollSeconds (Dolumar_Windows_Newsbar::AJAX_RELOAD_INT);
	}
	
	public function getContent ()
	{
		$text = Neuron_Core_Text::__getInstance ();
		
		$player = Neuron_GameServer::getPlayer ();
		if (!$player)
		{
			return $this->throwError ($text->get ('login', 'login', 'account'));
		}

		$input = $this->getInputData ();
		$request = $this->getRequestData ();

		$mapper = Neuron_GameServer_Mappers_BlockingChatMapper::getInstance ();

		$total = $mapper->countPrivateMessages ($player);

		if (isset ($input['page']))
		{
			$current = $input['page'];
			$request['page'] = $current;
			$this->updateRequestData ($request);
		}
		else
		{
			$current = isset ($request['page']) ? $request['page'] : 0;
		}

		$perpage = 10;

		$page = new Neuron_Core_Template ();

		$pages = Neuron_Core_Tools::splitInPages
		(
			$page, 
			$total, 
			$current, 
			$perpage
		);

		$messages = $mapper->getPrivateChats ($player, $pages['start'], $pages['perpage']);

		$page->set ('messages', $messages);

		return $page->parse ('gameserver/chat/privatemessages.phpt');
	}
	
	public function processInput ()
	{
		$this->updateContent ();
	}

	public function getRefresh ()
	{
		$this->updateContent ();
	}
}