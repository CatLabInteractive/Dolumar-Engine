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

class Neuron_Auth_OpenID
{
	private $disableredirect = false;

	public function dispatch ($openid_url = null)
	{
		$sInputs = explode ('/', isset ($_GET['module']) ? $_GET['module'] : '');
		unset ($_GET['module']);

		if (isset ($openid_url) || !isset ($_GET['openid_url']))
		{
			$_GET['openid_url'] = $openid_url;
		}

		$sAction = isset ($sInputs[1]) ? $sInputs[1] : null;
		switch ($sAction)
		{
			case 'finish':
				$this->finishAuth ();
			break;
	
			case 'register':
				$this->newAccount ();
			break;

			default:
				$this->tryAuth ();
			break;
		}
	}

	private function tryAuth ()
	{
		// Check for launch date
		if (defined ('LAUNCH_DATE'))
		{
			if (LAUNCH_DATE > time ())
			{
				$page = new Neuron_Core_Template ();
				
				$page->set ('name', '');
				$page->set ('launchdate', Neuron_Core_Tools::getCountdown (LAUNCH_DATE));
				
				echo $page->parse ('launchdate.phpt');
				exit ();
			}
		}

		$openid = getOpenIDURL();
		$consumer = getConsumer();

		// Begin the OpenID authentication process.
		$auth_request = $consumer->begin($openid);

		// No auth request means we can't begin OpenID.
		if (!$auth_request) 
		{
			displayError("Authentication error; not a valid OpenID:<br />".$openid);
		}

		$sreg_request = Auth_OpenID_SRegRequest::build
		(
	             // Required
	             array(),
	             // Optional
	             array('email', 'language', 'country', 'nickname', 'dob', 'gender')
		);

		if ($sreg_request) {
			$auth_request->addExtension($sreg_request);
		}
		
		// Add AX request for notification URL
		$ax = new Auth_OpenID_AX_FetchRequest ();
		$ax->add 
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/notify_url.xml',
				1,
				false,
				'notify_url'
			)
		);
		
		$ax->add 
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/profilebox_url.xml',
				1,
				false,
				'profilebox_url'
			)
		);
		
		$ax->add 
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/messagebundle_url.xml',
				1,
				false,
				'messagebundle_url'
			)
		);
		
		$ax->add 
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/fullscreen.xml',
				1,
				false,
				'fullscreen'
			)
		);
		
		$ax->add
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/userstats_url.xml',
				1,
				false,
				'userstats_url'
			)
		);
		
		$ax->add
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/hide_advertisement.xml',
				1,
				false,
				'hide_advertisement'
			)
		);

		$ax->add
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/hide_chat.xml',
				1,
				false,
				'hide_chat'
			)
		);
		
		$ax->add
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/tracker_url.xml',
				1,
				false,
				'tracker_url'
			)
		);
		
		$ax->add
		(
			new Auth_OpenID_AX_AttrInfo
			(
				'http://www.browser-games-hub.org/schema/openid/welcome_url.xml',
				1,
				false,
				'welcome_url'
			)
		);
		
		$auth_request->addExtension ($ax);

		/*
		$policy_uris = isset ($_GET['policies']) ? $_GET['policies'] : null;

		$pape_request = new Auth_OpenID_PAPE_Request($policy_uris);
		
		if ($pape_request) 
		{
			$auth_request->addExtension($pape_request);
		}
		*/

		// Redirect the user to the OpenID server for authentication.
		// Store the token for this authentication so we can verify the
		// response.

		// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
		// form to send a POST request to the server.
		if ($auth_request->shouldSendRedirect()) 
		{
			$redirect_url = $auth_request->redirectURL(getTrustRoot(), getReturnTo());

			// If the redirect URL can't be built, display an error
			// message.
			if (Auth_OpenID::isFailure($redirect_url)) 
			{
				displayError("Could not redirect to server: " . $redirect_url->message);
			} 
			else 
			{
				// Send redirect.
				if (!$this->disableredirect)
				{
					header("Location: ".$redirect_url);
				}

				echo '<html>';
				echo '<head><style type="text/css">body { background: black; color: white; }</style><head>';
				echo '<body>';
				echo '<p>Redirecting to OpenID Gateway...</p>';
				if ($this->disableredirect)
				{
					echo '<p><a href="'.$redirect_url.'">Click to continue.</a></p>';
				}
				echo '</body>';
				echo '</html>';
			}
		} 
		else 
		{
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $auth_request->htmlMarkup (getTrustRoot(), getReturnTo(), false, array('id' => $form_id));

			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html)) 
			{
				displayError("Could not redirect to server: " . $form_html->message);
			} 
			else 
			{
				//echo '<p>Redirecting to OpenID Gateway...</p>';

				if (!$this->disableredirect)
				{
					print $form_html;
				}
				else
				{
					$form_html = str_replace ("onload='document.forms[0].submit();", "", $form_html);
					$form_html = str_replace ('<script>var elements = document.forms[0].elements;for (var i = 0; i < elements.length; i++) {  elements[i].style.display = "none";}</script>', '', $form_html);
					print $form_html;
				}
			}
		}
	}

	private function finishAuth ()
	{
		$consumer = getConsumer();

		// Hacky hacky: something is going wrong with the url encoding, so fixing it here.
        if (isset($_GET['openid.sig'])) {
            $_GET['openid.sig'] = urlencode($_GET['openid.sig']);
        }

		// Complete the authentication process using the server's
		// response.
		$return_to = getReturnTo();
		$response = $consumer->complete ($return_to);

		// Check the response status.
		if ($response->status == Auth_OpenID_CANCEL) 
		{
			// This means the authentication was cancelled.
			echo 'Verification cancelled.';
		} 
		else if ($response->status == Auth_OpenID_FAILURE) 
		{
			// Authentication failed; display the error message.
			echo "OpenID authentication failed: " . $response->message;
		} 
		else if ($response->status == Auth_OpenID_SUCCESS) 
		{
			// This means the authentication succeeded; extract the
			// identity URL and Simple Registration data (if it was
			// returned).
			$openid = $response->getDisplayIdentifier();
			$esc_identity = escape($openid);

			// Fetch some random information
			if ($response->endpoint->canonicalID) 
			{
			    $escaped_canonicalID = escape($response->endpoint->canonicalID);
			    $success .= '  (XRI CanonicalID: '.$escaped_canonicalID.') ';
			}

			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();

			$email = isset ($sreg['email']) ? $sreg['email'] : null;
			$language = isset ($sreg['language']) ? strtolower ($sreg['language']) : null;
			$country = isset ($sreg['country']) ? $sreg['country'] : null;
			
			$dob = isset ($sreg['dob']) ? $sreg['dob'] : null;
			$gender = isset ($sreg['gender']) ? $sreg['gender'] : null;
			
			if (!empty ($dob))
				$_SESSION['birthday'] = strtotime ($dob);
			
			if (!empty ($gender))
				$_SESSION['gender'] = $gender;
			
			$nickname = isset ($sreg['nickname']) ? $sreg['nickname'] : null;
			
			//customMail ('daedelson@gmail.com', 'login test', print_r ($sreg, true));
			
			// Check if this language exists:
			if (isset ($language)/* && !isset ($_SESSION['language'])*/)
			{
				$allLanguages = Neuron_Core_Text::getLanguages ();
				if (in_array ($language, $allLanguages))
				{
					if (!isset ($_COOKIE['user_language']))
					{
						$_SESSION['language'] = $language;
					}
					//setcookie ('user_language', $language, time () + COOKIE_LIFE, '/');
				}
			}
			
			if (isset ($nickname))
			{
				$_SESSION['openid_nickname'] = $nickname;
			}
			
			// Fetch the AX
			$notify_url = null;
			$profilebox_url = null;
			$openid_userstats = null;
			
			$ax = Auth_OpenID_AX_FetchResponse::fromSuccessResponse ($response);
			if ($ax)
			{
				$ax_data = $ax->data;
			
				$keyname = 'http://www.browser-games-hub.org/schema/openid/notify_url.xml';
				$notify_url = isset ($ax_data[$keyname]) ? $ax_data[$keyname] : array ();
				
				$keyname2 = 'http://www.browser-games-hub.org/schema/openid/profilebox_url.xml';
				$profilebox_url = isset ($ax_data[$keyname2]) ? $ax_data[$keyname2] : array ();
				
				$keyname3 = 'http://www.browser-games-hub.org/schema/openid/messagebundle_url.xml';
				$messagebundle_url = isset ($ax_data[$keyname3]) ? $ax_data[$keyname3] : array ();
				
				$keyname4 = 'http://www.browser-games-hub.org/schema/openid/container.xml';
				$openid_container = isset ($ax_data[$keyname4]) ? $ax_data[$keyname4] : array ();
				
				$keyname5 = 'http://www.browser-games-hub.org/schema/openid/fullscreen.xml';
				$openid_fullscreen = isset ($ax_data[$keyname5]) ? $ax_data[$keyname5] : array ();
				
				$keyname6 = 'http://www.browser-games-hub.org/schema/openid/userstats_url.xml';
				$openid_userstats = isset ($ax_data[$keyname6]) ? $ax_data[$keyname6] : array ();
				
				$keyname7 = 'http://www.browser-games-hub.org/schema/openid/hide_advertisement.xml';
				$hide_advertisement = isset ($ax_data[$keyname7]) ? $ax_data[$keyname7] : array ();

				$keyname8 = 'http://www.browser-games-hub.org/schema/openid/hide_advertisement.xml';
				$hide_chat = isset ($ax_data[$keyname8]) ? $ax_data[$keyname8] : array ();
			
				$notify_url = count ($notify_url) > 0 ? $notify_url[0] : null;
				$profilebox_url = count ($profilebox_url) > 0 ? $profilebox_url[0] : null;
				$messagebundle_url = count ($messagebundle_url) > 0 ? $messagebundle_url[0] : null;
				$openid_container = count ($openid_container) > 0 ? $openid_container[0] : null;
				$openid_fullscreen = count ($openid_fullscreen) > 0 ? $openid_fullscreen[0] : null;
				$openid_userstats = count ($openid_userstats) > 0 ? $openid_userstats[0] : null;
				$hide_advertisement = count ($hide_advertisement) > 0 ? $hide_advertisement[0] : null;
				$hide_chat = count ($hide_chat) > 0 ? $hide_chat[0] : null;
				
				$_SESSION['opensocial_messagebundle'] = $messagebundle_url;
				$_SESSION['opensocial_container'] = $openid_container;
				$_SESSION['fullscreen'] = $openid_fullscreen == 1;
				$_SESSION['hide_advertisement'] = $hide_advertisement == 1;
				$_SESSION['hide_chat'] = $hide_chat == 1;
				
				$_SESSION['welcome_url'] = getAXValue ($ax_data, 'http://www.browser-games-hub.org/schema/openid/welcome_url.xml');
				$_SESSION['tracker_url'] = getAXValue ($ax_data, 'http://www.browser-games-hub.org/schema/openid/tracker_url.xml');
				
				// Load the tracker
				if (isset ($_SESSION['tracker_url']))
				{
					$_SESSION['tracker_html'] = @file_get_contents ($_SESSION['tracker_url']);
				}
				
				if (isset ($_SESSION['welcome_url']))
				{
					$_SESSION['welcome_html'] = @file_get_contents ($_SESSION['welcome_url']);
				}
			}

			return $this->handleAuthentication($esc_identity, $email, $notify_url, $profilebox_url, $openid_userstats);
		}
	}

    /**
     * @param $esc_identity
     * @param $email
     * @throws Neuron_Core_Error
     */
	public function handleAuthentication(
	    $esc_identity,
        $email,
        $notify_url = null,
        $profilebox_url = null,
        $openid_userstats = null
    ) {
        // Fetch a fresh user ID
        $db = Neuron_Core_Database::__getInstance ();
        $login = Neuron_Core_Login::__getInstance ();

        // See if there is an account available
        $acc = $db->select
        (
            'n_auth_openid',
            array ('user_id'),
            "openid_url = '".$db->escape ($esc_identity)."'"
        );

        $_SESSION['neuron_openid_identity'] = $esc_identity;

        if (count ($acc) == 1 && $acc[0]['user_id'] > 0)
        {
            $id = $acc[0]['user_id'];
            loginAndRedirect ($acc[0]['user_id'], $email);
        }
        else
        {
            if (count ($acc) == 0)
            {
                // Create a new account
                $db->insert
                (
                    'n_auth_openid',
                    array
                    (
                        'openid_url' => $esc_identity,
                        'user_id' => 0
                    )
                );
            }

            // Set a session key to make sure
            // that the server still knows you
            // when you hit submit.
            $_SESSION['dolumar_openid_identity'] = $esc_identity;
            $_SESSION['dolumar_openid_email'] = $email;

            $url = ABSOLUTE_URL.'dispatch.php?module=openid/register/&session_id='.session_id ();

            header ('Location: ' . $url);
        }

        // Update this ID
        $db->update
        (
            'n_auth_openid',
            array
            (
                'notify_url' => $notify_url,
                'profilebox_url' => $profilebox_url,
                'userstats_url' => $openid_userstats
            ),
            "openid_url = '".$db->escape ($esc_identity)."'"
        );
    }

	private function newAccount ()
	{
		// This script should only be executed if an openid is set in the session
		if (isset ($_SESSION['dolumar_openid_identity']))
		{
			$email = $_SESSION['dolumar_openid_email'];

			$login = Neuron_Core_Login::__getInstance ();
			$id = $login->registerAccount (null, $email);

			if ($id > 0)
			{
				registerWithOpenid ($id, $_SESSION['dolumar_openid_identity'], $email);
			}
			else
			{
				die ('Could not register a new account: '.$login->getError ());
			}

			// Skip all the login magic, combining account is now only done on a bigger level.
			/*

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
			*/
		}
		else
		{
			echo '<h2>Error in OpenID!</h2>';
			echo '<p>You shouldn\'t be here. No openid is set.</p>';
		}
	}
}

