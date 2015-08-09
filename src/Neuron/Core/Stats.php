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

class Neuron_Core_Stats
{
	private $folder, $files = array ();

	public static function __getInstance ($folder = '')
	{
		static $in;
		if (empty ($in[$folder]))
		{
			$in[$folder] = new Neuron_Core_Stats ($folder);
		}
		return $in[$folder];
	}

	public function __construct ($folder)
	{
		$this->folder = $folder . '/';
	}

	public function get ($key, $groep, $file, $default = null, $race = null)
	{
		if (is_object ($race))
		{
			$race = $race->getName ();
		}
		
		if ($race != null)
		{
			$file = STATS_DIR . $this->folder . $race . '/' . $file . '.ini';
		}

		else
		{
			$file = STATS_DIR . $this->folder . $file . '.ini';
		}

		if (!isset ($this->files[$file]))
		{
			$this->loadFile ($file);
		}

		if (isset ($this->files[$file][$groep][$key]))
		{
			return $this->files[$file][$groep][$key];
		}

		else
		{
			// Return value
			if ($race != null)
			{
				return $this->get ($key, $groep, $file, null, $default);
			}

			else
			{
				return $default;
			}
		}
	}
	
	/*
		Backwards comptability
	*/
	public function getSection ($groep, $file)
	{
		return $this->getGroup ($groep, $file);
	}

	public function getGroup ($groep, $orgFile, $default = array (), $race = null)
	{
		if (is_object ($race))
		{
			$race = $race->getName ();
		}
		
		if ($race != null)
		{
			$file = STATS_DIR . $this->folder . '/'. $race . '/' . $orgFile . '.ini';
		}

		else
		{
			$file = STATS_DIR . $this->folder . '/'. $orgFile . '.ini';
		}

		if (!isset ($this->files[$file]))
		{
			$this->loadFile ($file);
		}

		if (isset ($this->files[$file][$groep]) && is_array ($this->files[$file][$groep]))
		{
			foreach ($this->files[$file][$groep] as $k => $v)
			{
				if (!empty ($v))
				{
					return $this->files[$file][$groep];
				}
			}
		}

		else
		{
			// Return value
			if ($race != null)
			{
				return $this->getGroup ($groep, $orgFile, null, $default);
			}

			else
			{
				return $default;
			}
		}
	}
	
	/*
		Simply returns the whole file:
		$return[$section][$id] = $value
	*/
	public function getFile ($orgFile, $default = array (), $race = null)
	{
		if (is_object ($race))
		{
			$race = $race->getName ();
		}
		
		if ($race != null)
		{
			$file = STATS_DIR . $this->folder . '/'. $race . '/' . $orgFile . '.ini';
		}

		else
		{
			$file = STATS_DIR . $this->folder . '/'. $orgFile . '.ini';
		}

		if (!isset ($this->files[$file]))
		{
			$this->loadFile ($file);
		}

		if (isset ($this->files[$file]) && is_array ($this->files[$file]))
		{
			return $this->files[$file];
		}

		else
		{
			// Return value
			if ($race != null)
			{
				return $this->getFile ($groep, $orgFile, $default);
			}

			else
			{
				return $default;
			}
		}
	}

	private function loadFile ($file)
	{
		if (file_exists ($file))
		{
			$this->files[$file] = parse_ini_file ($file, true);
		}

		else
		{
			$this->files[$file] = false;
		}
	}
}
?>
