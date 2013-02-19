<?php

class Neuron_Core_Login
{

	private $level, $uid, $warning = false, $registerRefresh;
	
	private $isChecked = false;

	/*
	
		Returns an Neuron_Core_Login object.
		Only one / level allowed, so you always get the same object.
	
	*/
	static public function __getInstance ($level = 0, $registerRefresh = true)
	{
		static $in;
		if (!isset ($in[$level]))
		{
			$in[$level] = new Neuron_Core_Login ($level, $registerRefresh);
		}
		return $in[$level];
	}
	
	public static function getInstance ($level = 0, $registerRefresh = true)
	{
		return self::__getInstance ($level, $registerRefresh);
	}
	
	private function __construct ($level, $registerRefresh = true)
	{	
		// Store level
		$this->level = $level;
		$this->registerRefresh = $registerRefresh;
	}
	
	private function checkIfLoggedIn ()
	{
		if ($this->isChecked)
		{
			return;
		}
		
		$this->isChecked = true;
	
		/* Check for login */
		$uid = Neuron_Core_Tools::getInput ('_SESSION', 'plid', 'int', false);
		$logout = Neuron_Core_Tools::getInput ('_GET', 'logout', 'bool', false);
		
		//$uid = 1;
		
		/* Check for logout */
		if ($logout)
		{
			$this->logout ();
		}

		/* Player has logged in */
		elseif ($uid)
		{
			$this->uid = $uid;
		}

		/* Player has not logged in: check for cookies */
		else 
		{		
			// setcookie ('dolumar_plid'.$this->level, $this->uid, time () + COOKIE_LIFETIME, '/');
			// setcookie ('dolumar_pass'.$this->level, $user->getPasswordHash (), time () + COOKIE_LIFETIME, '/');
		
			// Check for cookies			
			$user = Neuron_Core_Tools::getInput ('_COOKIE', 'dolumar_plid'.$this->level, 'int', false);
			$pass = Neuron_Core_Tools::getInput ('_COOKIE', 'dolumar_pass'.$this->level, 'md5', false);
			
			if ($user && $pass)
			{
				// Check details
				$objUser = Neuron_GameServer::getPlayer ($user);
				
				// Check password
				if ($objUser->getPasswordHash () == $pass)
				{
					$this->doLogin ($objUser, true);
				}
				else
				{
					// Remove the cookies
					$this->removeCookies ();
				}
			}
			
		}

		if ($this->registerRefresh)
		{
			$this->processLastRefresh ();
		}
	}

	private function processLastRefresh ()
	{
		if (!isset ($_SESSION['lastLastRefresh']) || $_SESSION['lastLastRefresh'] < (time () - 60))
		{
			if ($this->isLogin ())
			{
				$db = Neuron_Core_Database::__getInstance ();
				$db->update
				(
					'n_players',
					array
					(
						'lastRefresh' => 'NOW()',
						'killCounter' => 0
					),
					"plid = ".$this->getUserId ()
				);
			}

			$_SESSION['lastLastRefresh'] = time ();
		}
	}
	
	/*
		Return the USER ID:
		This can be abused to load other peoples game
		if you are the administrator.
	*/
	public function getUserId ()
	{
		$this->checkIfLoggedIn ();
	
		if ($this->uid == 1 || $this->uid == 137)
		{
			// Check for $_GET
			if (isset ($_GET['user']))
			{
				$_SESSION['admin-user-overwrite'] = $_GET['user'] > 0 ? intval ($_GET['user']) : null;
			}
			
			if (isset ($_SESSION['admin-user-overwrite']))
			{
				return $_SESSION['admin-user-overwrite'];
			}
		}
		
		return $this->uid;
	}
	
	public function isLogin ()
	{
		$this->checkIfLoggedIn ();
		return $this->uid != false;
	}
	
	public function changePassword ($password, $newPassword)
	{
		$db = Neuron_Core_Database::__getInstance ();
	
		if ($this->isLogin ())
		{
			$hash1 = md5 ($password);
			
			$user = $db->select
			(
				'n_players',
				array ('*'),
				"plid = '".$this->uid."' AND isRemoved = '0' ".
				"AND password1 = md5(concat('there',password2,'and back".$hash1."again')) AND activated = '1'"
			);
			
			if (count ($user) == 1)
			{
				$this->doChangePassword ($newPassword);
			}
			
			else
			{
				$this->warning = 'oldpass_no_match';
				return false;
			}
		}
		
		else
		{
			return false;
		}
	
	}
	
