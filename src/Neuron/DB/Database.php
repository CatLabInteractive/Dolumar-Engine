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

// Let's just point the database to MySQL
abstract class Neuron_DB_Database
{
	protected $insert_id = 0;
	protected $affected_rows = 0;
	protected $query_counter = 0;
	
	protected $query_log = array ();

	public static function __getInstance ($id = 'general')
	{
		static $in;
		
		if (!isset ($in))
		{
			$in = array ();
		}
		
		if (!isset ($in[$id]))
		{
			$in[$id] = new Neuron_DB_MySQL ();
			$in[$id]->query ("SET names 'utf8'");
			$in[$id]->query ("SET time_zone = '+00:00'");
		}
		
		return $in[$id];
	}
	
	public static function getInstance ()
	{
		return self::__getInstance ();
	}
	
	public function getInsertId ()
	{
		return $this->insert_id;
	}
	
	public function getAffectedRows ()
	{
		return $this->affected_rows;
	}
	
	public function getQueryCounter ()
	{
		return $this->query_counter;
	}
	
	// Abstract functions
	public abstract function multiQuery($sSQL);
	public abstract function query ($sSQL);
	public abstract function escape ($txt);
	
	protected function addQueryLog ($sSQL)
	{
		if (count ($this->query_log) > 50)
		{
			array_shift ($this->query_log);
		}
	
		$this->query_log[] = $sSQL;
	}
	
	public function getLastQuery ()
	{
		return $this->query_log[count ($this->query_log) - 1];
	}
	
	public function getAllQueries ()
	{
		return $this->query_log;
	}
	
	// Functions that should not be used... but well, we can't do without them at the moment
	public abstract function fromUnixtime ($timestamp);
	public abstract function toUnixtime ($date);
}
?>
