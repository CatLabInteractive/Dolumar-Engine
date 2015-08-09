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

define ('DISABLE_STATIC_FACTORY', true);
		
$objCache = Neuron_Core_Cache::__getInstance ('general/');

$parameters = array ('type' => '2d');

$usecache = isset ($_GET['nocache']) ? $_GET['nocache'] != 1 : true;

if ($usecache && $ranking = $objCache->getCache ('ranking.xml', 60*60*12))
{
	header ('Content-type: text/xml');
	echo $ranking;
	exit (1);
}
else
{
	$lock = Neuron_Core_Lock::getInstance ();

	if ($lock->setLock ('ranking_api', 1))
	{
		header ('Content-type: text/xml');

		// Fetch ALL players
		$db = Neuron_Core_Database::__getInstance ();

		$players = $db->getDataFromQuery
		(
			$db->customQuery
			("
				SELECT
					n_players.*, 
					SUM(villages.networth) AS score
				FROM
					n_players
				LEFT JOIN
					villages ON villages.plid = n_players.plid
				WHERE
					n_players.plid IS NOT NULL 
					AND villages.vid IS NOT NULL
					AND villages.isActive = 1
					AND n_players.isPlaying = '1' 
					AND n_players.isRemoved = '0'
				GROUP BY
					n_players.plid
				ORDER BY
					score DESC,
					LOWER(n_players.nickname) ASC
			")
		);
	
		$output = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<browsergameshub version="1" type="2d">';
		$output .= '<players>';
		$objCache->setCache ('ranking.xml', $output);

		foreach ($players as $v)
		{
			$objPlayer = Neuron_GameServer::getPlayer ($v['plid'], $v);

			//$output['content']['players'][] = $objPlayer->getBrowserBasedGamesData ();
		
			$objCache->appendCache 
			(
				'ranking.xml', 
				Neuron_Core_Tools::output_partly_xml ($objPlayer->getBrowserBasedGamesData (), 'player')
			);
			
			$objPlayer->__destruct ();
			unset ($objPlayer);
		}
	
		$objCache->appendCache ('ranking.xml', ' </players>');
	
		$objCache->appendCache ('ranking.xml', '<clans>');

		// Clans
		$clans = $db->getDataFromQuery
		(
			$db->customQuery
			("
				SELECT
					clans.*,
					SUM(villages.networth) / COUNT(clan_members.plid) AS score
				FROM
					clans
				LEFT JOIN
					clan_members ON clans.c_id = clan_members.c_id AND clan_members.cm_active = 1
				LEFT JOIN
					n_players ON clan_members.plid = n_players.plid 
				LEFT JOIN
					villages ON villages.plid = n_players.plid
				GROUP BY clans.c_id
				ORDER BY
					clans.c_score DESC
			")
		);

		foreach ($clans as $v)
		{
			$tmp = array
			(
				'name' => $v['c_name'],
				'clan_id' => $v['c_id'],
				'score' => floor ($v['score']),
				'members' => array ()
			);
	
			// Load all members
			$members = $db->getDataFromQuery
			(
				$db->customQuery
				("
					SELECT
						*
					FROM
						clan_members
					WHERE
						clan_members.c_id = ".$v['c_id']."
						AND clan_members.cm_active = 1
				")
			);
	
			foreach ($members as $v)
			{
				$tmp['members'][] = array
				(
					'member_id' => $v['plid']
				);
			}
	
		
			$objCache->appendCache ('ranking.xml', Neuron_Core_Tools::output_partly_xml ($tmp, 'clan'));
		}
	
		$objCache->appendCache ('ranking.xml', '</clans>');
		$objCache->appendCache ('ranking.xml', '</browsergameshub>');
	
		//echo $sRanking;
		$lock->releaseLock ('ranking_api', 1);
		
		echo $objCache->getCache ('ranking.xml');
	}
	else
	{
		echo 'Try again soon.';
	}
}
?>