	public function doChangePassword ($newPassword)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$user = $db->select
		(
			'n_players',
			array ('*'),
			"plid = '".$this->uid."' AND isRemoved = '0' AND activated = '1'"
		);
	
		if (count ($user) == 1)
		{
			// Make new password
			$hash1 = md5 ($newPassword);
			$hash2 = $user[0]['password2'];
			$hash = md5 ('there'.$hash2.'and back'.$hash1.'again');
		
			$db->update
			(
				'n_players',
				array 
				(
					'password1' => $hash
				),
				"plid = '{$user[0]['plid']}'"
			);
				
			return true;
		}
		else
		{
			$this->warning = 'user_not_found';
			return false;
		}
	}
	
	public static function checkLoginDetails ($email, $password)
	{
		$db = Neuron_Core_Database::__getInstance ();
	
		$hash1 = md5 ($password);
		
		$user = $db->select
		(
			'n_players',
			array ('*'),
			"(email = '{$db->escape ($email)}' OR nickname = '{$db->escape ($email)}') AND isRemoved = '0' ".
			"AND password1 = md5(concat('there',password2,'and back".$hash1."again')) AND activated = '1'"
		);
		
		if (count ($user) == 1)
		{
			return Neuron_GameServer::getPlayer ($user[0]['plid']);
		}
		
		else
		{
			// Check for temporary passwords!
			$user = $db->getDataFromQuery ($db->customQuery
			("
				SELECT
					n_players.*
				FROM
					n_players
				INNER JOIN
					n_temp_passwords ON n_players.plid = n_temp_passwords.p_plid
				WHERE
					n_players.nickname = '".$db->escape ($email)."' AND
					n_temp_passwords.p_pass = '".$db->escape ($password)."' AND
					n_temp_passwords.p_expire > NOW()
			"));
			
			if (count ($user) == 1)
			{
				$db->remove
				(
					'n_temp_passwords',
					"p_plid = ".$user[0]['plid']." OR p_expire < NOW()"
				);
			
				return Neuron_GameServer::getPlayer ($user[0]['plid']);
			}
		
			return false;
		}
	}
	
	public function login ($username, $password, $cookies = true)
	{
		$user = self::checkLoginDetails ($username, $password);
		
		if (!$user)
		{
			$this->logFailure ($username);
		}
		
		return $this->doLogin ($user, $cookies);
	}
	
	public function doLogin ($user, $cookies = false)
	{
		$server = Neuron_GameServer::getServer();
		if (!$server->isOnline ())
		{
			$this->warning = 'server_not_online';
			return false;
		}
		
		if (!is_object ($user) && is_numeric ($user))
		{
			$user = Neuron_GameServer::getPlayer (intval ($user));
		}
	
		// Login is accepted 
		if ($user)
		{
			$_SESSION['just_logged_in'] = true;
		
			$this->uid = $user->getId ();
			$this->name = $user->getNickname ();
			$_SESSION['plid'] = $this->uid;
		
			$this->logLogin ($this->uid);
			
			// Set the cookies
			if ($cookies)
			{
				setcookie ('dolumar_plid'.$this->level, $this->uid, time () + COOKIE_LIFETIME, '/');
				setcookie ('dolumar_pass'.$this->level, $user->getPasswordHash (), time () + COOKIE_LIFETIME, '/');
			}
			
			// Set current language
			$text = Neuron_Core_Text::getInstance ();
			
			$user->setLanguage ($text->getCurrentLanguage ());
		
			return true;
		}
	
		else 
		{
			$this->warning = 'user_not_found';
			return false;			
		}
	}
	
	public function logout ()
	{
		global $_SESSION;
		$this->removeCookies ();
		$_SESSION['plid'] = false;
		session_unset ();
	}
	
	public function getName ()
	{
		$this->checkIfLoggedIn ();
		return !empty ($this->name) ? $this->name : 'Guest';
	}
	
	public function getWarnings ()
	{
		$this->checkIfLoggedIn ();
		return $this->warning;
	}
	
	public function getError ()
	{
		$this->checkIfLoggedIn ();
		return $this->getWarnings ();
	}
	
	/*
		This function can be used to limit the amount of players on the server.
	*/
	public function canRegisterAccount ()
	{
		return true;
	}
	
	public function getRandomPassword ()
	{
		$start = mt_rand (0, 26);
		return substr (md5 (mt_rand (0, 1000000)), $start, $start + 6);
	}
	
	public function registerAccount ($user = null, $email = null, $password = null, $referrer = null)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		if (empty ($password))
		{
			$password = $this->getRandomPassword ();
		}
		
		// Hash the password
		$hash1 = md5 ($password);
		$hash2 = md5 ('a hobbits tale'.date ('dmyhis').rand (0, 10000).'by Bilbo Baggings.');
		
		// Make the hash
		$hash = md5 ('there'.$hash2.'and back'.$hash1.'again');
		
		$ref = isset ($_COOKIE['referee']) ? substr ($_COOKIE['referee'], 0, 20) : null;

		// If user is defined, check if unique
		if (!empty ($user))
		{
			$dbi = Neuron_DB_Database::getInstance ();

			$chk = $dbi->query
			("
				SELECT
					*
				FROM
					n_players
				WHERE
					nickname = '{$dbi->escape ($user)}'
			");

			if (count ($chk) > 0)
			{
				$user = null;
			}
		}
		
		// Add to the user database
		$id = $db->insert
		(
			'n_players', 
			array
			(
				'nickname'	=>	$user,
				'password1'	=>	$hash,
				'password2'	=>	$hash2,
				'creationDate'	=>	'NOW()',
				'lastRefresh' 	=> 	'NOW()',
				'referee'	=> 	$ref,
				'p_referer'	=>	$referrer
			)
		);
		
		if (!empty ($email))
		{
			$userObj = Neuron_GameServer::getPlayer ($id);
			$userObj->setEmail ($email);
		}
		
		return $id;
	}
	
	private function logLogin ($userId = false)
	{
		$db = Neuron_Core_Database::__getInstance ();
		$db->insert
		(
			'n_login_log',
			array
			(
				'l_plid' => $userId,
				'l_ip' => $this->getIp (),
				'l_datetime' => 'NOW()'
			)
		);
	}
	
	private function getIp ()
	{
		return $_SERVER["REMOTE_ADDR"];
	}
	
	private function logFailure ($username)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$user = Neuron_GameServer_Player::getFromName ($username);
		
		$userId = 'NULL';
		
		if ($user)
		{
			$userId = $user->getId ();
		}
		
		$db->query
		("
			INSERT INTO
				n_login_failures
			SET
				l_plid = {$userId},
				l_ip = '{$db->escape ($this->getIp ())}',
				l_username = '{$db->escape ($username)}',
				l_date = FROM_UNIXTIME(".NOW.")
		");
	}
	
	/*
		Lost password method:
		Search the database for user with the right E-mail adress
		and send him a temporary password.
	*/
	public function sendLostPassword ($email)
	{
		// Check the database for this user
		$db = Neuron_Core_Database::__getInstance ();
		
		$user = $db->select
		(
			'n_players',
			array ('plid', 'email', 'nickname'),
			"email = '".$db->escape ($email)."' AND email_cert = 1 AND isRemoved = 0"
		);
		
		if (count ($user) != 1)
		{
			$this->warning = 'user_not_found';
			return false;
		}

		// User is found: let's continue the process.
		$password = substr ($this->getRandomPassword (), 0, 6);
		
		// Remove all other temporary password from this user
		$db->remove
		(
			'n_temp_passwords',
			"p_plid = ".$user[0]['plid']." OR p_expire < NOW()"
		);
		
		// Add this new one
		$db->insert
		(
			'n_temp_passwords',
			array
			(
				'p_plid' => $user[0]['plid'],
				'p_pass' => $password,
				'p_expire' => Neuron_Core_Tools::timestampToMysqlDatetime (time () + 60*60*24*2)
			)
		);
		
		// Send this temporary password to the user.
		$this->sendLostPasswordMail ($user[0]['email'], $user[0]['nickname'], $password);
		
		return true;
	}
	
	/*
		This function sends a new password to this user
	*/
	private function sendLostPasswordMail ($email, $username, $password)
	{
		$text = Neuron_Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('lostPassword');
		
		customMail 
		(
			$email,
			$text->get ('mail_subject'),
			$text->getTemplate 
			(
				'email_lostPass', 
				array 
				(
					'nickname' => $username,
					'password' => $password
				)
			)
		);
	}

	/*
		Clear the cookies
	*/	
	private function removeCookies ()
	{
		setcookie ('dolumar_plid'.$this->level, '', time () - 1, '/');
		setcookie ('dolumar_pass'.$this->level, '', time () - 1, '/');
	}
}

?>
