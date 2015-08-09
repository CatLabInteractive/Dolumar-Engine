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

class Neuron_GameServer_Windows_Register extends Neuron_GameServer_Windows_Window
{
	public function setSettings ()
	{
		$text = Neuron_Core_Text::__getInstance ();
	
		// Window settings
		$this->setSize ('300px', '300px');
		$this->setTitle ($text->get ('register', 'menu', 'main'));
		
		$this->setAllowOnlyOnce ();
	
	}
	
	public function getContent ()
	{
	
		/* No invitation code needed anymore */
		
		/*
		$text = Neuron_Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('locked');
	
		// Invitation link required!
		$page = new Neuron_Core_Template ();
		
		$page->set ('invCode', Neuron_Core_Tools::getInput ('_COOKIE', 'invitation', 'varchar'));
		
		$page->set ('locked', $text->get ('locked'));
		$page->set ('invitation', $text->get ('invitation'));
		$page->set ('code', $text->get ('code'));
		$page->set ('submit', $text->get ('submit'));
		
		return $page->parse ('register/register_inv.tpl');
		*/
		
		return $this->showRegisterForm ();
		//return '<p>Registration is disabled. Please use OpenID for authentication.</p>';
	
	}
	
	public function showRegisterForm ($error = false)
	{
		$text = Neuron_Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('register');
	
		$page = new Neuron_Core_Template ();
		$page->setTextSection ('register', 'account');
		
		// Get invitation code
		$data = $this->getInputData ();
		
		$page->set ('invCode', isset ($data['invCode']) ? $data['invCode'] : null);
		
		// If an error has occured, add that
		if ($error)
		{
			$page->set ('error', $text->get ($error, 'errors'));
		}
		
		// Check for already found data
		if (isset ($data['username']))
		{
			$page->set ('username_value', Neuron_Core_Tools::output_varchar ($data['username']));
		}
		
		if (isset ($data['email']))
		{
			$page->set ('email_value', Neuron_Core_Tools::output_varchar ($data['email']));
		}
		
		$page->set ('register', $text->get ('register'));
		$page->set ('submit', $text->get ('submit'));
		$page->set ('email', $text->get ('email'));
		$page->set ('username', $text->get ('username'));
		$page->set ('password', $text->get ('password'));
		$page->set ('password2', $text->get ('password2'));
		
		return $page->parse ('register/register.tpl');
	}
	
	private function processRegistration ($username, $email, $password, $password2)
	{
		$db = Neuron_Core_Database::__getInstance ();
		$login = Neuron_Core_Login::__getInstance ();
		$text = Neuron_Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('register');
	
		// Check input
		if (!Neuron_Core_Tools::checkInput ($username, 'username'))
		{
			$this->updateContent ($this->showRegisterForm ('usernameFormat'));
		}
		
		elseif (!Neuron_Core_Tools::checkInput ($password, 'password'))
		{
			$this->updateContent ($this->showRegisterForm ('passwordFormat'));
		}

		elseif (!Neuron_Core_Tools::checkInput ($email, 'email'))
		{
			$this->updateContent ($this->showRegisterForm ('emailFormat'));
		}
		
		elseif ($password != $password2)
		{
			$this->updateContent ($this->showRegisterForm ('passwordMismatch'));
		}
		
		else 
		{
			// Next step: checking username and stuff
			$userCheck = $db->select
			(
				'n_players',
				array ('plid'),
				"nickname = '$username' AND isRemoved = 0"
			);
			
			$mailCheck = $db->select
			(
				'n_players',
				array ('plid'),
				"email = '$email' AND isRemoved = 0"
			);
			
			if (count ($userCheck) > 0)
			{
				$this->updateContent ($this->showRegisterForm ('userFound'));
			}
			
			elseif (count ($mailCheck) > 0)
			{
				$this->updateContent ($this->showRegisterForm ('emailFound'));
			}
			
			else {
				
				/*
					Check for referrer.
				*/
				$referrer = intval (Neuron_Core_Tools::getInput ('_COOKIE', 'preferrer', 'int'));
				$user = Neuron_GameServer::getPlayer ($referrer);
				
				$ref_id = $user ? $user->getId () : null;
				
				// Create the account
				$id = $login->registerAccount ($username, $email, $password, $ref_id);
				$user = Neuron_GameServer::getPlayer ($id);
				
				/*
				if (!$isInfinite)
				{
					// Withdraw 1 invitation
					$db->update
					(
						'invitation_codes',
						array
						(
							'invLeft' => '--'
						),
						"invCode = '$invitation'"
					);
				}
				else
				{
					// Add 1 invitation
					$db->update
					(
						'invitation_codes',
						array
						(
							'invLeft' => '++'
						),
						"invCode = '$invitation'"
					);
				}
				*/
				
				// Show "finished" page
				$page = new Neuron_Core_Template ();
				
				$page->set ('done', $text->get ('done'));
				$page->set ('login', $text->get ('login'));
				
				$page->set ('tracker_url', htmlentities ($user->getTrackerUrl ('registration')));
				
				$this->updateContent ($page->parse ('register/register_done.tpl'));
			}
		}
	}
	
	public function getRefresh () {}

	public function processInput ()
	{
		
		$text = Neuron_Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('locked');
		
		$db = Neuron_Core_Database::__getInstance ();
	
		$data = $this->getInputData ();
		
		/*
		// Check invitation code
		if (isset ($data['invCode']))
		{
		
			// DB Check
			$l = $db->select
			(
				'invitation_codes',
				array ('plid', 'i_infinite'),
				"invCode = '{$data['invCode']}' AND (invLeft > 0 OR i_infinite = '1')"
			);
			
			// Okay
			if (count ($l) == 1)
			{
			
			*/
				
				// Check for input
				if (
					isset ($data['username'])
					&& isset ($data['email'])
					&& isset ($data['password'])
					&& isset ($data['password2'])
				)
				{
					$this->processRegistration (
						$data['username'], 
						$data['email'], 
						$data['password'],
						$data['password2']
					);
				}
				
				else {
					$this->updateContent ($this->showRegisterForm ());
				}
				
			/*
			}
			
			else {
				$this->updateContent ('<p class="false">'.$text->get ('invNotFound').'<br />'.$data['invCode'].'</p>');
			}
		}
		*/
	}
}
?>
