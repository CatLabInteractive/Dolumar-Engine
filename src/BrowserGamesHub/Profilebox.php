<?php
class BrowserGamesHub_Profilebox
{
	private $content;

	public function __construct ()
	{
	}
	
	public function setContent ($content)
	{
		$this->content = $content;
	}
	
	/*
		And the most important function of them all:
		send this notification to the users notifaction URL.
	*/
	public function send ($sUrl)
	{		
		// And now: send the notification!
		$postfields = array
		(
			'profilebox_xhtml' => $this->content
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
		}
		
		try
		{
			if (function_exists ('curl_init'))
			{
				$ch = curl_init ();
		
				curl_setopt($ch, CURLOPT_URL, $sUrl);
				//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
				curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
				curl_setopt($ch, CURLOPT_POST, true); // set POST method
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields); // add POST fields
		
				// execute curl command
				$result = curl_exec($ch); // run the whole process
			}
		}
		catch (Exception $e)
		{
			// Do nothing; Profilebox was not sent.
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
	}
}
?>
