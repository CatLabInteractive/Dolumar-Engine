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

class Neuron_GameServer_Windows_Premium extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
	
		// Window settings
		$this->setSize ('260px', '260px');
		$this->setTitle ($text->get ('premium', 'menu', 'main'));
		
		$this->setAllowOnlyOnce ();
		
		$this->setAjaxPollSeconds (3);
	}
	
	public function getContent ($error = null)
	{
		$text = Neuron_Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('premium');
		
		$player = Neuron_GameServer::getPlayer ();
		
		if (!$player)
		{
			return '<p class="false">'.$text->get ('login', 'login', 'account').'</p>';
		}
		
		$openid = isset ($_SESSION['neuron_openid_identity']) ? 
			md5 ($_SESSION['neuron_openid_identity']) : false;
		
		if ($player && $player->isFound () && ($player->isEmailCertified () || $openid))
		{
			$page = new Neuron_Core_Template ();
			
			// Premium account stuff
			$page->set ('premium', $text->get ('premium', 'myAccount'));
			
			if ($player->isPremium ())
			{
				$page->set ('premiumLast', Neuron_Core_Tools::putIntoText
				(
					$text->get ('premiumLast', 'myAccount'),
					array
					(
						date (DATETIME, $player->getPremiumEndDate ())
					)
				));
				
				$page->set ('extend2', $text->getClickTo ($text->get ('toPremium', 'myAccount')));
			}
			else
			{
				$page->set ('notPremium1', $text->get ('notPremium', 'myAccount'));
				$page->set ('notPremium2', $text->getClickTo ($text->get ('toPremium', 'myAccount')));
			}
			
			$page->set 
			(
				'toUse', 
				$text->getClickTo 
				(
					Neuron_Core_Tools::putIntoText 
					(
						$text->get ('toUseCredit'), 
						array ('amount' => PREMIUM_COST_CREDITS)
					)
				)
			);
			
			$page->set ('extend_url', htmlentities ($player->getCreditUseUrl (PREMIUM_COST_CREDITS)));
			
			$page->set ('benefits', $this->getBenefits ());

			if (!empty ($error))
			{
				$page->set ('error', $text->get ($error));
			}
			
			return $page->parse ('gameserver/account/premium.tpl');
		}
		elseif ($player->isFound ())
		{
			return '<p>'.$text->get ('validateEmail').'</p>';
		}
	}
	
	protected function getBenefits ()
	{
		return null;
	}
	
	public function getRefresh ()
	{
		$this->updateContent ();
	}
}
?>
