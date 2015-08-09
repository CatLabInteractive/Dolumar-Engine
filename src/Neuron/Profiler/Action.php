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

class Neuron_Profiler_Action
{
	private $oParent;
	private $sAction;
	private $fStart;
	private $fEnd;
	private $oChildren = array ();
	private $iMemUsageStart;
	private $iMemUsageEnd;
	
	public function __construct ($sAction, $oParent)
	{
		$this->sAction = $sAction;
		$this->oParent = $oParent;
		$this->fStart = microtime (true);
		$this->iMemUsageStart = memory_get_usage (true);
	}
	
	public function getParent ()
	{
		return $this->oParent;
	}
	
	public function appendChild ($oAction)
	{
		$this->oChildren[] = $oAction;
	}
	
	public function stop ()
	{
		$this->fEnd = microtime (true);
		$this->iMemUsageEnd = memory_get_usage (true);
	}
	
	public function getDuration ()
	{
		if (!isset ($this->fEnd))
		{
			$this->stop ();
		}
		return number_format ($this->fEnd - $this->fStart, 4);
	}
	
	public function getMemoryUsage ($unit = 'kb')
	{
		if (!isset ($this->iMemUsageEnd))
		{
			$this->stop ();
		}
		
		$value = $this->iMemUsageEnd - $this->iMemUsageStart;
		return $this->translateMemory ($value, $unit);
	}
	
	public function getTotalMemoryUsage ($unit = 'kb')
	{
		return $this->translateMemory ($this->iMemUsageEnd, $unit);
	}
	
	private function translateMemory ($value, $unit = 'kb')
	{
		switch ($unit)
		{
			case 'kb':
				return $value / 1024 . ' kB';
			break;
			
			default:
				return $value . 'B';
			break;
		}
	}
	
	public function getStringOutput ($level)
	{
		$sTabs = "";
		
		// Make tabs
		for ($i = 0; $i < $level; $i ++) $sTabs .= "\t";
		
		// Output duration
		$sOut = $sTabs . 
			"[".$this->getDuration () . "] " . 
			$this->fillWithSpaces ($this->sAction, 50) . 
			$this->fillWithSpaces ($this->getMemoryUsage ('kb'), 10, 'right').
			"[".$this->getTotalMemoryUsage ()."]\n";
		
		if (count ($this->oChildren) > 0)
		{
			$sOut .= $sTabs."{\n";
			foreach ($this->oChildren as $child)
			{
				$sOut .= $child->getStringOutput ($level + 1);
			}
			$sOut .= $sTabs . "}\n";
		}

		return $sOut;
	}
	
	private function fillWithSpaces ($text, $spaces, $align = 'left')
	{
		$text = trim ($text);
		$counts = ($spaces - strlen ($text));
	
		$spaces = "";
		for ($i = 0; $i < $counts; $i ++)
		{
			$spaces .= " ";
		}
		
		if ($align == 'right')
		{
			return $spaces.$text." ";	
		}
		else
		{
			return $text.$spaces;
		}
	}
	
	public function __toString ()
	{
		return $this->getStringOutput (0);
	}
}
?>
