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
	Windows_MapUpdater
	
	This (invisible) window will make sure the map is up to date.
*/
class Neuron_GameServer_Windows_ChatPopper 
	extends Neuron_GameServer_Windows_Window
{	
	public function setSettings ()
	{
		$this->setClassName ('invisible');
		$this->setType ('invisible');
		$this->setAllowOnlyOnce ();

		$this->setPool ('newchat');
		
		$this->setAjaxPollSeconds (5);
	}
	
	public function getContent ()
	{
		$data = $this->getRequestData ();
		return '<p>'.print_r ($data, true).'</p>';
	}
	
	public function getRefresh ()
	{
		$mapper = Neuron_GameServer_Mappers_BlockingChatMapper::getInstance ();

		$data = $this->getRequestData ();

		$player = Neuron_GameServer::getPlayer ();
		if ($player)
		{
			$mapper = Neuron_GameServer_Mappers_ChatMapper::getInstance ();

			if (isset ($data['lastId']))
			{
				$updates = $mapper->getPrivateChatUpdates ($player, $data['lastId']);

				if (count ($updates) > 0)
				{
					$lastId = $updates[0]['pu_id'];

					// open windows for all updates
					foreach ($updates as $v)
					{
						$this->getServer ()->openWindow ('PrivateChat', array ( 'id' => $v['pu_from'] ));
						$lastId = max ($lastId, $v['pu_id']);
					}

					$data['lastId'] = $lastId;

					$this->updateRequestData ($data);
				}
			}
			else
			{
				$updates = $mapper->getPrivateChatUpdates ($player);	
				if (count ($updates) > 0)
				{
					$data['lastId'] = $updates[0]['pu_id'];
					$this->updateRequestData ($data);
				}
				else
				{
					$data['lastId'] = 0;
					$this->updateRequestData ($data);	
				}
			}
		}
		
		$this->updateRequestData ($data);
		$this->updateContent ();
	}
}
?>
