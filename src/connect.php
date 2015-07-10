<?php
/*
	CONNECT.PHP
	This file is loaded on every request.
*/

// Report all PHP errors
//error_reporting(E_ALL);
ignore_user_abort (true);

session_name ('dolumar_session');

ini_set ('session.use_cookies', 0);
ini_set ('session.use_only_cookies', 0);

define ('CATLAB_BASEPATH', dirname (dirname (__FILE__)) . '/');
define ('CATLAB_LANGUAGE_PATH', CATLAB_BASEPATH . 'languages/');
define ('PEAR_BASEPATH', dirname (dirname (dirname (__FILE__))) . '/pear/');

// Define cookie path
$base_path = ABSOLUTE_URL;
$base_path = explode ('/', $base_path);

if (isset ($_SERVER['SERVER_NAME']))
{
	while (array_shift ($base_path) != $_SERVER['SERVER_NAME'] && count ($base_path) > 0) {}
}
$base_path = '/'.implode ('/', $base_path);
define ('COOKIE_BASE_PATH', $base_path);

define ('API_DATE_FORMAT', 'Y-m-d\TH:i:s');

// Set session ID if provided
if (isset ($_GET['phpSessionId']) && !empty ($_GET['phpSessionId']))
{
	session_id ($_GET['phpSessionId']);
	session_start();
}
elseif (isset ($_GET['session_id']) && !empty ($_GET['session_id']))
{
	session_id ($_GET['session_id']);
	session_start();
}

elseif (isset ($_COOKIE['session_id']) && !empty ($_COOKIE['session_id']))
{
	session_id ($_COOKIE['session_id']);
	session_start();
}

else
{
	// Make a new session
	session_start();
}

// Get right language tag
if (isset ($_GET['language']))
{
	define ('LANGUAGE_TAG', $_GET['language']);
	
	$_SESSION['language'] = LANGUAGE_TAG;
	setcookie ('user_language', LANGUAGE_TAG, time () + COOKIE_LIFE, '/');
}

elseif (isset ($_COOKIE['user_language']))
{
	define ('LANGUAGE_TAG', $_COOKIE['user_language']);
	$_SESSION['language'] = LANGUAGE_TAG;
}

elseif (isset ($_SESSION['language']))
{
	define ('LANGUAGE_TAG', $_SESSION['language']);
}

else
{
	if (!empty ($_SESSION['loginAuthLanguage']) && strlen ($_SESSION['loginAuthLanguage']) == 2)
	{
		define ('LANGUAGE_TAG', $_SESSION['loginAuthLanguage']);
		$_SESSION['language'] = LANGUAGE_TAG;
	}
	else
	{
		define ('LANGUAGE_TAG', 'en');
	}
}

// Check for client time zone
if (isset ($_COOKIE['time_zone_offset']) && isset ($_COOKIE['time_zone_dst']))
{
	$tz = timezone_name_from_abbr('', -$_COOKIE['time_zone_offset']*60, $_COOKIE['time_zone_dst']);
	if ($tz)
	{
		define ('TIME_ZONE', $tz);
	}
	else
	{
		define ('TIME_ZONE', 'Europe/Brussels');
	}
}
else
{
	define ('TIME_ZONE', 'Europe/Brussels');
}

// Game version
if (!defined('APP_VERSION')) 
{
	if (file_exists (BASE_PATH.'.svn/entries')
		&& is_writable (BASE_PATH.'version.txt'))
	{
		$svn = file(BASE_PATH.'.svn/entries');
		if (is_numeric(trim($svn[10]))) 
		{
			$version = $svn[10];
		} 
		else 
		{ // pre 1.4 svn used xml for this file
			$version = explode('"', $svn[4]);
			$version = $version[1];    
		}
		
		$version = trim ($version);
	
		define ('APP_VERSION', $version);
		
		// Check if the version file is still up to date
		$fversion = file_get_contents (BASE_PATH.'version.txt');
		if ($fversion != $version)
		{
			file_put_contents (BASE_PATH.'version.txt', $version);
		}
	}
	else
	{
		// get the version from the "version file"
		define ('APP_VERSION', trim (@file_get_contents (BASE_PATH.'version.txt')));
	}
}

ini_set ('max_execution_time', 90);

// Define "now"
define ('NOW', time ());

// Config file has already defined TIME_ZONE using above function (or config.)
date_default_timezone_set (TIME_ZONE);

/*
	Stupid magic quotes
*/
// Strip magic quotes from request data.
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    // Create lamba style unescaping function (for portability)
    $quotes_sybase = strtolower(ini_get('magic_quotes_sybase'));
    $unescape_function = (empty($quotes_sybase) || $quotes_sybase === 'off') ? 'stripslashes($value)' : 'str_replace("\'\'","\'",$value)';
    $stripslashes_deep = create_function('&$value, $fn', '
        if (is_string($value)) {
            $value = ' . $unescape_function . ';
        } else if (is_array($value)) {
            foreach ($value as &$v) $fn($v, $fn);
        }
    ');
    
    // Unescape data
    $stripslashes_deep($_POST, $stripslashes_deep);
    $stripslashes_deep($_GET, $stripslashes_deep);
    $stripslashes_deep($_COOKIE, $stripslashes_deep);
    $stripslashes_deep($_REQUEST, $stripslashes_deep);
}


