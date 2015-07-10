<?php
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
