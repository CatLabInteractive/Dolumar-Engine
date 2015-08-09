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

//require_once 'OAuth/OAuthServer.php';

class Neuron_Auth_OAuth
{
	public function dispatch ()
	{
		$sInputs = explode ('/', isset ($_GET['module']) ? $_GET['module'] : '');
		unset ($_GET['module']);

		$page = new Neuron_Core_Template ();

		$sAction = isset ($sInputs[1]) ? $sInputs[1] : null;
		switch ($sAction)
		{
			case 'install':
				$this->install ();
			break;

			case 'register':
				$page->set ('content', $this->register ());
				echo $page->parse ('oauth/index.phpt');
			break;

			case 'accesstoken':
				$this->accesstoken ();
			break;

			case 'authorize':
				$page->set ('content', $this->authorize ());
				echo $page->parse ('oauth/index.phpt');
			break;

			case 'requesttoken':
				$this->requesttoken ();
			break;

			case 'applications':
			default:
				$page->set ('content', $this->getApplications ());
				echo $page->parse ('oauth/index.phpt');
			break;
		}
	}

	private function getApplications ()
	{
		$player = Neuron_GameServer::getPlayer ();
		if (!$player)
		{
			echo '<p>Please login.</p>';
			return;
		}

		$store = Neuron_Auth_OAuthStore::getStore (); 

		$applications = array ();
		foreach ($store->listConsumers ($player->getId ()) as $application)
		{
			$applications[] = array (
				'ID' => $application['id'],
				'Consumer Key' => $application['consumer_key'],
				'Consumer Secret' => $application['consumer_secret']
			);
		}

		$page = new Neuron_Core_Template ();
		$page->set ('applications', $applications);
		return $page->parse ('oauth/applications.phpt');
	}

	private function register ()
	{
		$player = Neuron_GameServer::getPlayer ();
		if (!$player)
		{
			echo '<p>Please login.</p>';
			return;
		}

		$name = Neuron_Core_Tools::getInput ('_POST', 'name', 'varchar');
		$developer = Neuron_Core_Tools::getInput ('_POST', 'developer', 'varchar');

		$email = Neuron_Core_Tools::getInput ('_POST', 'email', 'email');
		$user_id = $player->getId ();

		$page = new Neuron_Core_Template ();

		if ($name && $developer)
		{
			$consumer = array (
				'requester_name' => $developer,
				'requester_email' => $email
			);

			// Register the consumer
			$store = Neuron_Auth_OAuthStore::getStore (); 
			$key = $store->updateConsumer($consumer, $user_id);

			// Get the complete consumer from the store
			$consumer = $store->getConsumer($key, $user_id);

			// Some interesting fields, the user will need the key and secret
			$page->set ('app_data', array (
				'Consumer ID' => $consumer['id'],
				'Consumer Key' => $consumer['consumer_key'],
				'Consumer secret' => $consumer['consumer_secret']
			));

			return $page->parse ('oauth/information.phpt');
		}
		else
		{
			return $page->parse ('oauth/register.phpt');
		}
	}

	private function install ()
	{
		$store = Neuron_Auth_OAuthStore::getStore (); 
		$store->install ();

		return 'Installed.';
	}

	private function accesstoken ()
	{
		Neuron_Auth_OAuthStore::getStore (); 
		$server = new OAuthServer();
		$token = $server->accessToken();
	}

	private function authorize ()
	{
		$player = Neuron_GameServer::getPlayer ();

		if (!$player)
		{
			$html = '<p>' . __('This page is only available for registered users.') . '</p>';

			/*

			$_SESSION['after_login_redirect'] = Neuron_URLBuilder::getURL 
			(
				'oauth/authorize', 
				array 
				(
					'oauth_token' => Neuron_Core_Tools::getInput ('_GET', 'oauth_token', 'varchar')
				)
			);

			header ('Location: ' . Neuron_URLBuilder::getURL ('login'));

			return;
			*/

			return $thml;
		}
		
		// The current user
		$user_id = $player->getId ();

		// Fetch the oauth store and the oauth server.
		$store = Neuron_Auth_OAuthStore::getStore (); 
		$server = new OAuthServer();

		try
		{
			// Check if there is a valid request token in the current request
			// Returns an array with the consumer key, consumer secret, token, token secret and token type.
			$rs = $server->authorizeVerify();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				// See if the user clicked the 'allow' submit button (or whatever you choose)
				$authorized = true;

				// Set the request token to be authorized or not authorized
				// When there was a oauth_callback then this will redirect to the consumer
				$server->authorizeFinish($authorized, $user_id);

				// No oauth_callback, show the user the result of the authorization
				// ** your code here **
				unset ($_GET['rewritepagemodule']);
				$url = Neuron_URLBuilder::getInstance ()->getRawURL ('oauth/authorize', $_GET);
				$html = '<form method="post" action="' . $url . '"><button>Accept</button></form>';
			}
			else
			{
				unset ($_GET['rewritepagemodule']);
				$url = Neuron_URLBuilder::getInstance ()->getRawURL ('oauth/authorize', $_GET);
				$html = '<form method="post" action="' . $url . '"><button>Accept</button></form>';
			}
		}
		catch (OAuthException $e)
		{
			// No token to be verified in the request, show a page where the user can enter the token to be verified
			// **your code here**
			$html = 'oops';
		}

		return $html;
	}

	private function requesttoken ()
	{
		Neuron_Auth_OAuthStore::getStore (); 
		$server = new OAuthServer();
		$token = $server->requestToken();
	}
}