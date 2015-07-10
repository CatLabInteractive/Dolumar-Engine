<?php
function doIncludes() {
    /**
     * Require the OpenID consumer code.
     */
    require_once BBGS_PATHNAME."Auth/OpenID/Consumer.php";

    /**
     * Require the "file store" module, which we'll need to store
     * OpenID information.
     */
    require_once BBGS_PATHNAME."Auth/OpenID/FileStore.php";

    /**
     * Require the Simple Registration extension API.
     */
    require_once BBGS_PATHNAME."Auth/OpenID/SReg.php";

    /**
     * Require the PAPE extension module.
     */
    require_once BBGS_PATHNAME."Auth/OpenID/PAPE.php";
}

doIncludes ();

function getConsumer () 
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

function getOpenIDUrl ()
{
	return sprintf
	(
		"%s://%s:%s%s/" . BBGS_OPENID_SCRIPTNAME,
		getScheme(), $_SERVER['SERVER_NAME'],
		$_SERVER['SERVER_PORT'],
		dirname($_SERVER['PHP_SELF'])
	);
}

function getReturnTo () 
{
	return getOpenIDUrl () . '?bbgs_action=finishauth';
}

function getTrustRoot() 
{
	return sprintf
	(
		"%s://%s:%s%s/",
		getScheme(), 
		$_SERVER['SERVER_NAME'],
		$_SERVER['SERVER_PORT'],
		dirname($_SERVER['PHP_SELF'])
	);
}

function openid_escape ($thing) 
{
	return htmlentities($thing);
}
?>
