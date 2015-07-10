<?php
class Neuron_GameServer_Windows_Lostpassword extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
	
		// Window settings
		$this->setSize ('250px', '300px');
		$this->setTitle ($text->get ('lostPassword', 'menu', 'main'));
		
		$this->setAllowOnlyOnce ();
	}
	
	public function getContent ()
	{
		$text = Neuron_Core_Text::__getInstance ();
		$login = Neuron_Core_Login::__getInstance ();
		
		if ($login->isLogin ())
		{
			return $this->throwError ($text->get ('loggedIn', 'lostPassword', 'account'));
		}
		
		// Check for input
		$input = $this->getInputData ();
		
		if (isset ($input['email']))
		{
			// Check if this E-mail is found
			if ($login->sendLostPassword ($input['email']))
			{
				return $this->throwOkay ($text->get ('done', 'lostPassword', 'account'));
			}
			else
			{
				return $this->showForm ($login->getError ());
			}
		}
		else
		{
			return $this->showForm ();
		}
	}
	
	private function showForm ($error = null)
	{
		// Show the form
		$page = new Neuron_Core_Template ();
		return $page->parse ('gameserver/account/lostPass.phpt');
	}
	
	public function processInput ()
	{
		$this->updateContent ();
	}
}
?>