function escape($thing) {
    return htmlentities($thing);
}

function getAXValue ($ax_data, $keyname)
{
	$notify_url = isset ($ax_data[$keyname]) ? $ax_data[$keyname] : array ();
	$notify_url = count ($notify_url) > 0 ? $notify_url[0] : null;
	
	return $notify_url;
}


function getOpenIDURL() 
{
	// Render a default page if we got a submission without an openid
	// value.
	if (empty($_GET['openid_url'])) 
	{
		$error = "Expected an OpenID URL.";
		die ($error);
	}

	$url = $_GET['openid_url'];
	
	/*
	// Check for http
	$url = trim ($_GET['openid_url']);
	
	if (substr ($url, 0, 4) != 'http')
	{
		$url = 'http://'.$url;
	}

	*/
	return rawurldecode ($url);
}


header('Cache-Control: no-cache');
header('Pragma: no-cache');


restore_error_handler ();
error_reporting (E_ERROR);

define('Auth_OpenID_VERIFY_HOST', true);
//define ('Auth_OpenID_BUGGY_GMP', true);
//define ('Auth_OpenID_RAND_SOURCE', null);

/*
$path_extra = dirname(dirname(dirname(__FILE__)));
$path = ini_get('include_path');
$path = $path_extra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);
*/

function loginAndRedirect ($id, $email)
{
	$login = Neuron_Core_Login::__getInstance ();

	if ($login->doLogin ($id, false, $email))
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

function registerWithOpenid ($id, $identity, $email)
{
	$db = Neuron_Core_Database::__getInstance ();

	// Update this ID
	$db->update
	(
		'n_auth_openid',
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
			n_auth_openid
		WHERE
			openid_url = '".$db->escape ($identity)."'
	");

	$id = $data[0]['user_id'];
	
	// now login and redirect.
	loginAndRedirect ($id, $email);
}

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
	$store_path = CACHE_DIR.'openid';

	if (!file_exists($store_path) && !mkdir($store_path)) 
	{
		print "Could not create the FileStore directory '$store_path'. ".
			" Please check the effective permissions.";
		exit(0);
	}

	$obj = new Auth_OpenID_FileStore ($store_path);
	return $obj;

    /*
	$connection = new Neuron_Auth_MySQLConnection ();
	$obj = new Auth_OpenID_MySQLStore ($connection);
	$obj->createTables ();
	return $obj;
    */
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
