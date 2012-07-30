<?php
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
