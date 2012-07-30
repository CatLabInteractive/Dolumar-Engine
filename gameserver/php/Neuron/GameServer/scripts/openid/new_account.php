<?php

// This script should only be executed if an openid is set in the session
if (isset ($_SESSION['dolumar_openid_identity']))
{
	header("Content-Type: text/html; charset=UTF-8");

	$page = new Neuron_Core_Template ();
	
	$server = Neuron_GameServer::getServer();
	$name = $server->getServerName ();
	
	$page->set ('name', $name);
	$page->set ('session_id', session_id ());
	
	$page->set ('showDetails', Neuron_Core_Tools::getInput ('_GET', 'details', 'varchar'));

	$login = Neuron_Core_Login::__getInstance ();
	$db = Neuron_Core_Database::__getInstance ();

	// Check for username and password. If these are found, put them in the database.
	$username = isset ($_POST['username']) ? $_POST['username'] : false;
	$password = isset ($_POST['password']) ? $_POST['password'] : false;
	
	if ($username && $password)
	{
		// Check for details:
		$player = $login->checkLoginDetails ($username, $password);
		
		if ($player)
		{
			registerWithOpenid ($player->getId (), $_SESSION['dolumar_openid_identity']);
		}
		else
		{
			$page->set ('error', true);
		}
	}

	elseif (isset ($_GET['do']) 
		&& $_GET['do'] == 'register' 
		|| (
			defined ('OPENID_SKIP_LOGIN') 
			&& OPENID_SKIP_LOGIN
		)
	)
	{
		$email = isset ($_SESSION['dolumar_openid_email']) ? $_SESSION['dolumar_openid_email'] : null;
	
		if (defined ('OPENID_SKIP_NICKNAME') 
			&& OPENID_SKIP_NICKNAME
			&& !empty ($_SESSION['openid_nickname'])
		)
		{
			$id = $login->registerAccount ($_SESSION['openid_nickname'], $email);
		}
		else
		{
			$id = $login->registerAccount (null, $email);
		}

		if ($id > 0)
		{
			registerWithOpenid ($id, $_SESSION['dolumar_openid_identity']);
		}
		else
		{
			die ('Could not register a new account: '.$login->getError ());
		}
	}
	
	$page->set ('static_client_url', BASE_URL . 'gameserver/');
	
	// Plugins
	$header = '';
	if (Neuron_Core_Template::hasTemplate ('gameserver/openid/header.phpt'))
		$header = $page->parse ('gameserver/openid/header.phpt');
	
	$page->set ('header', $header);
	
	echo $page->parse ('openid/register.phpt');
}
else
{
	echo '<h2>Error in OpenID!</h2>';
	echo '<p>You shouldn\'t be here. No openid is set.</p>';
}
?>
