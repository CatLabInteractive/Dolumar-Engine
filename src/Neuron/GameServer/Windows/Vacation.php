<?php
class Neuron_GameServer_Windows_Vacation extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
		$this->setTitle ($text->get ('title', 'vacationMode', 'account'));
		
		$this->setAllowOnlyOnce ();
	}
	
	public function getContent ()
	{
		$myself = Neuron_GameServer::getPlayer ();
		if ($myself->inVacationMode ())
		{
			return $this->inVacationMode ();
		}
		else
		{
			return $this->getStartVacation ();
		}
	}
	
	private function getStartVacation ()
	{
		$myself = Neuron_GameServer::getPlayer ();
	
		$input = $this->getInputData ();
		
		$page = new Neuron_Core_Template ();
		
		if (isset ($input['confirm']) && Neuron_Core_Tools::checkConfirmLink ($input['confirm']))
		{
			if ($myself->startVacationMode ())
			{
				$page->set ('done', true);
			}
			else
			{
				$page->set ('done', false);
				$page->set ('error', $myself->getError ());
			}
		}
		else
		{
			$page->set ('done', false);
		}
	
		$page->set ('checkkey', Neuron_Core_Tools::getConfirmLink ());
		
		return $page->parse ('gameserver/account/vacationMode.phpt');
	}
	
	private function inVacationMode ()
	{
		$myself = Neuron_GameServer::getPlayer ();
		
		$input = $this->getInputData ();
		
		$page = new Neuron_Core_Template ();
		if (isset ($input['disable']))
		{
			if ($myself->endVacationMode ())
			{
				$page->set ('success', true);
			}
			else
			{
				$page->set ('success', false);
				$page->set ('error', $myself->getError ());
			}
		}
		
		// Get "since"
		$page->set ('since', date (DATETIME, $myself->getVacationStart ()));
		
		return $page->parse ('gameserver/account/vacationModeActive.phpt');
	}
}
?>
