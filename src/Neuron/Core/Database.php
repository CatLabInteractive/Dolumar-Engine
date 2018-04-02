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

class Neuron_Core_Database
{
	public static function __getInstance ($id = 'general')
	{
		static $in;
		
		if (!isset ($in[$id]))
		{
			$in[$id] = new Neuron_Core_Database ();
		}
		return $in[$id];
	}

	private $db;
	private $inSession = false;

	public function __construct ()
	{
		$this->db = Neuron_DB_Database::__getInstance ();
	}
	
	public function customQuery ($sql)
	{
		return $this->query ($sql);
	}
	
	public function query ($sql_query)
	{
		$pr = Neuron_Profiler_Profiler::getInstance ();
		
		$data = $this->db->query ($sql_query);
		
		return $data;
	}

	public function multiQuery ($sql_query)
	{
		$pr = Neuron_Profiler_Profiler::getInstance ();

		$data = $this->db->multiQuery ($sql_query);

		return $data;
	}
	
	public function insert ($table, $data)
	{
		// If no connection has been set: open the default connection 
		$table = $this->makeTableSafe ($table);

		$sql = "INSERT INTO ".$table." SET ";
			
		foreach ($data as $k => $v)
		{
		
			if ($v === 'NOW()')
			{
				$sql .= "$k = NOW(), ";
			} elseif ($v === null) {
                $sql .= "$k = NULL, ";
            } else
			{
				$sql.= "$k = '".$this->escape ($v)."', ";
			}
		}
		
		$sql = substr ($sql, 0, -2);
		
		$this->query ($sql);
		return $this->db->getInsertId ();
	}
	
	public function getInsertId ()
	{
		return $this->db->getInsertId ();
	}
	
	public function makeSafe ($value)
	{
		return $this->escape ($value);
	}
	
	public function update ($table, $data, $where)
	{
		$table = $this->makeTableSafe ($table);
		$sql = "UPDATE $table SET ";

		$totalSets = 0;
		
		foreach ($data as $k => $v)
		{
		
			if (is_array ($v))
			{
				throw new Neuron_Core_Error ('Unexpected parameter in update function.');
			}
		
			if ($v === '++')
			{
			
				$sql.= "$k = ($k + 1), ";
				$totalSets ++;
			
			}
			
			elseif ($v === '--')
			{
			
				$sql.= "$k = ($k - 1), ";
				$totalSets ++;
			
			}
			
			elseif (substr ($v, 0, 2) === '++')
			{
				if (substr ($v, 2) != 0)
				{
					$sql.= "$k = ($k + ".substr ($v, 2)."), ";
					$totalSets ++;
				}
			}
			
			elseif (substr ($v, 0, 2) === '--')
			{
				if (substr ($v, 2) != 0)
				{
					$sql.= "$k = ($k - ".substr ($v, 2)."), ";
					$totalSets ++;
				}
			}

			elseif ($v === 'NOW()')
			{
				$sql .= "$k = NOW(), ";
				$totalSets ++;
			}

			elseif ($v === null) {
                $sql .= "$k = NULL, ";
                $totalSets ++;
			}
			
			elseif (is_numeric ($v))
			{
				$sql.= "$k = ".$this->escape ($v).", ";
				$totalSets ++;
			}
			
			else 
			{
				$sql.= "$k = '".$this->escape ($v)."', ";
				$totalSets ++;
			}
		
		}

		$sql = substr ($sql, 0, -2);
		
		$sql.= ' WHERE '.$where;

		if ($totalSets > 0)
		{
			$this->query ($sql);
			return $this->db->getAffectedRows ();
		}

		else
		{
			return 0;
		}
	}
	
	private function makeTableSafe ($table)
	{
		$table = explode (",", $table);
		
		$o = "";
		foreach ($table as $v)
		{
		
			$o .= "`".trim ($v)."`, ";
		
		}
		
		$o = substr ($o, 0, -2);
	
		return $o;
	}

	public function countRows ($table, $where)
	{
		$rows = $this->select
		(
			$table,
			array ('COUNT(*)'),
			$where
		);

		return $rows[0]['COUNT(*)'];
	}
	
	public function select ($table, $data, $where = false, $order = false, $limiet = false, $forUpdate = false)
	{
		$sql = "SELECT ";

		if ($forUpdate === null && $this->inSession)
		{
			$forUpdate = true;
		}

		foreach ($data as $k => $v)
		{
		
			$sql.= "$v, ";
		
		}
		
		$sql = substr ($sql, 0, -2);
		
		/* Make tables safe */
		$table = $this->makeTableSafe ($table);
		
		$sql.= ' FROM '.$table;
		
		if ($where)
		{
		
			$sql.= ' WHERE '.$where;
		
		}
		
		if ($order)
		{
		
			$sql.= ' ORDER BY '.$order;
		
		}
		
		if ($limiet)
		{
		
			$sql.= ' LIMIT '.$limiet;
		
		}
		
		if ($forUpdate)
		{
		
			$sql.= " FOR UPDATE";
		
		}

		return $this->query ($sql);
	}
	
	public function remove ($table, $where, $forUpdate = false)
	{
		$table = $this->makeTableSafe ($table);
		$sql = "DELETE FROM $table WHERE $where ";
		
		if ($forUpdate)
		{
		
			$sql.= " FOR UPDATE";
		
		}
		
		$this->query ($sql);
		return $this->db->getAffectedRows ();
	}
	
	public function getLatestQuery ()
	{
		return $this->db->getLastQuery ();
	}
	
	public function getCounter ()
	{
		return $this->db->getQueryCounter ();
	}
	
	public function beginWork ()
	{
		if ($this->inSession == false)
		{
			$this->inSession = true;
			$this->query ("SET AUTOCOMMIT = 0");
			$this->query ("START TRANSACTION;");
		}
	}
	
	public function commit ()
	{
		if ($this->inSession == true)
		{
			$this->inSession = false;
			$this->query ("COMMIT");
			$this->query ("SET AUTOCOMMIT = 1");
		}
	}
	
	public function rollBack ()
	{
		if ($this->inSession == true)
		{
			$this->inSession = false;
			$this->query ("Rollback");
			$this->query ("SET AUTOCOMMIT = 1");
		}
	}
	
	public function getAllQueries ()
	{
		$o = '';
		foreach ($this->allQueries as $v)
		{
			$v = str_replace ("\n", ' ', $v);
			$v = str_replace ("\t", '', $v);
			$o .= trim ($v)."\n";
		}
		return $o;
	}

	public function escape ($escape)
	{
		return $this->db->escape ($escape);
	}
	
	public function __destruct ()
	{
		/*
		if (DEBUG_LOGS || true)
		{
			$s = '';
			foreach ($this->allQueries as $v)
			{
				$s .= gmdate ('H:i:s') . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . str_replace ("\n", '', str_replace ("\t", ' ', $v)) . "\n";
			}
			
			$cache = Neuron_Core_Cache::__getInstance ('mysqllog/');
			$cache->setCache (date ('dmYHis'), (string)$s);
			
			//file_put_contents (CACHE_DIR . 'debug/mysql/'.gmdate ('Ymd_H').".txt", $s, FILE_APPEND);
		}
		*/
	}
	
	// Not in use anymore!
	public function getDataFromQuery ($object)
	{
		return $object;
	}
}

?>
