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

class Neuron_GameServer_Windows_Ignorelist 
	extends Neuron_GameServer_Windows_Window
{

	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
	
		// Window settings
		$this->setSize ('250px', '250px');
		$this->setTitle ($text->get ('ignorelist', 'menu', 'main'));
		
		//$this->setAllowOnlyOnce ();
	}
	
	public function getContent ()
	{
		$text = Neuron_Core_Text::getInstance ();	
	
		$player = Neuron_GameServer::getPlayer ();
		if (!$player)
		{
			return $this->throwError ($text->get ('login', 'login', 'account'));
		}
		
		$page = new Neuron_Core_Template ();
		
		$page->set ('nickname', '');
		
		// Ignore a player.
		$input = $this->getInputData ();
		$nickname = isset ($input['nickname']) ? $input['nickname'] : null;
		if (!empty ($nickname))
		{
			$target = Neuron_GameServer_Player::getFromName ($nickname);
			if ($target && $target->getId () == $player->getId ())
			{
				$page->set ('error', 'ignore_yourself');
			}
			
			else if ($target)
			{
				$player->setIgnoring ($target);
			}
			else
			{
				$page->set ('nickname', $nickname);
				$page->set ('error', 'player_not_found');
			}
		}
		
		// Unignore a player
		if (isset ($input['unignore']))
		{
			$target = Neuron_GameServer::getPlayer ($input['unignore']);
			if ($target)
			{
				$player->setIgnoring ($target, false);
			}
		}
		
		foreach ($player->getIgnoredPlayers () as $v)
		{
			$page->addListValue
			(
				'players',
				array
				(
					'id' => $v->getId (),
					'name' => Neuron_Core_Tools::output_varchar ($v->getName ())
				)
			);
		}
		
		return $page->parse ('gameserver/account/ignorelist.phpt');
	}
}
?>
