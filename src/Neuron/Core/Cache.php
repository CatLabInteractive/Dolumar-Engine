<?php
/**
 *  Dolumar engine, php/html MMORTS engine
 *  Copyright (C) 2009 Thijs Van der Schaeghe
 *  CatLab Interactive bvba, Gent, Belgium
 *  http://www.catlab.eu/
 *  http://www.dolumar.com/
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

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
