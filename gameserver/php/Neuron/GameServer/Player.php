<?php
if (!defined ('ALLOW_VACATION_MODE'))
{
	define ('ALLOW_VACATION_MODE', true);
}

class Neuron_GameServer_Player 
	extends Neuron_Core_ModuleFactory 
	implements Neuron_GameServer_Interfaces_Logable
{
	/*
		To enable the factory to do it's work,
		overload the loadModule function
		
		Return an object of the required module
		The factory will make sure that it is only loaded once
	*/
	protected function loadModule ($sModule)
	{
		$classname = 'Neuron_GameServer_Player_'.ucfirst ($sModule);
		if (!class_exists ($classname))
		{
			throw new Exception ('Module '.$sModule.' ('.$classname.') does not exist.');
		}
		return new $classname ($this);
	}
	
	public function init ()
	{
		//
	}

	/*
		Required by Interfaces_Logable:
	*/
	public static function getFromId ($id)
	{
		return Neuron_GameServer::getPlayer ($id);
	}

	public static function getFromOpenID ($openid)
	{
		$db = Neuron_Core_Database::__getInstance ();

		// See if there is an account available
		$acc = $db->select
		(
			'auth_openid',
			array ('user_id'),
			"openid_url = '".$db->escape ($openid)."'"
		);

		if (count ($acc) > 0)
		{
			return self::getFromId ($acc[0]['user_id']);
		}

		return false;
	}
	
	/*
		Check if a player name exists.
	*/
	public static function playerNameExists ($nickname)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$data = $db->select
		(
			'players',
			array ('plid'),
			"nickname = '".$db->escape ($nickname) . "' AND isRemoved = 0"
		);

		return count ($data) > 0;
	}
	
	/*
		Return a player from nickname
	*/
	public static function getFromName ($nickname)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$data = $db->select
		(
			'players',
			array ('plid'),
			"nickname = '".$db->escape ($nickname) . "' AND isRemoved = 0"
		);
		
		if (count ($data) == 1)
		{
			return self::getFromId ($data[0]['plid']);
		}
		return false;
	}
	
	public static function getAdminModes ()
	{
		return array
		(
			9 => 'Developer',
			6 => 'Administrator',
			4 => 'Moderator',
			2 => 'Chatmod',
			0 => 'Player'
		);
	}
	
	private $id;
	private $data;
	private $gameData;
	
	private $isFound;
	private $isPlaying;
	
	private $gameTriggerObj = null;
	public $error = null;
	protected $village_insert_id = false;
	
	private $objCredits = null;
	
	private $sPreferences = array ();
	
	private $iSocialStatuses = null;
	
	private $bans = null;
	
	public function __construct ($playerId)
	{
		$this->id = $playerId;
	}

	public function setData ($data)
	{
		$this->data = $data;
		$this->isFound = true;
	}
	
	public function getId ()
	{
		return (int)$this->id;
	}
	
	public function reloadData ()
	{
		$this->data = null;
	}
	
	private function loadData ()
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		if (!isset ($this->data))
		{
			$r = $db->getDataFromQuery ($db->customQuery
			("
				SELECT
					*
				FROM
					players
				WHERE
					players.plid = '".$this->getId ()."'
			"));
			
			if (count ($r) == 1)
			{
				$this->setData ($r[0]);
			}
			
			else 
			{
				$this->isFound = false;
			}
		}
	}
	
	/*
		Return all data (used by extended classes)
	*/
	protected function getData ()
	{
		$this->loadData ();
		return $this->data;
	}
	
	public function isFound ()
	{
		$this->loadData ();
		return $this->isFound;
	}
	
	public function isPlaying ()
	{
		$this->loadData ();
		return
		(
			$this->isFound &&
			!empty ($this->data['nickname'])
			&& ($this->data['isPlaying'] == 1)
		);
	}
	
	public function getPasswordHash ()
	{
		$this->loadData ();
		return isset ($this->data['password1']) ? $this->data['password1'] : null;
	}

	public function isNicknameSet ()
	{
		$this->loadData ();
		return !empty ($this->data['nickname']);
	}
	
	public function getNickname ()
	{
		$this->loadData ();
		return !empty ($this->data['nickname']) ? $this->data['nickname'] : 'Guest ' . $this->getId ();
	}
	
	public function getName ()
	{
		$this->loadData ();
		return $this->getNickname ();
	}
	
	/*
		Return the HTML version of the name.
	*/
	public function getDisplayName ()
	{
		$flags = '';
		
		if ($this->isProperPremium ())
		{
			$flags .= 'premium ';
		}
		
		if ($this->isModerator ())
		{
			$flags .= 'moderator ';
		}
		
		$string = '<span class="nickname '.$flags.'">';
		$nickname = Neuron_Core_Tools::output_varchar ($this->getName ());
		$string .= Neuron_URLBuilder::getInstance ()->getOpenUrl ('PlayerProfile', $nickname, array ('plid' => $this->getId ()));
		$string .= '</span>';
	
		return $string;
	}
	
	public function getEmail ()
	{
		$this->loadData ();
		return $this->data['email'];
	}
	
	public function isEmailSet ()
	{
		$this->loadData ();
		return Neuron_Core_Tools::checkInput ($this->data['email'], 'email');
	}
	
	/**
	*	This method will check if an email address is set
	*	and verified OR if an OpenID account is set (in which case)
	*	there is no email required.
	*/
	public function isEmailVerified ()
	{
		$openid = isset ($_SESSION['neuron_openid_identity']) ? 
			md5 ($_SESSION['neuron_openid_identity']) : false;
		
		return $this->isFound () && ($this->isEmailCertified () || $openid);
	}
	
	public function isEmailCertified ()
	{
		$this->loadData ();
		return $this->data['email_cert'] == '1';
	}
	
	public function sendCertificationMail ()
	{
		if ($this->isEmailSet ())
		{
			// Yeah yeah, mailing stuff.
			$text = Neuron_Core_Text::__getInstance ();
			customMail 
			(
				$this->getEmail (),
				$text->get ('mail_subject', 'choosemail', 'account'),
				$text->getTemplate ('email_cert', array 
				(
					Neuron_Core_Tools::output_varchar ($this->getNickname ()),
					API_FULL_URL.'emailcert?id='.$this->getId ().'&certkey='.$this->data['email_cert_key']
				))
			);
		}
	}
	
	public function certifyEmail ($key)
	{
		$this->loadData ();
	
		$db = Neuron_Core_Database::__getInstance ();
		$db->update
		(
			'players',
			array
			(
				'email_cert' => 1
			),
			"plid = '".$this->getId ()."' AND email_cert_key = '".$db->escape ($key)."'"
		);
		
		$okay = $this->data['email_cert'] == 0;
		$this->data['email_cert'] = 1;
		
		// Count the credits to register email with master server
		$this->getCredits ();
		
		// Give the refering user a bonus!
		$referer = intval ($this->data['p_referer']);		
		$referer = $referer > 0 ? Neuron_GameServer::getPlayer ($this->data['p_referer']) : false;
		
		if ($referer && $okay)
		{
			$referer->giveReferralBonus ($this);
		}
	}
	
	public function doesEmailExist ($email)
	{
		$db = Neuron_Core_Database::__getInstance ();
		$l = $db->select
		(
			'players',
			array ('plid'),
			"email = '".$db->escape ($email)."'"
		);
		
		return count ($l) > 0;
	}
	
	public function setEmail ($email)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		if (Neuron_Core_Tools::checkInput ($email, 'email'))
		{
			if (!$this->doesEmailExist ($email))
			{
				$key = md5 (time () + rand (0, 99999));
			
				$db->update
				(
					'players',
					array
					(
						'email' => $email,
						'email_cert' => 0,
						'email_cert_key' => $key
					),
					"plid = '".$this->getId ()."'"
				);
			
				$this->data['email'] = $email;
				$this->data['email_cert'] = 0;
				$this->data['email_cert_key'] = $key;
			
				$this->sendCertificationMail ();
				
				return true;
			}
			else
			{
				$this->error = 'email_exists';
				return false;
			}
		}
		else
		{
			$this->error = 'invalid_syntax';
			return false;
		}
	}
	
	/*
		Only for admin panel
	*/
	public function getAdminStatus ()
	{
		$this->loadData ();
		return $this->data['p_admin'];
	}
	
	public function setAdminStatus ($status)
	{
		$status = intval ($status);
		
		$db = Neuron_DB_Database::getInstance ();
		
		$db->query
		("
			UPDATE
				players
			SET
				p_admin = $status
			WHERE
				plid = {$this->getId ()}
		");
		
		$this->data['p_admin'] = $status;
	}
	
	public function getAdminStatusString ()
	{
		$states = $this->getAdminModes ();
		$k = $this->getAdminStatus ();
		return isset ($states[$k]) ? $states[$k] : null;
	}

	public function isDeveloper ()
	{
		$this->loadData ();
		
		if (!isset ($this->data['p_admin']))
		{
			throw new Neuron_Core_Error ('Data could not be loaded: ' . $this->id);
		}
		
		return $this->data['p_admin'] >= 9;
	}
	
	public function isAdmin ()
	{
		$this->loadData ();
		
		if (!isset ($this->data['p_admin']))
		{
			throw new Neuron_Core_Error ('Data could not be loaded: ' . $this->id);
		}
		
		return $this->data['p_admin'] >= 6;
	}
	
	public function isModerator ()
	{
		$this->loadData ();
		
		if (!isset ($this->data['p_admin']))
		{
			throw new Neuron_Core_Error ('Data could not be loaded: ' . $this->id);
		}
		
		return $this->data['p_admin'] >= 4;
	}

	public function isChatModerator ()
	{
		$this->loadData ();
		
		if (!isset ($this->data['p_admin']))
		{
			throw new Neuron_Core_Error ('Data could not be loaded.');
		}
		
		return $this->data['p_admin'] >= 2;
	}

	public function isPremium ()
	{
		if ($this->isProperPremium ())
		{
			return true;
		}
	
		if (defined ('FREE_PREMIUM') && FREE_PREMIUM)
		{
			return true;
		}
	
		// Let's move to the "other servers".	
		$this->loadData ();
		
		return $this->getPremiumEndDate () > time ();

		/*
		if (strtotime ($this->data['creationDate']) > (time () - 24 * 60 * 60 * 7))
		{
			return true;
		}
		elseif (strtotime ($this->data['premiumEndDate']) > time ())
		{
			return true;
		}
		else
		{
			return false;
		}
		*/
	}
	
	public function isProperPremium ()
	{
		if ($this->isModerator ())
		{
			return true;
		}
	
		$this->loadData ();
		return strtotime ($this->data['premiumEndDate']) > time ();
	}
	
	public function getPremiumEndDate ()
	{
		$this->loadData ();
		return max
		(
			strtotime ($this->data['sponsorEndDate']),
			strtotime ($this->data['premiumEndDate']),
			(strtotime ($this->data['creationDate']) + 24 * 60 * 60 * 7)
		);
	}

	public function showAdvertisement ()
	{
		$this->loadData ();
		if ($this->data['showAdvertisement'] == 1)
		{
			return true;
		}
		else
		{
			return !$this->isPremium ();
		}
	}

	public function extendPremiumAccount ($duration = 86400)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$this->loadData ();
		
		if (strtotime ($this->data['premiumEndDate']) > time ())
		{
			$start = strtotime ($this->data['premiumEndDate']);
		}
		else
		{
			$start = time ();
		}

		$db->update
		(
			'players',
			array
			(
				'premiumEndDate' => Neuron_Core_Tools::timeStampToMysqlDatetime ($start + $duration)
			),
			"plid = '".$this->getId ()."'"
		);
		
		$this->data['premiumEndDate'] = Neuron_Core_Tools::timeStampToMysqlDatetime ($start + $duration);
	}
	
	/*
		Set this users language
	*/
	public function setLanguage ($sLang)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		if (strlen ($sLang) > 5)
		{
			return false;
		}
		
		$db->query
		("
			UPDATE
				players
			SET
				p_lang = '{$db->escape ($sLang)}'
			WHERE
				plid = {$this->getId ()}
		");
		
		$this->reloadData ();
	}

	public function getRightLanguage ()
	{
		$this->loadData ();
		
		$lang = isset ($this->data['p_lang']) ? $this->data['p_lang'] : false;
		if ($lang)
		{
			return new Neuron_Core_Text ($lang);
		}
		else
		{
			return Neuron_Core_Text::__getInstance ();
		}
	}

	public function getLoginURL ()
	{
		return ABSOLUTE_URL;
	}

	public function isValidUnsubsribeKey ($key)
	{
		return $this->getUnsubscribeKey () === $key;
	}

	public function getUnsubscribeKey ()
	{
		// Not very secure, but it doesn't have to be very secure.
		return substr (md5 ($this->getNickname () . ':' . $this->getId () . ':' . $this->getEmail () . ':' . 'dolumar rules'), 0, 6);
	}

	public function getUnsubscribeURL ()
	{
		$key = $this->getUnsubscribeKey ();
		return API_FULL_URL.'unsubscribe?id='.$this->getId ().'&key=' . $key;
	}

	public function invitePeople 
	(
		$txtMsgKeySender, $txtMsgSectionSender,
		$txtMsgKeyReceiver, $txtMsgSectionReceiver,
		$maxPerInterval = 1, $maxReceiverPerInterval = 0, $interval = 604800,
		$inputData = array ()
	)
	{
		// First: load this users OpenID notification urls
		$db = Neuron_DB_Database::__getInstance ();
		
		$openid_rows = $db->query
		("
			SELECT
				notify_url
			FROM
				auth_openid
			WHERE
				user_id = {$this->getId()}
				AND notify_url IS NOT NULL
				AND notify_url != ''
		");

		$text = $this->getRightLanguage ();
		
		if (count ($openid_rows) > 0)
		{
	
			$server = Neuron_GameServer::getInstance ()->getServer ();
			$servername = new Neuron_GameServer_Logable_String ($server->getServerName ());

			$inputData = array_merge (array ('sender' => $this, 'server' => $servername), $inputData);

			$keyvalues = array ();
			foreach ($inputData as $k => $v)
			{
				$keyvalues[$k] = $v->getName ();
			}

			$senderMessage = Neuron_Core_Tools::putIntoText ($text->get ($txtMsgKeySender, $txtMsgSectionSender, 'notifications'), $keyvalues);
			$receiverMessage = Neuron_Core_Tools::putIntoText ($text->get ($txtMsgKeyReceiver, $txtMsgSectionReceiver, 'notifications'), $keyvalues);

			// Load OpenID accounts and send Browser Games Hub notifications
			$objNot = new BrowserGamesHub_Invitation ($senderMessage, $receiverMessage, $maxPerInterval, $maxReceiverPerInterval, $interval, $text->getCurrentLanguage ());
			
			$objNot->setIcon (STATIC_URL . 'icon.png');
			$objNot->setId ($txtMsgKeySender, $txtMsgSectionSender);
			$objNot->setSenderData ($this->getBrowserBasedGamesData ());

			// Keep in mind that the notification does not like actual names,
			// so we will replace all key names with their numeric value.
			$keys = array_keys ($inputData);
			$replace_keys = array ();
			foreach ($keys as $k => $v)
			{
				if ($v != 'actor')
				{
					$replace_keys[$v] = '{'.$k.'}';
				}
				else
				{
					$replace_keys[$v] = '{actor}';
				}
			}
			
			$objNot->setSkeletonSender
			(
				Neuron_Core_Tools::putIntoText
				(
					$text->get ($txtMsgKeySender, $txtMsgSectionSender, 'notifications'),
					$replace_keys
				)
			);

			$objNot->setSkeletonReceiver
			(
				Neuron_Core_Tools::putIntoText
				(
					$text->get ($txtMsgKeyReceiver, $txtMsgSectionReceiver, 'notifications'),
					$replace_keys
				)
			);

			$callback = API_FULL_URL.'invitation/?id='.$this->getId();
			$objNot->setCallback ($callback);
		
			// Take all the value strings and put them in there aswell
			foreach ($inputData as $v)
			{
				if ($v instanceof Dolumar_Players_Player)
				{
					$objNot->addArgument ($v->getName (), 'user', $v->getBrowserBasedGamesData ());
				}
			
				elseif ($v instanceof Neuron_GameServer_Interfaces_Logable)
				{
					$objNot->addArgument ($v->getName (), 'text');
				}
				else
				{
					$objNot->addArgument ($v, 'text');
				}
			}
		
			// Send the notification
			foreach ($openid_rows as $v)
			{
				return $objNot->send ($v['notify_url']);
			}
		}

		return array ('success' => false, 'error' => 'No OpenID providers set.');
	}

	/*
		Various notifications are called
	*/
	public function sendNotification ($txtMsgKey, $txtMsgSection, $inputData = array (), $objSender = null, $isPublic = false)
	{
		$text = $this->getRightLanguage ();
		
		// Add "actor" to inputdata
		$inputData['actor'] = $this;

		$newArray = array ();
		$plainArray = array ();
		foreach ($inputData as $k => $v)
		{
			if ($v instanceof Neuron_GameServer_Interfaces_Logable)
			{
				$newArray[$k] = Neuron_Core_Tools::output_varchar ($v->getName ());
				$plainArray[$k] = $v->getName ();
			}
			else
			{
				$newArray[$k] = Neuron_Core_Tools::output_varchar ($v);
				$plainArray[$k] = $v;
			}
		}

		$msg = Neuron_Core_Tools::putIntoText
		(
			$text->get ($txtMsgKey, $txtMsgSection, 'notifications'),
			$newArray
		);
		
		$msg_plaintext = Neuron_Core_Tools::putIntoText 
		(
			$text->get ($txtMsgKey, $txtMsgSection, 'notifications'),
			$plainArray
		);

		// Notify player gametrigger
		//$this->callGameTrigger ('sendNotification', array ($msg, $txtMsgSection, $txtMsgKey, $inputData));
		
		$this->sendOpenIDNotifications ($msg_plaintext, $txtMsgKey, $txtMsgSection, $inputData, $objSender, $isPublic);

		// Also send email
		$this->sendNotificationEmail ($msg_plaintext, $txtMsgKey, $txtMsgSection, $inputData, $objSender, $isPublic);
	}

	private function sendNotificationEmail ($msg_plaintext, $txtMsgKey, $txtMsgSection, $inputData, $objSender, $isPublic)
	{
		$text = $this->getRightLanguage ();

		// If online? Don't do anything
		if ($this->isOnline ())
		{
			return;
		}

		// Also send email
		if ($this->isEmailSet () && $this->getPreference ('emailNotifs', true))
		{
			if (!$isPublic)
			{
				customMail 
				(
					$this->getEmail (),
					$msg_plaintext,
					$text->getTemplate ('email_notification', array 
					(
						'nickname' => Neuron_Core_Tools::output_varchar ($this->getNickname ()),
						'message' => $msg_plaintext,
						'loginurl' => $this->getLoginURL (),
						'unsubsribe' => $this->getUnsubscribeURL ()
					))
				);
			}
		}
	}
	
	protected function onSendNotifications (BrowserGamesHub_Notification $notification)
	{
		// Do nothing.
	}
	
	private function sendOpenIDNotifications ($msg, $txtMsgKey, $txtMsgSection, $inputData, $objSender, $isPublic)
	{
		// First: load this users OpenID notification urls
		$db = Neuron_DB_Database::__getInstance ();
		
		$openid_rows = $db->query
		("
			SELECT
				notify_url
			FROM
				auth_openid
			WHERE
				user_id = {$this->getId()}
				AND notify_url IS NOT NULL
				AND notify_url != ''
		");
		
		if (count ($openid_rows) > 0)
		{
			$text = $this->getRightLanguage ();
	
			// Load OpenID accounts and send Browser Games Hub notifications
			$objNot = new BrowserGamesHub_Notification ($msg, time (), $text->getCurrentLanguage ());
			
			$objNot->setIcon (STATIC_URL . 'icon.png');

			$objNot->setId ($txtMsgSection, $txtMsgKey);

			$objNot->setTargetData ($this->getBrowserBasedGamesData ());
			
			if ($objSender instanceof Dolumar_Players_Player)
			{
				$objNot->setSenderData ($objSender->getBrowserBasedGamesData ());
			}

			// Keep in mind that the notification does not like actual names,
			// so we will replace all key names with their numeric value.
			$keys = array_keys ($inputData);
			$replace_keys = array ();
			foreach ($keys as $k => $v)
			{
				if ($v != 'actor')
				{
					$replace_keys[$v] = '{'.$k.'}';
				}
				else
				{
					$replace_keys[$v] = '{target}';
				}
			}
			
			$objNot->setSkeleton 
			(
				Neuron_Core_Tools::putIntoText
				(
					$text->get ($txtMsgKey, $txtMsgSection, 'notifications'),
					$replace_keys
				)
			);
			
			$desc = $text->get ($txtMsgKey.'_long', $txtMsgSection, 'notifications', '');
			if (!empty ($desc))
			{
				$objNot->setDescription 
				(
					Neuron_Core_Tools::putIntoText
					(
						$desc,
						$replace_keys
					)
				);
			}
		
			// Take all the value strings and put them in there aswell
			foreach ($inputData as $v)
			{
				if ($v instanceof Dolumar_Players_Player)
				{
					$objNot->addArgument ($v->getName (), 'user', $v->getBrowserBasedGamesData ());
				}
			
				elseif ($v instanceof Neuron_GameServer_Interfaces_Logable)
				{
					$objNot->addArgument ($v->getName (), 'text');
				}
				else
				{
					$objNot->addArgument ($v, 'text');
				}
			}
			
			// Visibliity
			$objNot->setVisibility ($isPublic ? 'public' : 'private');
			
			$this->onSendNotifications ($objNot);
		
			// Send the notification
			foreach ($openid_rows as $v)
			{
				$objNot->send ($v['notify_url']);
			}
		}
	}
	
	/*
		Updates this players profile box (if necesarry)
	*/
	public function updateProfilebox ()
	{
		/*
		// First: load this users OpenID notification urls
		$db = Neuron_DB_Database::__getInstance ();
		
		$openid_rows = $db->query
		("
			SELECT
				profilebox_url
			FROM
				auth_openid
			WHERE
				user_id = {$this->getId()}
				AND profilebox_url IS NOT NULL
				AND profilebox_url != ''
		");
		
		$village = $this->getMainVillage ();
		
		if ($village)
		{
			list ($x, $y) = $village->buildings->getTownCenterLocation ();
		
			$imgurl = ABSOLUTE_URL.'image/snapshot/?x='.$x.'&y='.$y.'&zoom=30&width=184&height=250&slogan='.urlencode ($village->getName ()).'&timestamp='.time();
			$content = '<img src="'.$imgurl.'" alt="'.Neuron_Core_Tools::output_varchar ($village->getName ()).'" title="'.Neuron_Core_Tools::output_varchar ($village->getName ()).'" />';
		
			$objNot = new BrowserGamesHub_Profilebox ();
			$objNot->setContent ($content);
		
			// Send the notification
			foreach ($openid_rows as $v)
			{
				$objNot->send ($v['profilebox_url']);
			}
		}
		*/
	}
	
	/*
		Send all user data (nickname, score, etc) to the OpenID provider.
	*/
	private function sendUserData ()
	{
		/*
		$profiler = Neuron_Profiler_Profiler::getInstance ();
		
		$profiler->start ('Sending user data to OpenID providers.');
	
		// First: load this users OpenID notification urls
		$db = Neuron_DB_Database::__getInstance ();
		
		$openid_rows = $db->query
		("
			SELECT
				userstats_url
			FROM
				auth_openid
			WHERE
				user_id = {$this->getId()}
				AND userstats_url IS NOT NULL
				AND userstats_url != ''
		");
		
		if (count ($openid_rows) > 0)
		{
			$stats = new BrowserGamesHub_Userstats ();
			
			$stats->setData ($this->getBrowserBasedGamesData ());
		
			foreach ($openid_rows as $v)
			{
				//$profiler->start ('Contacting ' . $v['userstats_url']);
				$stats->send ($v['userstats_url']);
				//$profiler->stop ();
			}
		}
		
		$profiler->stop ();
		*/

		// First: load this users OpenID notification urls
		$db = Neuron_DB_Database::__getInstance ();
		
		$openid_rows = $db->query
		("
			SELECT
				notify_url
			FROM
				auth_openid
			WHERE
				user_id = {$this->getId()}
				AND notify_url IS NOT NULL
				AND notify_url != ''
		");
		
		if (count ($openid_rows) > 0)
		{
			$information = $this->getBrowserBasedGamesData ();
			$statistics = $this->getStatistics ();

			// Send the notification
			foreach ($openid_rows as $v)
			{
				$stat = new BrowserGamesHub_Statistics ($statistics, $information);
				$stat->send ($v['notify_url']);
			}
		}
	}

	public function getStatistics ()
	{
		return array
		(
			'score' => $this->getScore ()
		);
	}
	
	/*
		This function is called whenever the score gets updated.
	*/
	public function updateScore ()
	{
		$this->sendUserData ();
	}

	/*
		Game triggers can be used to notify players on different websites.
		A player can have a trigger class attached to him. In that case
		all triggers of this class are used.
	*/
	public function callGameTrigger ($function, $arguments = array ())
	{
		/*
		if (!is_array ($arguments))
		{
			$arguments = array ($arguments);
		}
	
		$this->loadData ();

		if ($this->isPlaying () && !empty ($this->data['authType']))
		{
			$auth = 'OpenAuth_GameTriggers_' . ucfirst ($this->data['authType']);
		
			try
			{
				if ($this->gameTriggerObj === null)
				{
					if (file_exists ('openauth/'.strtolower ($auth).'/GameTriggers.php'))
					{
						include ('openauth/'.strtolower ($auth).'/GameTriggers.php');
						if (class_exists ($auth))
						{
							$this->gameTriggerObj = new $auth ($this);
						}
						else
						{
							$this->gameTriggerObj = false;
						}
					}
				}

				if ($this->gameTriggerObj)
				{
					call_user_func_array (array ($this->gameTriggerObj, $function), $arguments);
				}
			}
			catch (Exception $e)
			{
				throwAlertError (ucfirst ($this->data['authType']) . ' error: ' . $e->getMessage());
			}
		}
		*/
	}

	public function setNickname ($nickname)
	{
		if (true)
		{
			$db = Neuron_Core_Database::__getInstance ();
			$this->loadData ();
			
			if (!$this->isNicknameSet ())
			{
				if (Neuron_Core_Tools::checkInput ($nickname, 'username'))
				{
					$data = $db->select
					(
						'players',
						array ('plid'),
						"nickname = '{$nickname}' AND isRemoved = 0"
					);

					if (count ($data) == 0)
					{

						// Everything seems to be okay. Let's go.
						$db->update
						(
							'players',
							array
							(
								'nickname' => $nickname
							),
							"plid = '".$this->getId ()."'"
						);

						$this->data['nickname'] = $nickname;
						return true;
					}
					else
					{
						$this->error = 'user_found';
						return false;
					}
				}
				else
				{
					$this->error = 'error_username';
					return false;
				}
			}
			else
			{
				$this->error = 'nickname_already_set';
				return false;
			}
		}
		else
		{
			$this->error = 'game_not_open';
			return false;
		}
	}

	public function changeNickname ($nickname)
	{
		$db = Neuron_Core_Database::__getInstance ();
	
		$this->loadData ();
		if (!empty ($this->data['nickname']))
		{
			if (Neuron_Core_Tools::checkInput ($nickname, 'username'))
			{
				$data = $db->select
				(
					'players',
					array ('plid'),
					"nickname = '{$nickname}'"
				);

				if (count ($data) == 0)
				{

					// Everything seems to be okay. Let's go.
					$chk = $db->update
					(
						'players',
						array
						(
							'nickname' => $nickname
						),
						"plid = '".$this->getId ()."' "
					);

					if ($chk == 1)
					{
						$this->data['nickname'] = $nickname;
						return true;
					}
					else
					{
						$this->error = 'update_failed';
						return false;
					}
				}
				else
				{
					$this->error = 'user_found';
					return false;
				}
			}
			else
			{
				$this->error = 'error_username';
				return false;
			}
		}
		else
		{
			$this->error = 'nickname_not_set';
		}
	}

	public function getError ()
	{
		return $this->error;
	}
	
	/*
		This function starts the RESET ACCOUNT procedure.
		
		This function sends a mail to the player and allows
		the user to reset his account using a link provided
		in the mail.
	*/
	public function startResetAccount ()
	{
		if ($this->isFound ())
		{
			$db = Neuron_Core_Database::__getInstance ();
			
			$key = md5 (mt_rand (0, 1000000));
			
			$db->update
			(
				'players',
				array
				(
					'tmp_key' => $key,
					'tmp_key_end' => Neuron_Core_Tools::timestampToMysqlDatetime (time () + 60*60*24)
				),
				"plid = ".$this->getId ()
			);
			
			// Send the mail
			$text = Neuron_Core_Text::__getInstance ();
			customMail 
			(
				$this->getEmail (),
				$text->get ('msubject', 'resetaccount', 'account'),
				$text->getTemplate 
				(
					'email_reset', 
					array 
					(
						Neuron_Core_Tools::output_varchar ($this->getNickname ()),
						API_FULL_URL.'reset?id='.$this->getId ().'&certkey='.$key
					)
				)
			);
		}
	}
	
	/*
		This function resets the player acocunt.
		
		This includes:
		- Disable all villages this player owns 
	*/
	public function resetAccount ($key)
	{
		$this->loadData ();
		
		// Check key
		if (strtotime ($this->data['tmp_key_end']) > time () && $this->data['tmp_key'] == $key)
		{
			return $this->doResetAccount ();
		}
	
		return false;
	}
	
	/*
		This function executes and acount removal.
	*/
	public function execResetAccount ()
	{
		return $this->doResetAccount ();
	}
	
	/*
		This function is called when a valid reset call is triggered.
	*/
	public function doResetAccount ()
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$this->doEndVacationMode ();
		
		// Make non playing
		$db->update
		(
			'players',
			array
			(
				'isPlaying' => 0,
				'tmp_key' => NULL,
				'tmp_key_end' => NULL
			),
			"plid = ".$this->getId ()
		);
		
		return true;
	}
	
	public function isOnline ()
	{
		$this->loadData ();
		
		$db = Neuron_DB_Database::getInstance ();
		
		return $db->toUnixtime ($this->data['lastRefresh']) > (time () - ONLINE_TIMEOUT);
	}
	
	public function getLogArray ()
	{
		return $this->getAPIData (true);
	}

	public function getAPIData ($showExtendedInfo = true)
	{
		$this->loadData ();

		$data = array
		(
			'id' =>		$this->data['plid'],
			'name' => 	$this->getNickname (),
			'refresh' =>	$this->data['lastRefresh']
		);
		
		if ($showExtendedInfo)
		{
			$villages = $this->getVillages ();

			$vils = array ();
			foreach ($villages as $v)
			{
				$vils[] = array
				(
					'name' => 	$v->getName (),
					'id' => 	$v->getId ()
				);
			}
			
			$data['villages'] = $vils;
		}

		return $data;
	}

	public function getCreationDate ()
	{
		$this->loadData ();
		return Neuron_Core_Tools::datetimeToTimestamp ($this->data['creationDate']);
	}

	public function getLastRefresh ()
	{
		$this->loadData ();
		return Neuron_Core_Tools::datetimeToTimestamp ($this->data['lastRefresh']);
	}

	public function getRemovalDate ()
	{
		$this->loadData ();
		return Neuron_Core_Tools::datetimeToTimestamp ($this->data['removalDate']);
	}
	
	/*
		Return the referee count
	*/
	public function getReferal ()
	{
		$this->loadData ();
		return isset ($this->data['referee']) ? $this->data['referee'] : null;
	}
	
	/*
		Credits
	*/
	private function loadCredits ()
	{
		if (!isset ($this->objCredits))
		{
			$this->objCredits = new Neuron_GameServer_Credits ($this);
		}
	}

	public function isValidData ()
	{
		if ($this->objCredits->isValidData ())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getCredits ()
	{
		$this->loadCredits ();

		$credits = $this->objCredits->getCredits ();
		
		if ($credits === false)
		{
			$this->error = $this->objCredits->getError ();
			return false;
		}
		return $credits;
	}
	
	public function refundCredits ($amount, $description, $action = null)
	{
		$this->loadCredits ();
		return $this->objCredits->refundCredits ($amount, $description, $action);
	}
	
	public function useCredit ($amount, $data)
	{
		$this->extendPremiumAccount (60*60*24*15);
	}
	
	public function getCreditUseUrl ($amount = 100, $data = array (), $description = 'Premium membership')
	{
		$this->loadCredits ();
		return $this->objCredits->getUseUrl ($amount, $data, $description);
	}

	public function getCreditBuyUrl ()
	{
		$this->loadCredits ();
		return $this->objCredits->getBuyUrl ();
	}
	
	/*
		Return the amount of (foreign) credits
		this user will have to pay.
	*/
	public function convertCredits ($amount = 100)
	{
		$this->loadCredits ();
		return $this->objCredits->convertCredits ($amount);
	}

	public function getCreditDisplay ($amount = 100)
	{
			$this->loadCredits ();
		return $this->objCredits->getCreditDisplay ($amount);
	}
	
	public function getTrackerUrl ($tracker)
	{
		$this->loadCredits ();
		return $this->objCredits->getTrackerUrl ($tracker);	
	}

	public function handleUseRequest ($data, $id, $key)
	{
		$this->loadCredits ();
		$result = $this->objCredits->handleUseRequest ($data, $id, $key);
		$this->error = $this->objCredits->getError ();
		
		return $result;
	}
	
	/*
		Vacation mode
	*/
	public function startVacationMode ()
	{
		if (!ALLOW_VACATION_MODE)
		{
			$this->error = 'vacation_disabled';
			return false;
		}
	
		$db = Neuron_DB_Database::__getInstance ();
		
		$db->query
		("
			UPDATE
				players
			SET
				startVacation = NOW()
			WHERE
				plid = {$this->getId()}
		");
		
		return true;
	}
	
	/*
		And vacation mode, if possible...
	*/
	public function endVacationMode ()
	{
		$db = Neuron_DB_Database::getInstance ();
	
		$this->loadData ();
	
		$unixtime = $db->toUnixtime ($this->data['startVacation']);
		
		if ($unixtime > 0 && $unixtime + 60*60*24*7 > time ())
		{
			$this->error = 'too_early';
			return false;
		}
		
		$this->doEndVacationMode ();
		
		return true;
	}
	
	private function doEndVacationMode ()
	{
		$db = Neuron_DB_Database::__getInstance ();
	
		// Remove the vacation mode
		$db->query
		("
			UPDATE
				players
			SET
				startVacation = NULL
			WHERE
				plid = {$this->getId()}
		");
		
		// Reset all resource times
		$db->query
		("
			UPDATE
				villages
			SET
				lastResRefresh = '".time()."'
			WHERE
				plid = {$this->getId()} AND
				isActive = '1'
		");
	}
	
	/*
		Check if this player is a vacation mode
	*/
	public function inVacationMode ()
	{
		return ALLOW_VACATION_MODE && $this->getVacationStart () != null;
	}
	
	public function getVacationStart ()
	{
		$this->loadData ();
		if ($this->data['startVacation'] == null)
		{
			return null;
		}
		else
		{
			$db = Neuron_DB_Database::__getInstance ();
			return $db->toUnixtime ($this->data['startVacation']);
		}
	}
	
	public function getRank ()
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$rows = $db->getDataFromQuery ($db->customQuery
		("
			SELECT 
				COUNT(*) AS rank
			FROM
				players a 
			INNER JOIN
				players b ON (a.p_score < b.p_score OR (a.p_score = b.p_score AND a.plid > b.plid)) AND b.isPlaying = 1
			WHERE
				a.plid = '".$this->getId ()."'
			GROUP BY a.plid
		"));
		
		$total = $db->select
		(
			'players',
			array ('count(plid) AS total')
		);

		if (count ($rows) > 0)
		{
			$rank = $rows[0]['rank'];
			$total = $total[0]['total'];
		}
		else
		{
			$rank = 0;
			$total = count ($total) > 0 ? $total[0]['total'] : 1;
		}
		
		$rank = $rank + 1;
		
		//echo $total;
		
		return array ($rank, $total);
	}
	
	/*
		Calculate the score for this player
	*/
	public function getScore ()
	{
		$this->loadData ();
		$score = $this->data['p_score'];
		
		if ($score == 0 && $this->isPlaying ())
		{
			$this->updateScore ();
		}
		
		return $score;
	}
	
	/*
		Set the score
	*/
	public function setScore ($score)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$score = intval ($score);
		
		$db->query
		("
			UPDATE
				players
			SET
				p_score = {$score}
			WHERE
				plid = {$this->getId ()}
		");
		
		$this->loadData ();
		$this->data['p_score'] = $score;
	}
	
	public function getBrowserBasedGamesData ($data = null)
	{
		$openids = $this->getOpenIDs ();
		
		$openids_out = array ();
		foreach ($openids as $v)
		{
			$openids_out[] = array
			(
				'attributes' => array ('hash' => 'md5'),
				'element-content' => md5 ($v)
			);
		}
		
		$lkey = "";
		
		$this->loadData ();
		
		$isFound = $this->isFound;
		
		return array
		(
			'member_id' => $this->getId (),
			'member_url' => ABSOLUTE_URL.$lkey,
			'name' => $this->getName (),
			'score' => $this->getScore (),
			'openids' => $openids_out,
			'join_date' => $isFound ? date (API_DATE_FORMAT, strtotime ($this->data['creationDate'])) : null
		);
	}
	
	public function getOpenIDs ()
	{
		$db = Neuron_DB_Database::__getInstance ();
		
		$openids = $db->query
		("
			SELECT
				*
			FROM
				auth_openid
			WHERE
				user_id = {$this->getId()}
		");
		
		$out = array ();
		
		foreach ($openids as $v)
		{
			$out[] = $v['openid_url'];
		}
		
		return $out;
	}
	
	/*
		Preferences
	*/
	private function loadPreferences ()
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$data = $db->query
		("
			SELECT
				*
			FROM
				players_preferences
			WHERE
				p_plid = {$this->getId()}
		");
		
		foreach ($data as $v)
		{
			$this->sPreferences[$v['p_key']] = $v['p_value'];
		}
	}
	
	public function setPreference ($sKey, $sValue)
	{
		$db = Neuron_DB_Database::getInstance ();
	
		// Check if exist
		$check = $db->query
		("
			SELECT
				*
			FROM
				players_preferences
			WHERE
				p_key = '{$db->escape($sKey)}'
				AND p_plid = {$this->getId()}
		");
		
		if (count ($check) == 0)
		{
			$db->query
			("
				INSERT INTO
					players_preferences
				SET
					p_key = '{$db->escape($sKey)}',
					p_value = '{$db->escape($sValue)}',
					p_plid = {$this->getId ()}
			");
		}
		else
		{
			$db->query
			("
				UPDATE
					players_preferences
				SET
					p_value = '{$db->escape($sValue)}'
				WHERE
					p_key = '{$db->escape($sKey)}' AND
					p_plid = {$this->getId ()}
			");
		}
	}
	
	public function getPreference ($sKey, $default = false)
	{
		$this->loadPreferences ();
		if (isset ($this->sPreferences[$sKey]))
		{
			return $this->sPreferences[$sKey];
		}
		else
		{
			return $default;
		}
	}
	
	/*
		Return the game logs for this player...
		to be overloaded.
	*/
	public function getLogs ($iStart, $iEnd)
	{
		return array ();
	}
	
	/*
		Give a bonus for refering a friend.
	*/
	public function giveReferralBonus ($objUser) {}
	
	/*
		Social status changer.
		This function enables you to set a special status
		for a player. This special status must be INT.
		
		Reserved int's are: 1 (isFriend), -1 (ignoring), 0 (neutral)
	*/
	protected function setSocialStatus ($objUser, $status)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		if ($objUser instanceof Neuron_GameServer_Player)
		{
			$objUser = $objUser->getId ();
		}
		
		$objUser = intval ($objUser);
		
		$status = intval ($status);
		
		// Check if already in here.
		$chk = $db->query
		("
			SELECT
				ps_status
			FROM
				players_social
			WHERE
				ps_plid = {$this->getId()} 
				AND ps_targetid = {$objUser}
		");
		
		if (count ($chk) == 0)
		{
			$db->query
			("
				INSERT INTO
					players_social
				SET
					ps_plid = {$this->getId()},
					ps_targetid = {$objUser},
					ps_status = '{$status}'
			");
		}
		else
		{
			$db->query
			("
				UPDATE
					players_social
				SET
					ps_status = '{$status}'
				WHERE
					ps_targetid = {$objUser} AND
					ps_plid = {$this->getId()}
			");
		}
	}
	
	private function loadSocialStatuses ()
	{
		if (!isset ($this->iSocialStatuses))
		{
			$db = Neuron_DB_Database::getInstance ();
			$data = $db->query
			("
				SELECT
					ps_targetid,
					ps_status
				FROM
					players_social
				WHERE
					ps_plid = {$this->getId()}
			");
			
			$this->iSocialStatuses = array ();
			foreach ($data as $v)
			{
				$this->iSocialStatuses[$v['ps_targetid']] = $v['ps_status'];
			}
		}
	}
	
	protected function getSocialStatus ($objUser)
	{
		if ($objUser instanceof Neuron_GameServer_Player)
		{
			$objUser = $objUser->getId ();
		}
		
		$objUser = intval ($objUser);
	
		$this->loadSocialStatuses ();
		
		if (isset ($this->iSocialStatuses[$objUser]))
		{
			return $this->iSocialStatuses[$objUser];
		}
		else
		{
			return false;
		}
	}
	
	public function isIgnoring ($objUser)
	{
		return $this->getSocialStatus ($objUser) == -1;
	}
	
	public function setIgnoring ($objUser, $ignore = true)
	{
		$this->setSocialStatus ($objUser, $ignore == true ? -1 : 0);
	}
	
	private function getSocialStatuses ($iStatus)
	{
		$this->loadSocialStatuses ();
		
		$out = array ();
		foreach ($this->iSocialStatuses as $k => $v)
		{
			if ($v == $iStatus)
			{
				$out[] = Neuron_GameServer::getPlayer ($k);
			}
		}
		
		return $out;
	}
	
	public function getIgnoredPlayers ()
	{
		return $this->getSocialStatuses (-1);
	}
	
	private function loadBans ()
	{
		if (!isset ($this->bans))
		{
			$this->bans = array ();
			
			$db = Neuron_DB_Database::getInstance ();
			
			$chk = $db->query
			("
				SELECT
					bp_channel,
					UNIX_TIMESTAMP(bp_end) AS datum
				FROM
					players_banned
				WHERE
					plid = {$this->getId ()}
			");
			
			foreach ($chk as $v)
			{
				$this->bans[$v['bp_channel']] = $v['datum'];
				
				if ($v['datum'] < time ())
				{
					$this->unban ($v['bp_channel']);
				}
			}
		}
	}
	
	public function isBanned ($sChannel = 'chat')
	{
		$this->loadBans ();
		return isset ($this->bans[$sChannel]) ? true : false;
	}
	
	public function getBanDuration ($sChannel)
	{
		$this->loadBans ();
		return isset ($this->bans[$sChannel]) ? $this->bans[$sChannel] : false;
	}
	
	public function ban ($sChannel = 'chat', $duration = 3600, $ban = true)
	{
		$db = Neuron_DB_Database::getInstance ();

		$db->query
		("
			DELETE FROM
				players_banned
			WHERE
				plid = {$this->getId()} AND
				bp_channel = '{$db->escape ($sChannel)}'
		");
		
		$this->bans = null;
		
		// First unban
		if ($ban)
		{
			$db->query
			("
				INSERT INTO
					players_banned
				SET
					plid = {$this->getId ()},
					bp_channel = '{$db->escape ($sChannel)}',
					bp_end = FROM_UNIXTIME(".(time() + $duration).")
			");
		}
	}
	
	public function unban ($sChannel = 'chat')
	{
		$this->ban ($sChannel, null, false);
	}
	
	public function equals ($objPlayer)
	{
		return $objPlayer->getId () == $this->getId ();
	}

	/*
	* Called when someone sent you a gift.
	*/
	public function invitationGiftReceiver ($data, Neuron_GameServer_Player $from)
	{

	}

	/*
	* Called when someone accepts your gift.
	*/
	public function invitationGiftSender ($data, Neuron_GameServer_Player $to)
	{

	}

	public function countLogins ()
	{
		$db = Neuron_DB_Database::getInstance ();

		$data = $db->query ("SELECT COUNT(*) AS aantal FROM login_log WHERE l_plid = {$this->getId ()}");
		return $data[0]['aantal'];
	}
	
	public function __toString ()
	{
		return $this->getDisplayName ();
	}
	
	/*
		Destruct this object and all villages within it.
		This is a fairly dangerous function, it is possible
		that, using this method, villages will be destroyed
		that still have references.
	*/
	public function __destruct ()
	{
		//echo 'player destructed.' . "\n";
	
		//unset ( $this->data );
		unset ( $this->gameTriggerObj );
		unset ( $this->error );
		unset ( $this->village_insert_id );
		unset ( $this->id );
		unset ( $this->gameData );
		unset ( $this->isFound );
		unset ( $this->isPlaying );
		unset ( $this->objCredits );
		unset ( $this->sPreferences );
		unset ( $this->iSocialStatuses );
		unset ( $this->bans );
	}
}
?>
