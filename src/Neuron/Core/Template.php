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

// A few ugly methods because this wasn't very well designed at first? ;-)
$catlab_template_path = '';

function set_template_path ($path)
{
	global $catlab_template_path;
	$catlab_template_path = $path;
}

function get_template_path ()
{
	global $catlab_template_path;
	return $catlab_template_path;
}

function add_to_template_path ($path, $priorize = true)
{
	if (get_template_path () == '')
	{
		set_template_path ($path);
	}

	else if ($priorize)
	{
		set_template_path ($path . PATH_SEPARATOR . get_template_path ());
	}
	else
	{
		set_template_path (get_template_path () . PATH_SEPARATOR . $path);
	}
}

// Backwards compatability stuff
if (defined ('DEFAULT_TEMPLATE_DIR'))
{
	add_to_template_path (DEFAULT_TEMPLATE_DIR, false);
}

if (defined ('TEMPLATE_DIR'))
{
	add_to_template_path (TEMPLATE_DIR, true);
}

class Neuron_Core_Template
{

	private $values = array ();
	private $lists = array ();
	
	private $sTextFile = null;
	private $sTextSection = null;
	
	private $objText = null;
	
	public static function load ()
	{
	
	}
	
	public static function getUniqueId ()
	{
		if (!isset ($_SESSION['tc']))
		{
			$_SESSION['tc'] = time ();
		}
		
		$_SESSION['tc'] ++;
		
		return $_SESSION['tc'];
	}
	
	private static function getTemplatePaths ()
	{
		return explode (PATH_SEPARATOR, get_template_path ());
	}
	
	// Text function
	public function setTextSection ($sTextSection, $sTextFile = null)
	{
		$this->sTextSection = $sTextSection;
		
		if (isset ($sTextFile))
		{
			$this->sTextFile = $sTextFile;
		}
	}
	
	public function setTextFile ($sTextFile)
	{
		$this->sTextFile = $sTextFile;
	}

	public function set ($var, $value, $overwrite = false, $first = false)
	{
		$this->setVariable ($var, $value, $overwrite, $first);
	}
	
	// Intern function
	private function getText ($sKey, $sSection = null, $sFile = null, $sDefault = null)
	{
		if (!isset ($this->objText))
		{
			$this->objText = Neuron_Core_Text::__getInstance ();
		}
		
		$txt = Neuron_Core_Tools::output_varchar
		(
			$this->objText->get 
			(
				$sKey, 
				isset ($sSection) ? $sSection : $this->sTextSection, 
				isset ($sFile) ? $sFile : $this->sTextFile,
				false
			)
		);

		if (!$txt)
		{
			return $sDefault;
		}

		return $txt;
	}

	public function setVariable ($var, $value, $overwrite = false, $first = false)
	{
		if ($overwrite)
		{	
			$this->values[$var] = $value;
		}
		
		else 
		{
			if (isset ($this->values[$var]))
			{
				if ($first)
				{	
					$this->values[$var] = $value.$this->values[$var];
				}
				
				else 
				{
					$this->values[$var].= $value;
				}
			}
			
			else 
			{
				$this->values[$var] = $value;
			}
		}
	}
	
	public function addListValue ($var, $value)
	{
		$this->lists[$var][] = $value;
	}
	
	public function putIntoText ($txt, $params = array ())
	{
		return Neuron_Core_Tools::putIntoText ($txt, $params);
	}
	
	public function sortList ($var)
	{
		if (isset ($this->lists[$var]))
		{
			sort ($this->lists[$var]);
		}
	}
	
	public function usortList ($var, $function)
	{
		if (isset ($this->lists[$var]))
		{
			usort ($this->lists[$var], $function);
		}
	}
	
	public function isTrue ($var)
	{
		return isset ($this->values[$var]) && $this->values[$var];
	}
	
	private static function getFilename ($template)
	{
		foreach (self::getTemplatePaths () as $v)
		{
			if (is_readable ($v . '/' . $template))
			{
				return $v . '/' . $template;
			}
		}
		
		return false;	
	}
	
	public static function hasTemplate ($template)
	{
		return self::getFilename ($template) != false;
	}
	
	public function getClickTo ($sKey, $sSection = null, $sFile = null)
	{
		if (!isset ($this->objText))
		{
			$this->objText = Neuron_Core_Text::__getInstance ();
		}
		
		return $this->objText->getClickTo ($this->getText ($sKey, $sSection, $sFile));
	}

	public function parse ($template, $text = null)
	{
		/* Set static url adress */
		$this->set ('STATIC_URL', TEMPLATE_DIR);
		
		// SEt unique id
		$this->set ('templateID', self::getUniqueId ());

		ob_start ();
		
		if (! $filename = $this->getFilename ($template))
		{
			echo '<h1>Template not found</h1>';
			echo '<p>The system could not find template "'.$template.'"</p>';
			
			$filename = null;
		}
		
		foreach ($this->values as $k => $v)
		{
			$$k = $v;
		}
		
		foreach ($this->lists as $k => $v)
		{
			$n = 'list_'.$k;	
			$$n = $v;
		}
		
		
		if (isset ($filename))
		{
			include $filename;
		}
		
		$val = ob_get_contents();
		ob_end_clean();

		return $val;
	}
}
?>
