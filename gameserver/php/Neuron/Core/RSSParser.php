<?php
class Neuron_Core_RSSParser
{
	private $sUrl;
	private $oCache;
	private $iCacheLifespan;

	public function __construct ($sUrl)
	{
		$this->sUrl = $sUrl;
	}
	
	public function setCache ($cache, $iCacheLifespan = 3600)
	{
		$this->oCache = $cache;
		$this->iCacheLifespan = $iCacheLifespan;
	}
	
	public function getItems ($amount)
	{
		$name = md5 ($this->sUrl . '|' . $amount);
		
		if (isset ($this->oCache))
		{
			if ($out = $this->oCache->getCache ($name, $this->iCacheLifespan))
			{
				return unserialize ($out);
			}
			
			$data = $this->getFreshItems ($amount);
			$this->oCache->setCache ($name, serialize ($data));
			
			return $data;
		}
		
		return $this->getFreshItems ($amount);
	}
	
	private function getFreshItems ($amount)
	{
		$out = array ();
	
		$dom = @DOMDocument::load ($this->sUrl);	
		if ($dom)
		{
			$rss = $dom->getElementsByTagName ('rss');
		
			if ($rss->length > 0)
			{
				$channel = $rss->item (0)->getElementsByTagName ('channel');
				if ($channel->length > 0)
				{
					$items = $channel->item (0)->getElementsByTagName ('item');
				
					for ($i = 0; $i < $items->length && $i < $amount; $i ++)
					{
						$item = $items->item ($i);
				
						$out[] = array
						(
							'title' => $this->fetchValue ($item, 'title'),
							'url' => $this->fetchValue ($item, 'link'),
							'description' => $this->fetchValue ($item, 'description'),
							'date' => strtotime ($this->fetchValue ($item, 'pubDate'))
						);
					}
				}
			}
		}
		
		return $out;
	}
	
	private function fetchValue ($item, $name)
	{
		$element = $item->getElementsByTagName ($name);
		if ($element->length > 0)
		{
			return $element->item (0)->nodeValue;
		}
		return null;
	}
}
?>
