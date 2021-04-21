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

class Neuron_Core_Wiki
{
	const USE_CACHE = true;

	public static function parseHelpFile ($file)
	{
		if (
			!defined('WIKI_GUIDE_URL') ||
			!WIKI_GUIDE_URL
		) {
			return false;
		}

		return WIKI_GUIDE_URL;

		$objCache = Neuron_Core_Cache::__getInstance ('wiki/');
		$sKey = str_replace ('/', '|', $file);
		
		$cache = null;
		
		if (self::USE_CACHE)
		{
			$cache = $objCache->getCache ($sKey);
			//$cache = false;
		
			if ($cache)
			{
				return $cache;
			}
		}
		
		$t = self::parseNewFile ($file);

		if ($t)
		{
			if (self::USE_CACHE)
			{
				$objCache->setCache ($sKey, $t);
			}
			
			return $t;
		}

		else
		{
			return false;
		}
	}

	private static function parseNewFile ($file)
	{
		$text = Neuron_Core_Text::__getInstance ();
		
		$params1 = 'api.php?action=query&prop=revisions&rvprop=timestamp|user|comment|content&format=php&titles=';
		$params2 = 'api.php?action=query&prop=imageinfo&iiprop=url&format=php&titles=';
		
		$url = WIKI_GUIDE_URL.$params1.urlencode ($file);

		// Load file from wiki
		$wiki = file_get_contents ($url);
		$wiki = unserialize ($wiki);
		
		// Get the real content
		$content = Neuron_Core_Tools::getArrayFirstValue ($wiki['query']['pages']);

		if (!isset ($content[1]['revisions']))
		{
			return false;
		}

		else
		{
			$content = Neuron_Core_Tools::getArrayFirstValue ($content[1]['revisions']);
			$content = $content[1]['*'];
			
			// Search for thze images
			$images = array ();
			
			//[[Image:Voorbeeld.png]]
			
			// Replace external links
			$images = array ();
			preg_match_all ("/\[\[Image:([^|]*?)]]/si", $content, $images,  PREG_PATTERN_ORDER  );
			
			// Fetch all image infos
			$imageOut = array ();
			
			foreach ($images[1] as $k => $v)
			{
				$imgs = WIKI_GUIDE_URL . $params2 . 'Image:'.addslashes ($v);
				$imgdata = @file_get_contents ($imgs);
				if (!$imgdata) {
					continue;
				}

				$imgdata = unserialize ($imgdata);
				if (!$imgdata) {
					continue;
                }

                if (
                	!isset($imgdata['query']) ||
					!isset($imgdata['query']['pages']) ||
					!is_array($imgdata['query']['pages'])
				) {
					continue;
				}

				$imgdata = Neuron_Core_Tools::getArrayFirstValue ($imgdata['query']['pages']);	

				if (isset ($imgdata[1]['imageinfo']))
				{
					$sUrl = WIKI_GUIDE_URL . substr ($imgdata[1]['imageinfo'][0]['url'], 1);
					$content = str_replace ($images[0][$k], '***img***'.$sUrl.'***/img***', $content);
				}
			}
			
			// Replace external links			
			$content = preg_replace (
				'/([^\[]{1})' . '\[' . '([^ \]]+)' . '\]' . '([^\]]{1})' . '/si',
				'\\1[url]\\2[/url]\\3',
				$content
			);

			$content = preg_replace (
				'/([^\[]{1})' . '\[' . '([^ \]]+)' . ' ([^\]]+)' . '\]' . '([^\]]{1})' . '/si',
				'\\1[url=\\2]\\3[/url]\\4',
				$content
			);
			
			// Replace code
			$content = str_replace ('<code>', '[code]', $content);
			$content = str_replace ('</code>', '[/code]', $content);
			
			$content = str_replace ('***img***', '[img align="left"]', $content);
			$content = str_replace ('***/img***', '[/img]', $content);

			
			// Replace headers
			$content = preg_replace ("/====([^|]*?)====/si", "[h4]\\1[/h4]", $content);
			$content = preg_replace ("/===([^|]*?)===/si", "[h3]\\1[/h3]", $content);
			$content = preg_replace ("/==([^|]*?)==/si", "[h2]\\1[/h2]", $content);
			
			$content = html_entity_decode ($content, ENT_NOQUOTES, 'UTF-8');
			
			$content = preg_replace (
				"/\[\[([^|]*?)]]/si",
				'[action title="\\1" data="{\'page\':\\1\'}"]\\1[/action]',
				$content
			);

			$content = preg_replace (
				"/\[\[([^|\[]*?)\|([^|\]]*?)\]\]/si",
				'[action title="\\2" data="{\'page\':\'\\1\'}"]\\2[/action]',
				$content
			);
			
			$content = Neuron_Core_Tools::output_text ($content);
			
			return $content;
		}
	}
}
?>
