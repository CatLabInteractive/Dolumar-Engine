<?php

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
	return urldecode ($url);
}

function run() 
{
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
			header("Location: ".$redirect_url);
			echo '<html>';
			echo '<head><style type="text/css">body { background: black; color: white; }</style><head>';
			echo '<body>';
			echo '<p>Redirecting to OpenID Gateway...</p>';
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
			echo '<p>Redirecting to OpenID Gateway...</p>';
			print $form_html;
		}
	}
}

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

run();
?>
