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

class Neuron_Core_Memcache
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
	
	private $objCache;
	
	// Small cache to speed up issues
	private $sLastCache;
	private $sLastValue;
	
	private function __construct ()
	{
		if (defined ('MEMCACHE_SERVERS'))
		{
			try
			{
				// create a new persistent client
				$m = new Memcached("memcached_pool");
				$m->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);

				// some nicer default options
				$m->setOption(Memcached::OPT_NO_BLOCK, TRUE);
				$m->setOption(Memcached::OPT_AUTO_EJECT_HOSTS, TRUE);
				$m->setOption(Memcached::OPT_CONNECT_TIMEOUT, 2000);
				$m->setOption(Memcached::OPT_POLL_TIMEOUT, 2000);
				$m->setOption(Memcached::OPT_RETRY_TIMEOUT, 2);

				// setup authentication
				if (defined ('MEMCACHE_USERNAME') && defined ('MEMCACHE_PASSWORD')) {
					$m->setSaslAuthData( MEMCACHE_USERNAME, MEMCACHE_PASSWORD );
				}

				// We use a consistent connection to memcached, so only add in the
				// servers first time through otherwise we end up duplicating our
				// connections to the server.
				if (!$m->getServerList()) {
					// parse server config
					$servers = explode(",", MEMCACHE_SERVERS);
					foreach ($servers as $s) {
						$parts = explode(":", $s);
						if (!isset($parts[1])) {
							$parts[1] = 11211;
						}
						$m->addServer($parts[0], $parts[1]);
					}
				}
			}
			catch (Exception $e)
			{
				$this->objCache = false;
			}
		}
		else
		{
			$this->objCache = false;
		}
	}
	
	public function hasCache ($sKey)
	{
		// Check if this object even exists
		if (!$this->objCache)
		{
			return false;
		}
	
		$this->sLastCache = $sKey;
		$this->sLastValue = @$this->objCache->get ($sKey);
		
		return $this->sLastValue !== false;
	}
	
	public function setCache ($sKey, $sContent)
	{
		// Check if this object even exists
		if (!$this->objCache)
		{
			return false;
		}
	
		return @$this->objCache->set ($sKey, $sContent, 0, 0);
	}
	
	public function getCache ($sKey)
	{
		// Check if this object even exists
		if (!$this->objCache)
		{
			return false;
		}
	
		if ($this->sLastCache == $sKey)
		{
			return $this->sLastValue;
		}
		else
		{
			return @$this->objCache->get ($sKey);
		}
	}
	
	public function removeCache ($sKey)
	{
		if (!$this->objCache)
		{
			return false;
		}
		
		//customMail ('daedelson@gmail.com', 'deleting cache '.$sKey, 'test');
		
		$this->objCache->delete ($sKey);
	}
	
	public function flush ()
	{
		// Check if this object even exists
		if (!$this->objCache)
		{
			return false;
		}
		
		//customMail ('daedelson@gmail.com', 'clearing all cache', 'test');
		
		$this->objCache->flush ();
	}
	
	public function __toString ()
	{
		if ($this->objCache)
		{
			return print_r ($this->objCache->getExtendedStats (), true);
		}
		else
		{
			return 'No memcache support configured.';
		}
	}
}
?>
