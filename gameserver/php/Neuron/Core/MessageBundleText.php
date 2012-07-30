<?php
class Neuron_Core_MessageBundleText extends Neuron_Core_Text
{
	private $sUrl;
	private $sName;

	public function __construct ($sUrl)
	{
		$this->sUrl = $sUrl;
		$this->sName = md5 ($sUrl);
		
		parent::__construct ();
	}

	protected function load_file ($file)
	{
		$cache = Neuron_Core_Cache::getInstance ('language/'.$this->sName.'/');
		
		if ($data = $cache->getCache ($file))
		{
			$this->cache[$file] = unserialize ($data);
		}
		else
		{
			Neuron_Core_MessageBundle::bundle2text ($this->sUrl, $this->sName);
			
			// Now check again. If it not exists, enter an empty array.
			if ($data = $cache->getCache ($file))
			{
				$this->cache[$file] = unserialize ($data);
			}
			else
			{
				$cache->setCache ($file, serialize (array ()));
			}
		}
	}
}
?>