/*
	Auto load function:
	Real OOP!
	
	Loads the class /php/group/class.php by calling "new Group_Class ();"
*/
function __autoload ($class_name) 
{
	$v = explode ('_', $class_name);
	
	$p = count ($v) - 1;
	
	$url = '';
	foreach ($v as $k => $vv)
	{
		if ($k == $p)
		{
			$url .= '/'.$vv.'.php';
		}
		else 
		{
			$url .= '/'.$vv;
		}
	}
	
	foreach (explode (PATH_SEPARATOR, get_include_path ()) as $v)
	{
		if (file_exists ($v . $url))
		{
			require_once ($v . $url);
			return true;
		}
	}
	
	//echo get_include_path ();
	//throw new Exception ("Could not load class: " . $class_name);
	
	return false;
}

/*
	Replace the "mail" function by something more flexible.
*/
function customMail ($target, $subject, $msg)
{
	require_once ('Neuron/Core/PHPMailer.php');
	$mail = new Neuron_Core_PHPMailer ();

	$mail->IsSMTP();                                   // send via SMTP
	$mail->Host     = "mail.dolumar.be"; // SMTP servers
	$mail->Port = 587;
	$mail->SMTPAuth = true;     // turn on SMTP authentication
	$mail->Username = "noreply+dolumar.be";  // SMTP username
	$mail->Password = "aukv0006"; // SMTP password
	
	$mail->From     = "no-reply@dolumar.be";
	$mail->FromName = "Dolumar";

	$mail->CharSet  = 'utf-8';
	
	$mail->isHtml (false);
	
	$mail->AddAddress($target); 
	
	$mail->Subject  =  $subject;
	$mail->Body = $msg;
	
	$mail->Send ();
}

/*
	This function commands the game to reload the counters
*/
function reloadStatusCounters ()
{
	unset ($_SESSION['status_counters']);
}

// Reload function
function reloadEverything ()
{
	if (!defined ('RELOAD'))
	{
		define ('RELOAD', true);
	}
}

function utf8_urldecode($str) 
{
	$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
	return html_entity_decode($str,null,'UTF-8');;
}

function return_bytes($val) 
{
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last) 
	{
		// The 'G' modifier is available since PHP 5.1.0
		case 'g':
		$val *= 1024;
		case 'm':
		$val *= 1024;
		case 'k':
		$val *= 1024;
	}

	return $val;
}

/******************************** 
 * Retro-support of get_called_class() 
 * Tested and works in PHP 5.2.4 
 * http://www.sol1.com.au/ 
 ********************************/ 
if(!function_exists('get_called_class')) { 
function get_called_class($bt = false,$l = 1) { 
    if (!$bt) $bt = debug_backtrace(); 
    if (!isset($bt[$l])) throw new Exception("Cannot find called class -> stack level too deep."); 
    if (!isset($bt[$l]['type'])) { 
        throw new Exception ('type not set'); 
    } 
    else switch ($bt[$l]['type']) { 
        case '::': 
            $lines = file($bt[$l]['file']); 
            $i = 0; 
            $callerLine = ''; 
            do { 
                $i++; 
                $callerLine = $lines[$bt[$l]['line']-$i] . $callerLine; 
            } while (stripos($callerLine,$bt[$l]['function']) === false); 
            preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/', 
                        $callerLine, 
                        $matches); 
            if (!isset($matches[1])) { 
                // must be an edge case. 
                throw new Exception ("Could not find caller class: originating method call is obscured."); 
            } 
            switch ($matches[1]) { 
                case 'self': 
                case 'parent': 
                    return get_called_class($bt,$l+1); 
                default: 
                    return $matches[1]; 
            } 
            // won't get here. 
        case '->': switch ($bt[$l]['function']) { 
                case '__get': 
                    // edge case -> get class of calling object 
                    if (!is_object($bt[$l]['object'])) throw new Exception ("Edge case fail. __get called on non object."); 
                    return get_class($bt[$l]['object']); 
                default: return $bt[$l]['class']; 
            } 

        default: throw new Exception ("Unknown backtrace method type"); 
    } 
} 
} 

// Set include path
set_include_path (get_include_path () . PATH_SEPARATOR . CATLAB_BASEPATH . 'php' . PATH_SEPARATOR . PEAR_BASEPATH);

// Check for ref cookie
if (isset ($_GET['ref']))
{
	setcookie ('referee', $_GET['ref'], time() + 60*60, '/');
}

// Check for player referee cookie
if (isset ($_GET['pref']))
{
	setcookie ('preferrer', $_GET['pref'], time () + 60*60, '/');
}

if (isset ($_GET['bonus']))
{
	setcookie ('player_bonus', $_GET['bonus'], time () + 60*60, '/');
}

// Crazy hacks because of my outdated code
$_REQUEST = $_REQUEST;