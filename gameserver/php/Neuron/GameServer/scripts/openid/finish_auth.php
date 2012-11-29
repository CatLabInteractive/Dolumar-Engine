<?php
function escape($thing) {
    return htmlentities($thing);
}

function getAXValue ($ax_data, $keyname)
{
	$notify_url = isset ($ax_data[$keyname]) ? $ax_data[$keyname] : array ();
	$notify_url = count ($notify_url) > 0 ? $notify_url[0] : null;
	
	return $notify_url;
}

function run() 
{
	$consumer = getConsumer();

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

		// Fetch a fresh user ID
		$db = Neuron_Core_Database::__getInstance ();
		$login = Neuron_Core_Login::__getInstance ();
		
		// See if there is an account available
		$acc = $db->select
		(
			'auth_openid',
			array ('user_id'),
			"openid_url = '".$db->escape ($esc_identity)."'"
		);
		
		$_SESSION['neuron_openid_identity'] = $esc_identity;
		
		if (count ($acc) == 1 && $acc[0]['user_id'] > 0)
		{
			$id = $acc[0]['user_id'];
			loginAndRedirect ($acc[0]['user_id']);
		}
		else
		{
			if (count ($acc) == 0)
			{
				// Create a new account
				$db->insert
				(
					'auth_openid',
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
			include ('new_account.php');
		}
		
		// Update this ID
		$db->update
		(
			'auth_openid',
			array
			(
				'notify_url' => $notify_url,
				'profilebox_url' => $profilebox_url,
				'userstats_url' => $openid_userstats
			),
			"openid_url = '".$db->escape ($esc_identity)."'"
		);
		
		//customMail ('daedelson@gmail.com', 'login test', print_r ($_SESSION, true));
		
		/*		
		$success = sprintf
		(
			'You have successfully verified <a href="%s">%s</a> as your identity.', 
			$esc_identity, 
			$esc_identity
		);

		if ($response->endpoint->canonicalID) 
		{
		    $escaped_canonicalID = escape($response->endpoint->canonicalID);
		    $success .= '  (XRI CanonicalID: '.$escaped_canonicalID.') ';
		}

		$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);

		$sreg = $sreg_resp->contents();

		if (@$sreg['email']) 
		{
			$success .= "  You also returned '".escape($sreg['email'])."' as your email.";
		}

		if (@$sreg['nickname']) 
		{
			$success .= "  Your nickname is '".escape($sreg['nickname'])."'.";
		}

		if (@$sreg['fullname']) 
		{
			$success .= "  Your fullname is '".escape($sreg['fullname'])."'.";
		}

		$pape_resp = Auth_OpenID_PAPE_Response::fromSuccessResponse($response);

		if ($pape_resp) 
		{
			if ($pape_resp->auth_policies) 
			{
				$success .= "<p>The following PAPE policies affected the authentication:</p><ul>";

				foreach ($pape_resp->auth_policies as $uri) 
				{
					$escaped_uri = escape($uri);
					$success .= "<li><tt>$escaped_uri</tt></li>";
				}

				$success .= "</ul>";
			} 
			else 
			{
				$success .= "<p>No PAPE policies affected the authentication.</p>";
			}

			if ($pape_resp->auth_age) 
			{
				$age = escape($pape_resp->auth_age);
				$success .= "<p>The authentication age returned by the server is: <tt>".$age."</tt></p>";
			}

			if ($pape_resp->nist_auth_level) 
			{
				$auth_level = escape($pape_resp->nist_auth_level);
				$success .= "<p>The NIST auth level returned by the server is: <tt>".$auth_level."</tt></p>";
			}
		} 
		else 
		{
			$success .= "<p>No PAPE response was sent by the provider.</p>";
		}
		*/
	}

	//echo $success;
	// include 'index.php';
}
run();
?>
