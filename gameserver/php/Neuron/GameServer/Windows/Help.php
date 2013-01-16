<?php

class Neuron_GameServer_Windows_Help extends Neuron_GameServer_Windows_Window
{
	protected $prefix;
	protected $page;
	
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
		$this->prefix = WIKI_PREFIX.strtoupper ($text->getCurrentLanguage ());

		$text = Neuron_Core_Text::__getInstance ();
	
		// Window settings
		$this->setSize ('300px', '350px');
		$this->setTitle ($text->get ('help', 'menu', 'main'));
		
		$this->setClassName ('help');
		
		//$this->setAllowOnlyOnce ();
	}
	
	public function getAdditionalContent ($page)
	{
		return null;
	}
	
	public function getContent ($update = false, $isBack = false)
	{
		$text = Neuron_Core_Text::__getInstance ();
	
		if (!$update)
		{
			$_SESSION['help_history'] = array ();
		}
		
		elseif ($isBack)
		{
			array_pop ($_SESSION['help_history']);
		}
	
		$page = $this->getPageFromInput ();
		$this->page = $page;
		
		if (substr ($this->page, 0, strlen ('window:')) == 'window:')
		{
			$html = $this->getWindowAction (substr ($this->page, strlen ('window:')));
		}
		else
		{
			//$html = Neuron_Core_Wiki::parseHelpFile ($page);
			$html = $this->getHelpFile ($page);
			
			if (!$html)
			{
				$edit = WIKI_EDIT_URL.$page;
				$html = '<p>404: Not Found (' . $page . ').<br />Click <a href="' . $edit.'" target="wiki">here</a> to edit.</p>';
			}
		}
		
		// *************************
		// Additional navigation
		// *************************
		
		// Fetch the "back" button
		if (!$isBack)
		{
			$_SESSION['help_history'][] = $page;
		}
		
		$html .= $this->getAdditionalContent ($page);
		
		if (count ($_SESSION['help_history']) > 1 || $page != $this->prefix.'/Index')
		{
			$html .= $this->getFooter ();
		}
		
		return $html;
	}
	
	protected function getHelpFile ($sPage)
	{
		return Neuron_Core_Wiki::parseHelpFile ($sPage);
	}
	
	protected function getWindowAction ($sAction)
	{
		switch ($sAction)
		{
			case 'close':
				return $this->getCloseWindow ();
			break;
		
			default:
				return '<p>Invalid input: window action not found.</p>';
			break;
		}
	}
	
	protected function getCloseWindow ()
	{
		$this->closeWindow ();
		return '<p>Closing window...</p>';
	}
	
	protected function getFooter ()
	{
		$text = Neuron_Core_Text::__getInstance ();
	
		$html = '<p class="navigation">';
	
		if (count ($_SESSION['help_history']) > 1)
		{
			$backPage = $_SESSION['help_history'][count ($_SESSION['help_history']) - 2];			
			$html .= $this->getLink ($backPage, $text->get ('previous', 'help', 'main'));
		}
	
		if ($this->page != $this->prefix.'/Index')
		{
			if (count ($_SESSION['help_history']) > 1)
			{
				$html .= ' | ';
			}
				
			$html .= $this->getLink ($this->prefix.'/Index', $text->get ('home', 'help', 'main'));
		}
	
		$html .= '</p>';
		
		return $html;
	}
	
	protected function getLink ($sPage, $sName, $bSelected = false)
	{
		return '<a href="javascript:void(0);" '.($bSelected?'class="selected" ':null).
			'onclick="windowAction(this,{\'page\':\''.$sPage.'\'});">'.
			$sName.'</a>';
	}
	
	private function getPageFromInput ()
	{
		$data = $this->getInputData ();
		$text = Neuron_Core_Text::__getInstance ();
		
		if (isset ($data['page']))
		{
			$page = $data['page'];
		}
		
		else
		{
			// Fetch request data
			$req = $this->getRequestData ();
			$prefix = WIKI_PREFIX.strtoupper($text->getCurrentLanguage ());
			
			if (!isset ($req['page']))
			{
				$page = $prefix.'/'.$this->getDefaultPage ();
			}
			else
			{
				$page = $prefix.'/'.$req['page'];
			}
		}
		return $page;
	}
	
	public function getDefaultPage ()
	{
		return 'Index';
	}
	
	public function processInput ()
	{
		$input = $this->getInputData ();
		
		if 
		(
			isset ($input['back']) && 
			$input['back'] == 'back'
		)
		{
			$this->updateContent ($this->getContent (true, true));
		}
	
		else
		{
			$this->updateContent ($this->getContent (true));
		}
	}
	
	protected function hasContent ($sPage)
	{
		$data = $this->getHelpFile ($sPage);
		return !empty ($data);
	}
	
	public function reloadContent ()
	{
		// Do nothing
	}

}

?>
