<?php
class Neuron_GameServer_Windows_Tutorial extends Neuron_GameServer_Windows_Help
{

	public function setSettings ()
	{
	
		$text = Neuron_Core_Text::__getInstance ();
	
		// Window settings
		$this->setSize ('250px', '250px');
		$this->setTitle ($text->get ('tutorial', 'menu', 'main'));
		
		$this->setClassName ('help');
		
		$this->setNoClose ();
		
		$this->setPosition (null, 40, 15);
		
		//$this->setAllowOnlyOnce ();
	}
	
	public function getFooter ()
	{
		$out = '<p class="navigation">Page ';
		
		// Count the pages
		$pagecount = $this->getTutorialPageCount ();
		
		$pages = array ();
		for ($i = 1; $i <= $pagecount; $i ++)
		{
			$pages[$i] = $this->prefix.'/Tutorial'.$i;
		}
		$pages['Close'] = 'window:close';
		
		foreach ($pages as $k => $v)
		{
			$out .= $this->getLink ($v, $k, trim ($this->page) == trim ($v)).' ';
		}
		
		$out .= '</p>';
		
		return $out;
	}
	
	private function getTutorialPageCount ()
	{
		$cache = Neuron_Core_Memcache::getInstance ();
		
		if (!$count = $cache->getCache ('tutorial_count_cache'))
		{
			$i = 1;
		
			while ($this->hasContent ($this->prefix.'/Tutorial'.($i + 1)))
			{
				$i ++;
			}
			
			$count = $i;
			
			$cache->setCache ('tutorial_count_cache', $count);
		}
		
		return $count;
	}
	
	protected function getCloseWindow ()
	{
		$input = $this->getInputData ();
		
		$text = Neuron_Core_Text::__getInstance ();
		
		if (isset ($input['confirm']) && $input['confirm'])
		{
			$player = Neuron_GameServer::getPlayer ();
			if ($player)
			{
				$player->setPreference ('closeTutorial', 1);
			}
		
			$this->closeWindow ();
		}
		else
		{
			$out = '<p>'.$text->get ('closeTutorial', 'help', 'main').'</p>';
		
			$out .= '<p><a href="javascript:void(0);" '.
				'onclick="windowAction (this, {\'page\':\'window:close\',\'confirm\':1})">'.
				$text->get ('closeTutorialBtn', 'help', 'main').'</a></p>';
				
			return $out;
		}
	}
	
	/*
		Due to netlog translation services...
		We are going to put the tutorial in 
		the language files.
		
		Muchos betteros, no?
	*/
	protected function getHelpFile ($sFile)
	{
		$sFile = substr ($sFile, strlen ($this->prefix) + 1);
	
		$page = new Neuron_Core_Template ();
		
		$filename = 'tutorial/'.$sFile.'.phpt';
		
		if ($page->hasTemplate ($filename))
		{
			return $page->parse ($filename);
		}
		else
		{
			return null;
		}
	}
}
