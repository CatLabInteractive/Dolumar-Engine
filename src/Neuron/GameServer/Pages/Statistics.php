<?php
class Neuron_GameServer_Pages_Statistics extends Neuron_GameServer_Pages_Page
{
	public function getBody ()
	{
		$player = Neuron_GameServer::getPlayer (Neuron_Core_Tools::getInput ('_GET', 'id', 'int'));

		$statistics = $player->getStatistics ();
		$out = $this->linearArray ($statistics);

		$page = new Neuron_Core_Template ();
		$page->set ('statistics', $out);
		return $page->parse ('gameserver/pages/statistics.phpt');
	}

	private function linearArray ($statistics, $out = array (), $keyprefix = '')
	{
		foreach ($statistics as $k => $v)
		{
			if (is_array ($v))
			{
				$this->linearArray ($v, &$out, $k . '_');
			}
			else
			{
				$out[$keyprefix . $k] = $v;
			}
		}
		return $out;
	}
}