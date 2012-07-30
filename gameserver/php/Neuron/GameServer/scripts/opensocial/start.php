<?php 
	require_once ('./php/connect.php'); 
	require_once ('lib.oath.php');
	
	$action = isset ($_GET['action']) ? $_GET['action'] : null;
	
	switch ($action)
	{
		case 'login':
			include ('login.php');
		break;
	
		default:
			include ('module.xml');
		break;
	}
?>
