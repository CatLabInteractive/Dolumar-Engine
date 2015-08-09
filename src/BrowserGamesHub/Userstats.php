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

class BrowserGamesHub_Userstats
{
	private $sXML;

	public function __construct ()
	{
		$this->sXML = array ();
	}
	
	public function setNickname ($nickname)
	{
		$this->sXML['nickname'] = $nickname;
	}
	
	public function setData ($data)
	{
		$this->sXML = $data;
	}
	
	/*
		And the most important function of them all:
		send this notification to the users notifaction URL.
	*/
	public function send ($sUrl)
	{
		$xml = Neuron_Core_Tools::output_xml 
		(
			$this->sXML, 
			1, 
			'userstats', 
			array 
			(
			)
		);
		
		// And now: send the notification!
		$postfields = array
		(
			'userstats_xml' => $xml
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
	}
}
?>
