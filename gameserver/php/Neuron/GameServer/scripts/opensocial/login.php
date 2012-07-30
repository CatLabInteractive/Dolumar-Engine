<?php

include ('lib.oath.php');

class OrkutSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 
{
    protected function fetch_public_cert(&$request) 
    {
    	switch (trim ($_GET['oauth_consumer_key']))
    	{
    		case 'orkut.com':
    			return file_get_contents ('./static/keys/orkut.pem') ;
    		break;
    	
    		case 'netlog.com':
			return file_get_contents ('./static/keys/netlog.pem') ;
		break;
		
		default:
			die (json_encode (array ('status' => 'error', 'msg' => 'Public key not found: '.$_GET['oauth_consumer_key'])));
		break;
	}
    }
}

$request = OAuthRequest::from_request(null, null, $_GET);
$signature_method = new OrkutSignatureMethod();

$signature_valid = $signature_method->check_signature($request, null, null, $_GET["oauth_signature"]);

if (!$signature_valid)
{
	die (json_encode (array ('status' => 'error', 'msg' => 'Signature invalid. Please make sure your public key is valid and up to date.')));
}
elseif (empty ($_GET['opensocial_owner_id']) && empty ($_GET['opensocial_viewer_id']))
{
	die (json_encode (array ('status' => 'error', 'msg' => 'Owner ID or Viewer ID not set.')));
}
elseif ($_GET['opensocial_owner_id'] == $_GET['opensocial_viewer_id'])
{
	$_SESSION['loginAuthType'] = 'opensocial';
	$_SESSION['loginAuthUID'] = $_GET['opensocial_owner_id'];
	$_SESSION['loginAuthSesKey'] = null;
	//$_SESSION['loginAuthLanguage'] = isset ($_POST['fb_sig_locale']) ? translateLanguage ($_POST['fb_sig_locale']) : null;
	
	die (json_encode (array ('status' => 'success', 'session_key' => session_id ())));
}

?>
