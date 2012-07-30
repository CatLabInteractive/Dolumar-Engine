<?php
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
						players
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
