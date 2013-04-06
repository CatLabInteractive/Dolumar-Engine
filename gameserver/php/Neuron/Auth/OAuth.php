<?php
require_once 'OAuth/OAuthServer.php';

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
}