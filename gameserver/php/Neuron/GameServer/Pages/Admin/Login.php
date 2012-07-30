<?php
class Neuron_GameServer_Pages_Admin_Login extends Neuron_GameServer_Pages_Admin_Page
{
	private $login;

	public function __construct (Neuron_Core_Login $login)
	{
		$this->login = $login;
	}
	
	public function getOuterBody ()
	{
		$username = Neuron_Core_Tools::getInput ('_POST', 'username', 'varchar');
		$password = Neuron_Core_Tools::getInput ('_POST', 'password', 'varchar');
	
		$page = new Neuron_Core_Template ();
	
		if ($username && $password)
		{
			$chk = $this->login->login ($username, $password, false);
			if ($chk)
			{
				$url = $this->getUrl ('index');
				header ('Location: '.$url);
				return '<p>Welcome! Click <a href="'.$url.'">here</a> to continue.</p>';
			}
			else
			{
				$page->set ('error', $this->login->getError ());
			}
		}
		
		$page->set ('action', '');
		return $page->parse ('pages/login.phpt');
	}
}
?>
