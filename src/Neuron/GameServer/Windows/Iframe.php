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

class Neuron_GameServer_Windows_Iframe 
	extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();

		$data = $this->getRequestData ();

		$center = isset ($data['centered']) ? $data['centered'] : true;
		$width = isset ($data['width']) ? $data['width'] . 'px' : '600px';
		$height = isset ($data['height']) ? $data['width'] . 'px' : '500px';
		$title = isset ($data['title']) ? $data['title'] : null;
	
		// Window settings
		$this->setSize ($width, $height);
		$this->setTitle ($title);

		$this->setClass ('small-border no-overflow');

		if ($center)
		{
			$this->setCentered ();
		}
	}
	
	public function getContent ()
	{
		$data = $this->getRequestData ();
		
		$url = $data['url'];

		return '<iframe src="'.$url.'" style="width: 100%; height: 100%; border: 0px none black;" border="0"></iframe>';
	}
	
	public function reloadContent ()
	{
	
	}
}
?>
