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

class Neuron_GameServer_Map_Display_Cuboid
	extends Neuron_GameServer_Map_Display_Mesh
{
	private $width, $height, $depth;

	public function __construct ($width, $height, $depth, Neuron_GameServer_Map_Color $color)
 	{
 		$this->width = $width;
 		$this->height = $height;
 		$this->depth = $depth;

 		$this->setColor ($color);
 	}

 	public function getWidth ()
 	{
 		return $this->width;
 	}

 	public function getHeight ()
 	{
 		return $this->height;
 	}

	public function getDisplayData ()
	{
		return array
		(
			'attributes' => array 
			(
				'model' => 'cuboid',
				'width' => $this->width,
				'height' => $this->height,
				'depth' => $this->depth,
				'color' => $this->getColor ()->getHex ()
			)
		);
	}	
}