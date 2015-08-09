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

/*
function calculateClickable ($width, $height)
{
	$hw = floor ($width / 2);
	$ch = floor ($hw / 2);
	
	// Let's start the coordinates
	$cors = array
	(
		array (0, 0),
		array (0, $height - $ch),
		array ($hw, $height),
		array ($width, $height - $ch),
		array ($width, 0)
	);
	
	return $cors;
	
}

function parseClickable ($clickable)
{
	$out = array ();
	
	$clicks = explode (',', $clickable);

	$half = count ($clicks) / 2;
	for ($i = 0; $i < $half; $i ++)
	{
		$out[] = array (intval ($clicks[$i*2]), intval ($clicks[($i*2)+1]));
	}
	
	return $out;
}

$stats = Neuron_Core_Stats::__getInstance ();

// Fetch the images
$toScan = array ('tiles', 'sprites');

foreach ($toScan as $folder)
{
	$imageUrl = 'static/images/'.$folder.'/';
	
	$files = scandir ($imageUrl);

	foreach ($files as $file)
	{
		$short = explode ('.', $file);
		$extension = $short[count($short)-1];
		$short = $short[0];
		
		if (!empty ($short) && ($extension == 'gif' || $extension == 'png'))
		{
			$imageSize = @getimagesize ($imageUrl . $file);
			
			if ($imageSize)
			{			
				// Clickable area
				$clickable = $stats->get ('clickable', $short, 'images');
				if (!isset ($clickable))
				{
					$clickable = calculateClickable ($imageSize[0], $imageSize[1]);
				}
				else
				{
					$clickable = parseClickable ($clickable);
				}
			
				$output[] = array 
				(
					strtolower ($short),
					$imageUrl . $file,
					$imageSize[0],
					$imageSize[1],
					$stats->get ('offsetx', $short, 'images', 0),
					$stats->get ('offsety', $short, 'images', 0),
					$clickable
				);
			}
		}
	}
}
*/

// Efkes zonder preloading.
$output = array ();

echo json_encode ($output);

?>
