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
