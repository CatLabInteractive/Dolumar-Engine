<?php 
function color_cache ($im, $color)
{
	global $color_cache;
	
	if (!isset ($color_cache[$color[0].'_'.$color[1].'_'.$color[2]]))
	{
		$color_cache[$color[0].'_'.$color[1].'_'.$color[2]] = imagecolorallocate ($im, $color[0], $color[1], $color[2]);
	}
	return $color_cache[$color[0].'_'.$color[1].'_'.$color[2]];
}

if (isset ($_GET['x']) && isset ($_GET['y']))
{
	$extension = 'png';
	
	$cache = Neuron_Core_Cache::__getInstance ('worldmap/');
	
	$width = Neuron_Core_Tools::getInput ('_GET', 'width', 'int', 250);
	$height = Neuron_Core_Tools::getInput ('_GET', 'height', 'int', 250);
	
	// Fetch cache
	$cachename = 'i'.intval($_GET['x']).'p'.intval($_GET['y']).'w'.$width.'h'.$height.'p'.$extension;
	
	$image = $cache->getCache ($cachename, 60 * 60 * 24 * 2);
	
	if ($image)
	{
		header("Content-type: image/".$extension);
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + 60*60*12) . " GMT");
		echo $image;
	}
	
	else
	{
		$x = $_GET['x'];
		$y = $_GET['y'] - 1;
		
		$startX = $x * $width;
		$startY = ($y) * $height;
		
		$endX = ($x + 1) * $width;
		$endY = ($y + 1) * $height;
		
		// Load from cache
		$db = Neuron_DB_Database::__getInstance ();
		
		$data = $db->query
		("
			SELECT
				*
			FROM
				z_cache_tiles
			WHERE
				t_ix >= $startX AND t_ix <= $endX AND
				t_iy >= $startY AND t_iy <= $endY
				
		");
		
		$im = imagecreate ($width, $height);
		$background = imagecolorallocate ($im, 0, 0, 0);
		
		// Allocate black
		//$black = imagecolorallocate ($im, array (0, 0, 0));
		
		// Prepare all colours
		$colors = array ();
		
		$colors[0] = color_cache ($im, array (105, 178, 0));
		$colors[1] = color_cache ($im, array (0, 0, 150));
		$colors[2] = color_cache ($im, array (159, 188, 0));
		$colors[3] = color_cache ($im, array (61, 132, 26));
		$colors[4] = color_cache ($im, array (255, 255, 100));
		$colors[5] = color_cache ($im, array (255, 200, 0));

		// Show all these pixels
		foreach ($data as $v)
		{
			$color = isset ($colors[$v['t_tile']]) ? $colors[$v['t_tile']] : $black;
			$location = array ($v['t_ix'] - $startX, $height - ($v['t_iy'] - $startY));
			
			imagesetpixel ($im, $location[0], $location[1], $color);
		}
		
		// Start output buffering
		ob_start ();
		
		if ($extension == 'gif')
		{
			imagegif ($im);
		}
		
		else
		{			
			imagepng ($im, null);
		}
		
		imagedestroy($im);
		
		// Fetch thze output & flush it down the toilet
		header("Content-type: image/".$extension);
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + 60*60*12) . " GMT");
		header("Cache-Control: max-age=315360000");
		
		$output = ob_get_flush();		
		$cache->setCache ($cachename, $output);
	}
}
?>
