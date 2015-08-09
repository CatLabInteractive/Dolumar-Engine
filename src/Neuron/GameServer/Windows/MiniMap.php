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

class Neuron_GameServer_Windows_MiniMap extends Neuron_GameServer_Windows_Window
{

	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
		
		$login = Neuron_Core_Login::__getInstance ();
		
		if ($login->isLogin ())
		{
			$player = Neuron_GameServer::getPlayer ();
			$pos = $player->getPreferences ();
			$pos = $pos['minimapPosition'];
		}

		else
		{
			$pos = Neuron_Core_Tools::getInput ('_COOKIE', COOKIE_PREFIX . 'prefMP', 'int', 0);
		}

		if ($pos == 4)
		{
			$this->setPosition ('auto', 'auto', '0px', '0px');
		}

		elseif ($pos == 2)
		{
			$this->setPosition ('0px', '32px', 'auto', 'auto');
		}

		elseif ($pos == 3)
		{
			$this->setPosition ('auto', '32px', '0px', 'auto');
		}
		else
		{
			$this->setPosition ('0px', 'auto', 'auto', '0px');
		}

		if ($pos != 5)
		{
			$this->setType ('panel');
		}

		else
		{
			// Movable ;-)
			$this->setPosition ('0px', 'auto', 'auto', '0px');
			//$this->setOnResize ('onResizeMiniMap');
		}
		
		// Window settings
		$this->setSize ('200px', '150px');
		$this->setTitle ('Mini Map');
		$this->setClass ('minimap');
		
		$this->setType ('panel');
		
		$this->setAllowOnlyOnce ();
		
		$this->setPool ('minimap');
	
	}
	
	public function getRefresh () {}
	
	public function reloadContent () {}
	
	public function getContent ()
	{
		$player = Neuron_GameServer::getPlayer ();
		
		$premium = false;
		if ($player && $player->isPremium ())
		{
			$premium = true;
		}
	
		$minimap = '<div id="minimap" style="width: 100%; height: 100%;" ' . ($premium ? 'class="premium-minimap"' : null) . '>';
		$minimap .= '</div>';
		
		$this->setOnload ('initMinimap');
		
		return $minimap;
	}

}

?>
