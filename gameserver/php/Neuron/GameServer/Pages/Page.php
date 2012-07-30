<?php
class Neuron_GameServer_Pages_Page
{
	/*
		Return the HTML
	*/
	public function getOutput ()
	{
		return $this->getHTML ();
	}
	
	public function getHTML ()
	{
		header("Content-Type: text/html; charset=UTF-8");
	
		$page = new Neuron_Core_Template ();
		$page->set ('body', $this->getOuterBody ());
		$page->set ('stylesheet', 'page');
		$page->set ('static_client_url', '');
		
		foreach ($this->getJavascript () as $v)
		{
			$page->addListValue ('javascripts', $v);
		}
		
		return $page->parse ('pages/index.phpt');
	}
	
	/*
		Return the whole body of the template.
	*/
	public function getOuterBody ()
	{
		$page = new Neuron_Core_Template ();
		
		$page->set ('body', $this->getBody ());
		
		return $page->parse ('pages/body.phpt');
	}
	
	public function getUrl ($sUrl, $sArrs = array (), $sBase = 'page/')
	{
		if (!isset ($_COOKIE['session_id']))
		{
			$sArrs['session_id'] = session_id ();
		}
	
		$out = ABSOLUTE_URL.$sBase.$sUrl;
		if (count ($sArrs) > 0)
		{
			$out .= '?';
			foreach ($sArrs as $k => $v)
			{
				$out .= $k .'='.urlencode ($v).'&';
			}
			$out = substr ($out, 0, -1);
		}
		return $out;
	}
	
	protected function getParameter ($id)
	{
		$id = intval ($id);
	
		$module = Neuron_Core_Tools::getInput ('_REQUEST', 'module', 'varchar');
		$data = explode ('/', $module);

		if (isset ($data[$id]))
		{
			return $data[$id];
		}
		
		return null;
	}
	
	protected function getJavascript ()
	{
		return array ('admin');
	}
	
	/*
		Return the body of the page
	*/
	public function getBody ()
	{
		return null;
	}
}
?>
