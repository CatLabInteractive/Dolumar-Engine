<?php

restore_error_handler ();
error_reporting (E_ERROR);

define('Auth_OpenID_VERIFY_HOST', true);
define ('Auth_OpenID_BUGGY_GMP', true);
define ('Auth_OpenID_RAND_SOURCE', null);

/*
$path_extra = dirname(dirname(dirname(__FILE__)));
$path = ini_get('include_path');
$path = $path_extra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);
*/

function loginAndRedirect ($id)
{
	$login = Neuron_Core_Login::__getInstance ();

	if ($login->doLogin ($id))
	{
		//$_SESSION['just_logged_in'] = true;
	
		$url = ABSOLUTE_URL.'?session_id='.session_id ();
		header ('Location: '.$url);
		echo '<p>You are logged in.<br />Click <a href="'.$url.'">here</a> to continue.</p>';
		echo '<script type="text/javascript">document.location = \''.$url.'\';</script>';
	}
	else
	{
		echo ('<p>Could not login to account '.$id.'</p>');
		echo '<p>Error: '.$login->getError ().'</p>';
		exit (1);
	}
}

function registerWithOpenid ($id, $identity)
{
	$db = Neuron_Core_Database::__getInstance ();

	// Update this ID
	$db->update
	(
		'auth_openid',
		array
		(
			'user_id' => $id
		),
		"user_id = 0 AND openid_url = '".$db->escape ($identity)."'"
	);

	// Load the id, just to beware of copies
	$data = $db->query
	("
		SELECT
			*
		FROM
			auth_openid
		WHERE
			openid_url = '".$db->escape ($identity)."'
	");

	$id = $data[0]['user_id'];
	
	// now login and redirect.
	loginAndRedirect ($id);
}

function doIncludes() {
    /**
     * Require the OpenID consumer code.
     */
    require_once "Auth/OpenID/Consumer.php";

    /**
     * Require the "file store" module, which we'll need to store
     * OpenID information.
     */
    require_once "Auth/OpenID/FileStore.php";

    /**
     * Require the Simple Registration extension API.
     */
    require_once "Auth/OpenID/SReg.php";

    /**
     * Require the AX extension API
     */    
    require_once "Auth/OpenID/AX.php";

    /**
     * Require the PAPE extension module.
     */
    require_once "Auth/OpenID/PAPE.php";
}

doIncludes();

function displayError($message) 
{
	echo '<p>'.$message.'</p>';
	exit(0);
}

global $pape_policy_uris;

$pape_policy_uris = array
(
	PAPE_AUTH_MULTI_FACTOR_PHYSICAL,
	PAPE_AUTH_MULTI_FACTOR,
	PAPE_AUTH_PHISHING_RESISTANT
);

function &getStore() 
{
	/**
	* This is where the example will store its OpenID information.
	* You should change this path if you want the example store to be
	* created elsewhere.  After you're done playing with the example
	* script, you'll have to remove this directory manually.
	*/
	$store_path = CACHE_DIR.'openid';

	if (!file_exists($store_path) && !mkdir($store_path)) 
	{
		print "Could not create the FileStore directory '$store_path'. ".
			" Please check the effective permissions.";
		exit(0);
	}

	$obj = new Auth_OpenID_FileStore ($store_path);
	return $obj;
}

function &getConsumer() 
{
	/**
	* Create a consumer object using the store object created
	* earlier.
	*/
	$store = getStore();
	$consumer = new Auth_OpenID_Consumer($store);
	return $consumer;
}

function getScheme() 
{
	$scheme = 'http';
	if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') 
	{
		$scheme .= 's';
	}
	return $scheme;
}

function getReturnTo() 
{
	/*
	return sprintf
	(
		"%s://%s:%s%s/openid/finish/",
			getScheme(), 
			$_SERVER['SERVER_NAME'],
			$_SERVER['SERVER_PORT'],
			dirname($_SERVER['PHP_SELF'])
	);
	*/
	
	
	return ABSOLUTE_URL.'dispatch.php?module=openid/finish/';
}

function getTrustRoot() 
{
	/*
	return sprintf("%s://%s:%s%s/",
		   getScheme(), $_SERVER['SERVER_NAME'],
		   $_SERVER['SERVER_PORT'],
		   dirname($_SERVER['PHP_SELF']));
	*/
	
	return ABSOLUTE_URL;
}