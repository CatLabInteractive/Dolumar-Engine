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

class Neuron_GameServer_Windows_Imprint extends Neuron_GameServer_Windows_Help
{	
	public function getDefaultPage ()
	{
		return 'Imprint';
	}
	
	public function getAdditionalContent ($page)
	{
		$page = new Neuron_Core_Template ();
		
		$modes = Neuron_GameServer_Player::getAdminModes ();
		
		$out = array ();
		
		$db = Neuron_DB_Database::getInstance ();
		
		$i = 0;
		
		foreach ($modes as $k => $v)
		{
			if ($k > 0 && $k < 8)
			{
				$out[$v] = array ();
			
				$k = intval ($k);
			
				$sql = $db->query
				("
					SELECT
						plid
					FROM
						n_players
					WHERE
						p_admin = {$k}
				");
			
				foreach ($sql as $vv)
				{
					$player = Neuron_GameServer::getPlayer ($vv['plid']);
					$out[$v][] = $player->getDisplayName ();
					
					$i ++;
				}
			}
		}
		
		$page->set ('moderators', $out);
		$page->set ('hasmods', $i > 0);
		
		return $page->parse ('neuron/imprint/imprint.phpt');
	}
	
	public function getFooter ()
	{
		return null;
	}
}
?>
