<?php

/*
	This script tests the memory issues in the ranking API.
*/

// Fetch ALL players
define ('DISABLE_STATIC_FACTORY', true);

$db = Neuron_Core_Database::__getInstance ();

$players = $db->getDataFromQuery
(
	$db->customQuery
	("
		SELECT
			players.*, 
			SUM(villages.networth) AS score
		FROM
			players
		LEFT JOIN
			villages ON villages.plid = players.plid
		WHERE
			players.plid IS NOT NULL 
			AND villages.vid IS NOT NULL
			AND villages.isActive = 1
			AND players.isPlaying = '1' 
			AND players.isRemoved = '0'
		GROUP BY
			villages.vid
		ORDER BY
			score DESC,
			LOWER(players.nickname) ASC
	")
);

$last = memory_get_usage ();
$run = 0;

function getMemoryUsage ()
{
	global $last;
	global $run;

	$usage = memory_get_usage ();
	$out = "Run " . $run . ": " . number_format ($usage) . ' <span style="color: green;">'.($usage - $last).'</span>';
	$last = $usage;
	
	$run ++;

	return $out;
}

echo '<pre>';
echo 'Memory usage (initial) '.getMemoryUsage()."\n";

foreach ($players as $v)
{

		$objPlayer = Neuron_GameServer::getPlayer ($v['plid'], $v);

		$tmp = $objPlayer->getBrowserBasedGamesData ();

		$objPlayer->__destruct ();
		unset ($objPlayer);

		echo getMemoryUsage()."\n";
}

echo '</pre>';

?>
