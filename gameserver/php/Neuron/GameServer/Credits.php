<?php
/*
	This class manages the connection to the premium
	credits server.
*/
class Neuron_GameServer_Credits
{
	private $objUser;
	private $sToken;
	
	private $error;
	
	private $objCredits;
	
	private $convertcache = array ();
	
	public function __construct (Neuron_GameServer_Player $objUser)
	{
		$this->objUser = $objUser;
		
		$this->objCredits = new BBGS_Credits (PREMIUM_GAME_TOKEN);
		
		// Load token
		$this->objCredits->setPrivateKey (
			file_get_contents (BASE_PATH . 'gameserver/php/Neuron/GameServer/certificates/credits_private.cert'));
		
		if ($this->objUser->isEmailCertified ())
		{
			$this->objCredits->setEmail ($this->getEmail ());
		}
		
		$this->objCredits->setReferal ($objUser->getReferal ());
		
		/*
		$openid = isset ($_SESSION['neuron_openid_identity']) ? 
			$_SESSION['neuron_openid_identity'] : null;
		
		if (isset ($openid))
		{
			$this->objCredits->setOpenID ($openid);
		}
		*/
		
		foreach ($objUser->getOpenIDs () as $v)
		{
			$this->objCredits->addOpenID ($v);
		}
			
		$container = isset ($_SESSION['opensocial_container']) ?
			$_SESSION['opensocial_container'] : null;
		
		if (isset ($container))
		{
			$this->objCredits->setContainer ($container);
		}
		
		$fullscreen = isset ($_SESSION['fullscreen']) && $_SESSION['fullscreen'] ? 1 : 0;
		$this->objCredits->setFullscreen ($fullscreen);
		
		$this->objCredits->setLanguage (Neuron_Core_Text::getInstance ()->getCurrentLanguage ());
		
		$this->objCredits->setUserId ($objUser->getId ());
		
		if (isset ($_SESSION['birthday']))
		{
			$this->objCredits->setBirthday ($_SESSION['birthday']);
		}
		
		if (isset ($_SESSION['gender']))
		{
			$this->objCredits->setGender (strtolower ($_SESSION['gender']) == 'm' ? 'male' : 'female');
		}
	}
	
	private function getEmail ()
	{
		return trim (strtolower ($this->objUser->getEmail ()));
	}

	public function getCredits ()
	{
		if (!$this->objCredits->isValidData (false))
		{
			return 0;
		}

		try
		{
			return $this->objCredits->getCredits ();
		}
		catch (BadMethodCallException $e)
		{
			$this->error = 'email_not_set'; 
			return false; 
		}
	}
	
	public function useCredit ()
	{
		return true;
	}
	
	public function getBuyUrl ()
	{
		//return PREMIUM_URL . '?email='.$this->getEmail().'&token='.$this->sToken;
		return $this->objCredits->buyCredits ();
	}
	
	public function refundCredits ($amount, $description, $action)
	{
		return $this->objCredits->refundCredits ($amount, $description, $action);
	}
	
	/*
		This function connects to the credit gateway
		and checks if a certain transaction exists.
		
		If the transaction exists, it handles it
		and returns TRUE.
	*/
	public function handleUseRequest ($data, $transactionId, $transactionKey)
	{
		/*
		$transaction = PREMIUM_URL . '?action=transaction'.
			'&transaction_id='.urlencode ($transactionId).
			'&transaction_key='.urlencode ($transactionKey).
			'&output=serialize';
		
		$credits = file_get_contents ($transaction);
		$credits = @unserialize ($credits);
		
		$content = $credits['content'];
	
		$status = isset ($content['status']) ? $content['status'] : null;
		$transaction = isset ($content['transaction']) ? $content['transaction'] : null;
		
		if ($status == 1 && is_array ($transaction))
		{
			$amount = isset ($transaction['amount']) ? $transaction['amount'] : null;
			$this->objUser->useCredit ($amount, $data);
			
			return true;
		}
		
		else
		{
			$this->error = 'Transaction not found.';
			return false;
		}
		*/
		
		if (isset ($_POST['transaction_id']) && isset ($_POST['transaction_secret']))
		{
			$valid = $this->objCredits->isRequestValid ($_POST['transaction_id'], $_POST['transaction_secret']);
	
			if ($valid)
			{
				$amount = $_POST['transaction_amount'];
				$this->objUser->useCredit ($amount, $data);
				
				return true;
			}
			else
			{
				$this->error = 'This request was not valid or already executed. Ignore.';
			}
		}
		else
		{
			$this->error = 'No post data received.';
		}
		
		return false;
	}
	
	public function getUseUrl ($amount = 1, $data = array (), $description = 'Premium features', $action = 'premium')
	{
		$callback = API_FULL_URL.'spendCredit/'.
			'?key='.md5($this->getEmail()).
			'&id='.$this->objUser->getId();
		
		foreach ($data as $k => $v)
		{
			$callback .= '&' . urlencode ($k) . '=' . urlencode ($v);
		}

		try
		{
			return $this->objCredits->requestCredits ($amount, $description, $callback, $action);
		}
		catch (BadMethodCallException $e)
		{
			$this->error = 'email_not_set'; 
			return false; 
		}
	}
	
	private function getConvertData ($amount = 1)
	{
		if (!isset ($this->convertcache[$amount]))
		{
			$this->convertcache[$amount] = $this->objCredits->convert ($amount);
		}
		return $this->convertcache[$amount];
	}
	
	public function convertCredits ($amount = 1)
	{
		/*
		$parameters = array
		(
			'action' => 'convert',
			'email' => $this->getEmail (),
			'amount' => $amount,
			'callback' => 
			(
				API_FULL_URL.'convert/'.
				'?key='.md5($this->getEmail()).
				'&id='.$this->objUser->getId()
			)
		);
		
		$data = file_get_contents ($this->getSignedUrl (PREMIUM_URL, $parameters));
		
		return json_decode ($data);
		*/
		
		//return $amount;
		
		$data = $this->getConvertData ($amount);
		return $data['amount'];
	}
	
	public function getCreditDisplay ($amount, $html = false)
	{
		$data = $this->getConvertData ($amount);
		return $html ? $data['html'] : $data['text'];	
	}
	
	/*
		@param $sTracker ID of the tracker, for example: "registration"
	*/
	public function getTrackerUrl ($sTracker)
	{
		/*
		$parameters = array
		(
			'tracker' => $sTracker
		);
		
		return $this->getSignedUrl (TRACKER_URL, $parameters);
		*/
		
		return $this->objCredits->getTrackerUrl ($sTracker);
	}
	
	public function getError ()
	{
		return $this->error;
	}
}
?>
