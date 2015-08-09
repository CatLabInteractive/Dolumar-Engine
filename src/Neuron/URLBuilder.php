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

class Neuron_URLBuilder
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
	
	private $opencallback;
	private $updatecallback;
	
	public function __construct ()
	{
		$this->setOpenCallback (array ($this, '_buildUrl'));
		$this->setUpdateCallback (array ($this, '_buildUrl'));
	}
	
	public function setCallback ($callback)
	{
		$this->setOpenCallback ($callback);
	}
	
	public function setOpenCallback ($callback)
	{
		$this->opencallback = $callback;
	}
	
	public function setUpdateCallback ($callback)
	{
		$this->updatecallback = $callback;
	}
	
	private function _buildUrl ($module, $display, $data, $title = null)
	{
		$query = '?';
		
		foreach ($data as $k => $v)
		{
			$query .= $k . '=' . urlencode ($v) . '&';
		}
		
		$query = substr ($query, 0, -1);
	
		return '<a href="'.ABSOLUTE_URL . $module . $query . '">'.$display.'</a>';
	}
	
	public function getURL ($module, $data, $display, $title =  null)
	{
		return $this->getOpenURL ($module, $display, $data, $title);
	}

	public function getRawURL ($module, $data)
	{
		$query = '?';
		
		foreach ($data as $k => $v)
		{
			$query .= $k . '=' . urlencode ($v) . '&';
		}
		
		$query = substr ($query, 0, -1);

		return ABSOLUTE_URL . $module . $query;
	}
	
	public function getOpenUrl ($module, $display, $data, $title = null, $misc1 = null)
	{
		return call_user_func ($this->opencallback, $module, $display, $data, $title, $misc1);
	}
	
	public function getUpdateUrl ($module, $display, $data, $title = null)
	{
		return call_user_func ($this->updatecallback, $module, $display, $data, $title);
	}
}
?>
