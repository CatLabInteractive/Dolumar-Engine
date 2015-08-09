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

class Neuron_GameServer_Pages_Admin_Multis extends Neuron_GameServer_Pages_Admin_Page
{
	public function getBody ()
	{
		$timeframe = Neuron_Core_Tools::getInput ('_GET', 'timeframe', 'int', 60*60*48);
	
		$db = Neuron_DB_Database::__getInstance ();
		
		$page = new Neuron_Core_Template ();
		$page->set ('timeframe', $timeframe);
		
		// Fetch all doubles
		$data = $db->query
		("
			SELECT
				n_login_log.l_ip,
				
				GROUP_CONCAT(DISTINCT n_login_log.l_plid) AS plids,
				GROUP_CONCAT(DISTINCT n_players.nickname) AS nicknames,
				
				GROUP_CONCAT(c.pac_plid1) AS cleared_1,
				GROUP_CONCAT(c.pac_plid2) AS cleared_2,
				GROUP_CONCAT(c.pac_reason) AS cleared_reason,
				
				COUNT(DISTINCT l_plid) AS aantal
			FROM
				n_login_log
			LEFT JOIN
				n_players ON n_login_log.l_plid = n_players.plid
			LEFT JOIN
				n_players_admin_cleared c ON (c.pac_plid1 = n_login_log.l_plid OR c.pac_plid2 = n_login_log.l_plid)
			WHERE
				n_login_log.l_datetime > FROM_UNIXTIME(".(NOW - $timeframe).") AND
				n_players.isPlaying = 1
			GROUP BY
				l_ip
			HAVING
				aantal > 1
		");
		
		foreach ($data as $row)
		{
			$plids = explode (',', $row['plids']);
			$nicknames = explode (',', $row['nicknames']);
			
			// Check clearances.
			$clearances = $this->getClearancesFromRow ($row);
			
			$players = array ();
			$combinedlogs = "";
			
			foreach ($plids as $k => $v)
			{
				$players[] = array 
				(
					'id' => $plids[$k], 
					'name' => isset ($nicknames[$k]) ? $nicknames[$k] : 'no-nickname-set',
					'url' => $this->getUrl ('user', array ('id' => $plids[$k])),
					'logs_url' => $this->getUrl ('gamelogs', array ('players' => $plids[$k]))
				);
				
				$combinedlogs .= $plids[$k]."|";
			}
			
			// Check for cleared accounts
			$allcleared = true;
			
			foreach ($players as $k => $v)
			{
				$players[$k]['cleared'] = $this->isCleared ($clearances, $v, $players);
				if ($allcleared && !$players[$k]['cleared'])
				{
					$allcleared = false;
				}
			}
			
			$combinedlogs = substr ($combinedlogs, 0, -1);
		
			$page->addListValue
			(
				'players',
				array
				(
					'ip' => $row['l_ip'],
					'players' => $players,
					'combined_logs_url' => $this->getUrl ('gamelogs', array ('players' => $combinedlogs)),
					'clearmultis' => $this->getUrl ('clearmultis', array ('players' => $combinedlogs)),
					'cleared' => $allcleared,
					'amount' => $row['aantal']
				)
			);
		}
		
		$page->usortList ('players', array ($this, 'sortcompare'));
		
		return $page->parse ('pages/admin/multis.phpt');
	}
	
	public function sortcompare ($a1, $a2)
	{
		if ($a1['cleared'] && !$a2['cleared'])
		{
			return 1;
		}
		
		elseif ($a2['cleared'] && !$a1['cleared'])
		{
			return -1;
		}
		
		else
		{
			//return strcmp ($a1['ip'], $a2['ip']);
			return $a1['amount'] > $a2['amount'] ? -1 : +1;
		}
	}
	
	private function isCleared ($clearances, $player, $players)
	{
		foreach ($clearances as $clear)
		{
			foreach ($players as $multi)
			{
				if
				(
					($clear['player1'] == $player['id'] && $clear['player2'] == $multi['id'])
					|| ($clear['player2'] == $player['id'] && $clear['player1'] == $multi['id'])
				)
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	private function getClearancesFromRow ($row)
	{
		$clearances = array ();
		
		if (!empty ($row['cleared_1']))
		{
			$d_player1 = explode (',', $row['cleared_1']);
			$d_player2 = explode (',', $row['cleared_2']);
			$d_reason  = explode (',', $row['cleared_reason']);
		
			foreach ($d_player1 as $k => $v)
			{
				$clearances[] = array
				(
					'player1' => isset ($d_player1[$k]) ? $d_player1[$k] : null,
					'player2' => isset ($d_player2[$k]) ? $d_player2[$k] : null,
					'reason' => isset ($d_reason[$k]) ? $d_reason[$k] : null
				);
			}
		}
		
		return $clearances;
	}
}
?>
