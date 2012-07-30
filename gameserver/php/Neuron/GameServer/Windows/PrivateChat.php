<?php
class Neuron_GameServer_Windows_PrivateChat
	extends Neuron_GameServer_Windows_BaseChat
{
	protected function getTitle ()
	{
		try
		{
			$target = $this->getTarget ();
			return $target->getName ();
		}
		catch (Exception $e)
		{
			return 'Player not found';
		}
	}

	public function getHeader ()
	{
		return '<p>Please be respectful to everyone.</p>';
	}

	private function getTarget ()
	{
		$requestdata = $this->getRequestData ();

		$id1 = $requestdata['id'];
		$player = Neuron_GameServer::getPlayer ($id1);

		if (!$player || $player->equals (Neuron_GameServer::getPlayer ()))
		{
			throw new Neuron_Exceptions_DataNotSet ('Player not found');
		}

		return $player;
	}

	protected function onReceivedMessages (Neuron_GameServer_Mappers_ChatMapper $mapper)
	{
		$me = Neuron_GameServer::getPlayer ();
		$target = $this->getTarget ();

		$mapper->setPrivateChatUpdateRead ($target, $me);
	}

	protected function onPostMessage ($msgid, Neuron_GameServer_Mappers_ChatMapper $mapper)
	{
		$me = Neuron_GameServer::getPlayer ();
		$mapper->addPrivateChatUpdate ($msgid, $me, $this->getTarget ());
	}

	protected function getChannelName ()
	{
		$requestdata = $this->getRequestData ();

		if (!isset ($requestdata['id']))
		{
			throw new Neuron_Exceptions_DataNotSet ('id');
		}

		else
		{
			$id1 = Neuron_GameServer::getPlayer ()->getId ();
			$player = $this->getTarget ();

			$id2 = $player->getId ();

			// Now, do something funny: lowest goes first
			if ($id1 < $id2)
			{
				return 'private:' . $id1 . ':' . $id2;
			}
			else
			{
				return 'private:' . $id2 . ':' . $id1;
			}
		}
	}

}