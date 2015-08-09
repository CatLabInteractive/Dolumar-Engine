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

define ('BBGS_PATHNAME', dirname(__FILE__).'/');

// Change this if you want to rename the openid scriptname
define ('BBGS_OPENID_SCRIPTNAME', 'openid.php');

$path = ini_get('include_path');
$path = BBGS_PATHNAME . PATH_SEPARATOR . $path;
ini_set('include_path', $path);

require_once (BBGS_PATHNAME . 'openid.php');

class BBGS_Client
{
	private $sError = null;

	public function __construct ()
	{
		// Check for openid URL
		$action = isset ($_GET['bbgs_action']) ? $_GET['bbgs_action'] : false;
		$openid_url = isset ($_GET['openid_url']) ? $_GET['openid_url'] : false;
		
		if ($openid_url || $action == 'tryauth')
		{
			$this->logout ();
			$this->try_auth ($openid_url);
		}
		
		elseif ($action == 'finishauth')
		{
			$this->finish_auth ();
		}
		
		elseif ($action == 'logout')
		{
			$this->logout ();
		}
	}
	
	public function isLogin ()
	{
		return isset ($_SESSION['bbgs_openid_identity']);
	}
	
	public function login ($openid_url)
	{
		header ('Location: '.getOpenIDUrl () . '?openid_url='.urlencode ($openid_url));
	}
	
	public function logout ()
	{
		unset ($_SESSION['bbgs_openid_identity']);
	}
	
	public function getOpenID ()
	{
		return $this->isLogin () ? $_SESSION['bbgs_openid_identity'] : false;
	}
	
	/*
		Return the user e-mail address (if available)
	*/
	public function getEmail ()
	{
		return isset ($_SESSION['bbgs_openid_email']) ? $_SESSION['bbgs_openid_email'] : false;
	}

	/*
		Return the user nickname (if available)
	*/
	public function getNickname ()
	{
		return isset ($_SESSION['bbgs_openid_nickname']) ? $_SESSION['bbgs_openid_nickname'] : false;
	}
	
	/*
		Return the user full name (if available)
	*/
	public function getFullname ()
	{
		return isset ($_SESSION['bbgs_openid_fullname']) ? $_SESSION['bbgs_openid_fullname'] : false;
	}
	
	private function try_auth ($openid)
	{
		$consumer = getConsumer();
		
		// Begin the OpenID authentication process.
		$auth_request = $consumer->begin ($openid);
		
		// No auth request means we can't begin OpenID.
		if (!$auth_request) 
		{
			$this->sError = "Authentication error; not a valid OpenID: " . $openid;
			return;
		}
		
		// Request a bunch of optional parameteres
		$sreg_request = Auth_OpenID_SRegRequest::build
		(
			// Required
			array('nickname'),
			// Optional
			array('fullname', 'email')
		);

		if ($sreg_request) 
		{
			$auth_request->addExtension($sreg_request);
		}
		
		// Redirect the user to the OpenID server for authentication.
		// Store the token for this authentication so we can verify the
		// response.

		// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
		// form to send a POST request to the server.
		if ($auth_request->shouldSendRedirect()) 
		{
			$redirect_url = $auth_request->redirectURL
			(
				getTrustRoot(),
				getReturnTo()
			);

			// If the redirect URL can't be built, display an error
			// message.
			if (Auth_OpenID::isFailure($redirect_url)) 
			{
				$this->sError = "Could not redirect to server: " . $redirect_url->message;
			} 
			else 
			{
				// Send redirect.
				header("Location: ".$redirect_url);
				
				echo '<p>Redirecting to OpenID provider... please wait.</p>';
				exit;
			}
		} 
		else 
		{
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $auth_request->htmlMarkup
			(
				getTrustRoot(), 
				getReturnTo(),
				false, 
				array
				(
					'id' => $form_id
				)
			);

			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html)) 
			{
				$this->sError = "Could not redirect to server: " . $form_html->message;
			}
			else 
			{
				print $form_html;
				
				echo '<p>Redirecting to OpenID provider... please wait.</p>';
				exit;
			}
		}
	}
	
	private function finish_auth ()
	{
		$consumer = getConsumer();

		// Complete the authentication process using the server's
		// response.
		$return_to = getReturnTo();
		$response = $consumer->complete($return_to);

		// Check the response status.
		if ($response->status == Auth_OpenID_CANCEL) 
		{
			// This means the authentication was cancelled.
			$msg = 'Verification cancelled.';
		} 
		else if ($response->status == Auth_OpenID_FAILURE) 
		{
			// Authentication failed; display the error message.
			$msg = "OpenID authentication failed: " . $response->message;
		} 
		else if ($response->status == Auth_OpenID_SUCCESS) 
		{
			// This means the authentication succeeded; extract the
			// identity URL and Simple Registration data (if it was
			// returned).
			$openid = $response->getDisplayIdentifier();
			$esc_identity = openid_escape ($openid);
			
			$_SESSION['bbgs_openid_identity'] = $esc_identity;

			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);

			$sreg = $sreg_resp->contents ();

			if (@$sreg['email']) 
			{
				$_SESSION['bbgs_openid_email'] = $sreg['email'];
			}

			if (@$sreg['nickname']) 
			{
				$_SESSION['bbgs_openid_nickname'] = $sreg['nickname'];
			}

			if (@$sreg['fullname']) 
			{
				$_SESSION['bbgs_openid_fullname'] = $sreg['fullname'];
			}
		}
	}
	
	/*
		If something is wrong, this funciton will tell you what.
	*/
	public function getError ()
	{
		return $this->sError;
	}
}
?>
