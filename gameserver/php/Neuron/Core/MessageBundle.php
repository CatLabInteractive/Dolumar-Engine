<?php
/*
	This class handles the convertion between
	my INI based translations and XML based translations.
*/
class Neuron_Core_MessageBundle
{
	public static function text2bundle ($text)
	{
		$path = $text->getPath ();
		
		$output = array ();
		self::handleDirectory ($output, $path);
		
		$data = array ();
		foreach ($output as $k => $v)
		{
			$data[] = array
			(
				'attributes' => array ('name' => $k),
				'element-content' => $v
			);
		}
		
		return Neuron_Core_Tools::output_xml 
		(
			$data, 
			null, 
			'messagebundle', 
			array (),
			'msg'
		);
	}
	
	public static function bundle2text ($xml, $sName = 'default')
	{
		$data = self::getTextContent ($xml);
		
		$cache = Neuron_Core_Cache::getInstance ('language/'.$sName.'/');
		
		foreach ($data['messages'] as $k => $v)
		{
			$cache->setCache ($k, serialize ($v));
		}
		
		foreach ($data['files'] as $k => $v)
		{
			$cache->setCache ($k, $v);
		}
	}
	
	private static function getTextContent ($xml)
	{
		if (!$xml instanceof DOMDocument)
		{
			$sxml = $xml;
			$xml = new DOMDocument ();
			
			// Loading failed, return empty content.
			if (!@$xml->load ($sxml))
			{
				return array
				(
					'messages' => array (),
					'files' => array ()
				);
			}
		}
		
		// loop trough all messages
		$bundle = $xml->getElementsByTagName ('messagebundle');
		if ($bundle->length == 1)
		{
			// Fetch all messages
			$bundle = $bundle->item (0);
			
			$fields = array ();
			$files = array ();
			
			$messages = $bundle->getElementsByTagName ('msg');
			foreach ($messages as $v)
			{
				//echo $v->nodeValue."<br />";
				$key = $v->getAttribute ('name');
				
				// Check if this is a template
				if (substr ($key, -3) == 'txt')
				{
					$files[str_replace ($key, "/", '|')] = $v->nodeValue;
				}
				else
				{
					$data = explode ('|', $key);
					
					if (!isset ($fields[$data[0]]))
					{
						$fields[$data[0]] = array ();
					}
					
					if (!isset ($fields[$data[0]][$data[1]]))
					{
						$fields[$data[0]][$data[1]] = array ();
					}
					
					$fields[$data[0]][$data[1]][$data[2]] = $v->nodeValue;
				}
			}
		}
		
		return array
		(
			'messages' => $fields,
			'files' => $files
		);
	}
	
	private static function handleDirectory (&$output, $sDirectory, $base = null)
	{
		if (!$base)
		{
			$base = $sDirectory;
		}
	
		$paths = scandir ($sDirectory);
		foreach ($paths as $path)
		{
			if (substr ($path, 0, 1) != '.')
			{
				$path = $sDirectory.$path;
				$filename = str_replace ($base, '', $path);
				$filename = substr ($filename, 0, -4);
			
				if (is_dir ($path))
				{
					self::handleDirectory ($output, $path.'/', $base);
				}
				elseif (!self::isValidFile ($path))
				{
					// do nothing.
				}
				elseif (self::isIniFile ($path))
				{
					// Handle ini file
					$content = parse_ini_file ($path, true);
					if ($content)
					{
						foreach ($content as $k => $v)
						{
							foreach ($v as $kk => $vv)
							{
								$output[$filename.'|'.$k.'|'.$kk] = self::convertVariableToken ($vv);
								//$output[$vv] = $vv;
							}
						}
					}
				}
				elseif (self::isTextFile ($path))
				{
					// Handle regular text file
					$output[$filename.'.txt'] = self::convertVariableToken (file_get_contents ($path));
				}
			}
		}
	}
	
	private static function convertVariableToken ($sValue)
	{
		return preg_replace 
		(
			'/@@([a-zA-Z0-9]*)/',
			'${\\1}',
			$sValue
		);
	}
	
	private static function isIniFile ($sFile)
	{
		return substr ($sFile, -3) == 'lng';
	}
	
	private static function isTextFile ($sFile)
	{
		return substr ($sFile, -3) == 'txt';
	}
	
	private static function isValidFile ($sFile)
	{	
		return substr ($sFile, 0, 1) != '.'
			&& substr ($sFile, -1) != '~';
	}
}
?>
