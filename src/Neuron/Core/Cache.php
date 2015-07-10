<?php
class Neuron_Core_Cache
{
	public static function __getInstance ($sDir)
	{
		static $in;
		
		if (!isset ($in))
		{
			$in = array ();
		}
		
		if (!isset ($in[$sDir]))
		{
			$in[$sDir] = new Neuron_Core_Cache ($sDir);
		}
		
		return $in[$sDir];
	}
	
	public static function getInstance ($sDir)
	{
		return self::__getInstance ($sDir);
	}
	
	private $sPath = '';
	
	public function __construct ($sDir)
	{		
		$this->sPath = CACHE_DIR . $sDir;
		
		// Check if cache dir exists & create if not
		$this->touchFolder ($sDir);
	}
	
	public function getFileName ($sKey)
	{
		return $this->sPath . $sKey . '.tmp';
	}
	
	public function touchFolder ($sDir)
	{
		if (!file_exists (CACHE_DIR.$sDir))
		{
			$sCrums = explode ('/', $sDir);
			$sPath = CACHE_DIR;
			foreach ($sCrums as $v)
			{
				$sPath .= $v . '/';
				if (!file_exists ($sPath))
				{
					mkdir ($sPath);
				}
			}
		}
	}
	
	public function hasCache ($sKey, $iLifeSpan = 86400)
	{
		if (file_exists ($this->sPath . $sKey . '.tmp'))
		{
			if ($iLifeSpan == 0 || @filectime ($this->sPath . $sKey . '.tmp') > (time () - $iLifeSpan))
			{
				return true;
			}
			else
			{
				@unlink ($this->sPath . $sKey . '.tmp');
				return false;
			}
		}
		return false;
	}
	
	public function setCache ($sKey, $sContent)
	{
		return file_put_contents ($this->sPath . $sKey . '.tmp', $sContent);
	}
	
	public function appendCache ($sKey, $sContent)
	{
		return file_put_contents ($this->sPath . $sKey . '.tmp', $sContent, FILE_APPEND);
	}
	
	public function getCache ($sKey, $iLifeSpan = 86400)
	{
		if (self::hasCache ($sKey, $iLifeSpan))
		{
			return file_get_contents ($this->sPath . $sKey . '.tmp');
		}
		else
		{
			return false;
		}
	}
}
?>
