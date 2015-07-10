<?php
	define ('MAP_PERLIN_NO_CACHE', 'yus');
?>

<form method="post">
	<label for="key">Random map key:</label>
	<input type="text" name="key" value="<?=isset($_POST['key']) ? $_POST['key'] : null?>" />
	<button type="submit">Test!</button>
</form>

<?php

	echo '<h2>Current key</h2>';
	echo '<p>Config key: "'.RANDMAPFACTOR.'"</p>';

	if (isset ($_POST['key']))
	{
		$keys = explode (',', $_POST['key']);
		foreach ($keys as $key)
		{
			$key = trim ($key);
			$GLOBALS['MAP_CONFIG_RANDMAPFACTOR'] = $key;
		
			echo '<h2>Checking hash "'.$key.'" against local cache:</h2>';
			echo '<ul>';
	
			// Check the first 25 square for a match
			for ($i = 0; $i < 2; $i ++)
			{
				for ($j = 0; $j < 2; $j ++)
				{
					$d1 = Dolumar_Map_Map::getLocation ($i, $j, false, false);
					$d2 = Dolumar_Map_Map::getLocation ($i, $j, false, true);
				
					// Check if equal
					$img1 = $d1->getImage ();
					$img2 = $d2->getImage ();
					
					echo '<li>';
					if ($d1->getHeight () == $d2->getHeight () && $img1['image'] == $img2['image'])
					{
						echo '<span style="color: green;">Location '.$i.','.$j.' does match!</span>';
					}
					else
					{
						echo '<span style="color: red;">Location '.$i.','.$j.' does not match ('.$img1['image'].' != '.$img2['image'].').</span>';
						
						echo '<pre>';
						echo "Fresh data:\n";
						print_r ($d1);
						echo "Cache:\n";
						print_r ($d2);
						echo '</pre>';
						
						break 2;
					}
					echo '</li>';
				}
			}
		
			echo '</ul>';
		}
	}
?>
