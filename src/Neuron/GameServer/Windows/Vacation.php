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

class Neuron_GameServer_Windows_Vacation extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
		$this->setTitle ($text->get ('title', 'vacationMode', 'account'));
		
		$this->setAllowOnlyOnce ();
	}
	
	public function getContent ()
	{
		$myself = Neuron_GameServer::getPlayer ();
		if ($myself->inVacationMode ())
		{
			return $this->inVacationMode ();
		}
		else
		{
			return $this->getStartVacation ();
		}
	}
	
	private function getStartVacation ()
	{
		$myself = Neuron_GameServer::getPlayer ();
	
		$input = $this->getInputData ();
		
		$page = new Neuron_Core_Template ();
		
		if (isset ($input['confirm']) && Neuron_Core_Tools::checkConfirmLink ($input['confirm']))
		{
			if ($myself->startVacationMode ())
			{
				$page->set ('done', true);
			}
			else
			{
				$page->set ('done', false);
				$page->set ('error', $myself->getError ());
			}
		}
		else
		{
			$page->set ('done', false);
		}
	
		$page->set ('checkkey', Neuron_Core_Tools::getConfirmLink ());
		
		return $page->parse ('gameserver/account/vacationMode.phpt');
	}
	
	private function inVacationMode ()
	{
		$myself = Neuron_GameServer::getPlayer ();
		
		$input = $this->getInputData ();
		
		$page = new Neuron_Core_Template ();
		if (isset ($input['disable']))
		{
			if ($myself->endVacationMode ())
			{
				$page->set ('success', true);
			}
			else
			{
				$page->set ('success', false);
				$page->set ('error', $myself->getError ());
			}
		}
		
		// Get "since"
		$page->set ('since', date (DATETIME, $myself->getVacationStart ()));
		
		return $page->parse ('gameserver/account/vacationModeActive.phpt');
	}
}
?>
