<?php

$db = Neuron_DB_Database::getInstance ();

$from = Neuron_Core_Tools::getInput ('_GET', 'from', 'int', 0);

$players = $db->query
("
	SELECT
		*
	FROM
		n_players
	WHERE
		plid > {$from}
	ORDER BY
		plid ASC
");

$thijs = Neuron_GameServer::getPlayer (1)->getMainVillage ();

echo '<pre>';
foreach ($players as $playerdata)
{
	$player = Neuron_GameServer::getPlayer ($playerdata['plid']);

	foreach ($player->getVillages () as $village)
	{
		$capacity = $village->resources->getCapacity ();
		$resources = $village->resources->getResources ();
		
		$diff = array ();
		$sum = 0;
		foreach ($capacity as $k => $v)
		{
			$diff[$k] = $capacity[$k] - $resources[$k];
			if ($diff[$k] < 0)
			{
				$diff[$k] = 0;
			}
			$sum += $diff[$k];
		}
		
		if ($sum > 0)
		{
			$village->resources->giveResources ($diff);
			
			$objLogs = Dolumar_Players_Logs::__getInstance ();
			$objLogs->addResourceTransferLog ($thijs, $village, $diff);
			
			echo "Giving " . $sum . " resources to " . $village->getName () . " (" . $player->getId () . ")\n";
		}
	}
}
echo '</pre>';
?>
