<?php
class Neuron_GameServer_Windows_ClanChat
	extends Neuron_GameServer_Windows_BaseChat
{
	protected function getTitle ()
	{
		$clan = $this->getClan ();
		if ($clan)
		{
			return $this->getClan ()->getName ();
		}
		else
		{
			return 'Clan not found.';
		}
	}

	protected function getHeader ()
	{
		return '';
	}

	private function getClan ()
	{
		$requestData = $this->getRequestData ();
		
		if (isset ($requestData['clan']))
		{
			$clan = new Dolumar_Players_Clan ($requestData['clan']);
			if ($clan)
			{
				$login = Neuron_Core_Login::__getInstance ();

				if ($login->isLogin ())
				{
					$me = Neuron_GameServer::getPlayer ();
				}

				if ($clan->isMember ($me))
				{
					return $clan;
				}
			}
		}

		return null;
	}

	protected function getChannelName ()
	{
		if ($clan = $this->getClan ())
		{
			return 'clan:' . $clan->getId ();
		}
		else
		{
			throw new Neuron_Exceptions_DataNotSet ('id');
		}
	}

}