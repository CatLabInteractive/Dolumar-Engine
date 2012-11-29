<?php
/*
	This class constructs a notification and sends it to the server.
*/
class BBGS_Statistics
{
	private $sMessage;
	private $iTimestamp;
	private $sId;
	private $sGroup;
	
	private $sSkeletons;
	private $sValues;
	
	private $sXML;
	
	private $sVisibility;
	private $sLanguage;
	
	private $privatekey;
	
	private $attachments = array ();

	/*
		Construct a notification with it's 2 required
		parameters. More information can (and should) be
		set with the setters below.
	*/
	public function __construct ($statistics, $information)
	{
		$this->sXML = array ();
		$this->sXML['statistics'] = $statistics;
		$this->sXML['information'] = $information;
	}

	public function setPrivateKey ($key)
	{
		$this->privatekey = $key;
	}
	
	private function getPrivateKey ()
	{
		if (!isset ($this->privatekey))
		{
			throw new Exception ('Private key not set.');
		}
	
		return $this->privatekey;
	}
	
	private function getSignature ($data)
	{
		$privatekey = $this->getPrivateKey ();
		
		openssl_sign 
		(
			$data, 
			$binary_signature,
			$privatekey,
			OPENSSL_ALGO_SHA1
		);
		
		return $binary_signature;
	}
	
	/*
		And the most important function of them all:
		send this notification to the users notifaction URL.
	*/
	public function send ($sUrl)
	{
		$key = $this->getPrivateKey ();
	
		$xml = self::output_xml 
		(
			$this->sXML, 
			1, 
			'notification', 
			array 
			(
				'visibility' => $this->sVisibility, 
				'lang' => $this->sLanguage
			)
		);
		
		// And now: send the notification!
		$postfields = array
		(
			'date' => date ('Y-m-d\TH:i:s', $this->iTimestamp),
			'xml' => $xml,
			'signature' => base64_encode ($this->getSignature ($xml)),
			'type' => 'statistics'
		);
		
		// Make sure curl doesn't think that we're trying to upload files
		foreach ($postfields as $k => $v)
		{
			if (substr ($v, 0, 1) == '@')
			{
				// Let's hope the receive will trim the response.
				// It's only used for the bare message anyway.
				$postfields[$k] = ' '.$v;
			}
		}
		
		if (defined ('NOTIFICATION_DEBUG'))
		{
			$postfields['debug'] = true;
			
			echo '<h2>Preparing data to send:</h2><pre>';
			echo htmlentities (print_r ($postfields, true));
			echo '</pre>';
			
			echo '<h2>Sending data</h2>';
			echo '<p>Contacting <span style="color: red;">'.$sUrl.'</span>... ';
			
			echo '<form method="post" action="' . $sUrl . '">';
			foreach ($postfields as $k => $v)
			{
				echo '<textarea col="0" row="0" type="hidden" name="'.$k.'" style="visibility: hidden; width: 0px; height: 0px;">'.htmlentities ($v).'</textarea>';
			}
			echo '<button type="submit">Test call</button>';
			echo '</form>';
		}
		
		try
		{
			if (function_exists ('curl_init'))
			{
				$ch = curl_init ();
		
				curl_setopt($ch, CURLOPT_URL, $sUrl);
				//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
				//curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
				curl_setopt($ch, CURLOPT_POST, true); // set POST method
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields); // add POST fields
		
				// execute curl command
				$result = curl_exec($ch); // run the whole process
			}
		}
		catch (Exception $e)
		{
			// Do nothing. The notification was not sent.
		}
		
		// Show if debug is on.
		if (defined ('NOTIFICATION_DEBUG'))
		{
			if (!$result)
			{
				echo '<p>cURL error: '.curl_error ($ch).'</p>';
			}

			echo 'Done!</p>';
			echo '<h2>Beginning notification output:</h2>';
			echo '<pre>'.htmlentities($result).'</pre>';
			echo '<h2>End of notification output.</h2>';
		}

		$result = json_decode ($result, true);

		return array
		(
			'success' => $result['success'] ? true : false,
			'iframe' => isset ($result['iframe']) ? $result['iframe'] : null
		);
	}
	
	/*
		XML BUILDER
	*/
	private static function output_xml ($data, $version = '0.1', $root = 'root', $parameters = array (), $sItemName = 'item')
	{	
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement($root);
		
		if (!empty ($version))
		{
			$xml->writeAttribute ('version', $version);
		}
		
		foreach ($parameters as $paramk => $paramv)
		{
			$xml->writeAttribute ($paramk, $paramv);
		}

		self::writexml ($xml, $data, $sItemName);

		$xml->endElement();
		return $xml->outputMemory(true);
	}
	
	private static function output_partly_xml ($data, $key =  null)
	{
		$output = '<'.$key;
		
		if (isset ($data['attributes']) && is_array ($data['attributes']))
		{
			foreach ($data['attributes'] as $k => $v)
			{
				$output .= ' '.$k.'="'.$v.'"';
			}
		
			unset ($data['attributes']);
		}
		
		$output .= '>';
		if (!is_array ($data))
		{
			$output .= $data;
		}
		
		elseif (count ($data) == 1 && isset ($data['element-content']))
		{
			$output .= $data['element-content'];
		}
		
		else
		{
			foreach ($data as $k => $v)
			{
				if (is_numeric ($k))
				{
					$k = substr ($key, 0, -1);
				}
				
				$output .= self::output_partly_xml ($v, $k);
			}
		}
		$output .= '</'.$key.'>'."\n";
		
		return $output;
	}
	
	private static function writexml (XMLWriter $xml, $data, $item_name = 'item')
	{
		foreach($data as $key => $value)
		{
			if (is_int ($key))
			{
				$key = $item_name;
			}

			if (is_array($value))
			{
				if ($key != 'items')
				{
					$xml->startElement($key);
				}
				
				if (isset ($value['attributes']) && is_array ($value['attributes']))
				{
					foreach ($value['attributes'] as $k => $v)
					{
						$xml->writeAttribute ($k, $v);
					}
					
					unset ($value['attributes']);
				}
				
				self::writexml ($xml, $value, substr ($key, 0, -1));
				
				if ($key != 'items')
				{
					$xml->endElement();
				}
			}
			
			elseif ($key == 'element-content')
			{
				$xml->text ($value);
			}
	
			else
			{
				$xml->writeElement($key, $value);
			}
		}
	}
}
?>
