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
