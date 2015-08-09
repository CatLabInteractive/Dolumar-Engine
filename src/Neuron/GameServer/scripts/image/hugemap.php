<?php


include ('php/connect.php');

$imageSize = 250;
if (isset ($_GET['x']) && isset ($_GET['y']))
{

	header('Content-type: image/png');

	function color_cache ($im, $color)
	{
		global $color_cache;
		
		if (!isset ($color_cache[$color[0].'_'.$color[1].'_'.$color[2]]))
		{
			$color_cache[$color[0].'_'.$color[1].'_'.$color[2]] = imagecolorallocate ($im, $color[0], $color[1], $color[2]);
		}
		return $color_cache[$color[0].'_'.$color[1].'_'.$color[2]];
	}

	// Check for file
	$file = 'cache/hugemap/map'.$_GET['x'].'p'.$_GET['y'].'.png';
	
	$parseNew = true;
	if (file_exists ($file))
	{
		if (filectime ($file) > (time() - 60 * 60 * 24 * 31))
		{
			$parseNew = isset ($_GET['parseNew']);

			if ($parseNew)
			{
				unlink ($file);
			}

			else
			{
				echo file_get_contents ($file);
			}
		}
		
		else
		{
			unlink ($file);
		}
	}
	
	if ($parseNew)
	{

		// Load buildings from SQL
		$points = array 
		(
			array 
			(
				$_GET['x'] * $imageSize + ($imageSize / 2), 
				$_GET['y'] * $imageSize + ($imageSize / 2)
			)
		);
		
		$buildingSQL = Dolumar_Map_Map::getBuildingsFromLocations ($points, $imageSize);
		$buildings = array ();
		foreach ($buildingSQL as $buildingV)
		{
			$race = Dolumar_Races_Race::getRace ($buildingV['race']);

			$b = Dolumar_Buildings_Building::getBuilding ($buildingV['buildingType'], $race, $buildingV['xas'], $buildingV['yas']);

			$b->setData ($buildingV['bid'], $buildingV);
			$buildings[floor ($buildingV['xas'])][floor ($buildingV['yas'])][] = $b;
		}

		// Add buildings
		$startX = $_GET['x'] * $imageSize;
		$endX = $startX + $imageSize;

		$startY = $_GET['y'] * $imageSize;
		$endY = $startY + $imageSize;

		$im = imagecreate ($imageSize, $imageSize);

		$i = 0;
		for ($x = $startX; $x <= $endX; $x ++)
		{
			$j = 0;
			for ($y = $startY; $y <= $endY; $y ++)
			{
				// Check for building
				if (isset ($buildings[$x]) && isset ($buildings[$x][$y]))
				{
					$color = color_cache ($im, $buildings[$x][$y][0]->getMapColor ());
				}
				
				else
				{
					$location = Dolumar_Map_Location::getLocation ($x, $y);

					$c = $location->getHeightIntencity ();
					$col = $location->getMapColor ();

					$col[0] = floor ($col[0] * $c);
					$col[1] = floor ($col[1] * $c);
					$col[2] = floor ($col[2] * $c);
					
					$color = color_cache ($im, $col);
				}

				imagerectangle ($im, $i, $j, $i+1, $j+1, $color);
				$j ++;
			}
			$i ++;
		}

		imagepng ($im, $file);
		imagepng ($im);

	}
}

else
{

	// Get HTML
	$parts = MAXMAPSTRAAL / $imageSize;
	for ($i = -$parts; $i < $parts; $i ++)
	{
		for ($j = -$parts; $j < $parts; $j ++)
		{
			$file = 'cache/hugemap/map'.$i.'p'.$j.'.png';
			if (file_exists ($file))
			{
				echo '<img style="position: absolute; left: '.(($i * $imageSize) + MAXMAPSTRAAL).'px; top: '.(($j * $imageSize)+MAXMAPSTRAAL).'px;" src="hugemap.php?x='.$i.'&y='.$j.'" />';
			}
		}
	}

}

?>
