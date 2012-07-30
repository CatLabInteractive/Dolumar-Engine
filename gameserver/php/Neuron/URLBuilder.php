<?php
class Neuron_URLBuilder
{
	public static function getInstance ()
	{
		static $in;
	
		if (!isset ($in))
		{
			$in = new self ();
		}
		
		return $in;
	}
	
	private $opencallback;
	private $updatecallback;
	
	public function __construct ()
	{
		$this->setOpenCallback (array ($this, '_buildUrl'));
		$this->setUpdateCallback (array ($this, '_buildUrl'));
	}
	
	public function setCallback ($callback)
	{
		$this->setOpenCallback ($callback);
	}
	
	public function setOpenCallback ($callback)
	{
		$this->opencallback = $callback;
	}
	
	public function setUpdateCallback ($callback)
	{
		$this->updatecallback = $callback;
	}
	
	private function _buildUrl ($module, $display, $data, $title = null)
	{
		$query = '?';
		
		foreach ($data as $k => $v)
		{
			$query .= $k . '=' . urlencode ($v) . '&';
		}
		
		$query = substr ($query, 0, -1);
	
		return '<a href="'.ABSOLUTE_URL . $module . $query . '">'.$display.'</a>';
	}
	
	public function getURL ($module, $data, $display, $title =  null)
	{
		return $this->getUrl ($module, $display, $data, $title);	
	}
	
	public function getOpenUrl ($module, $display, $data, $title = null, $misc1 = null)
	{
		return call_user_func ($this->opencallback, $module, $display, $data, $title, $misc1);
	}
	
	public function getUpdateUrl ($module, $display, $data, $title = null)
	{
		return call_user_func ($this->updatecallback, $module, $display, $data, $title);
	}
}
?>
