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

$sAction = isset ($sInputs[1]) ? $sInputs[1] : null;

$sOutput = Neuron_Core_Tools::getInput ('_GET', 'output', 'varchar');
switch ($sAction)
{
	case 'minimap':
		require_once (self::SCRIPT_PATH.'image/minimap.php');
	break;
	
	case 'snapshot':
		require_once (self::SCRIPT_PATH.'image/snapshot.php');
	break;
	
	case 'playercard':
		$player_id = isset ($sInputs[2]) ? $sInputs[2] : null;
		require_once (self::SCRIPT_PATH.'image/snapshot.php');
	break;
	
	case 'world':
		require_once (self::SCRIPT_PATH.'image/world.php');
	break;
}
?>
