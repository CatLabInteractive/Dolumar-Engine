<?php
class Neuron_GameServer_Pages_Admin_Clearmultis extends Neuron_GameServer_Pages_Admin_Page
{
	private $playermap;
	private $added = array ();

	public function getBody ()
	{	
		$page = new Neuron_Core_Template ();
		
		// Let's find the players
		$input = Neuron_Core_Tools::getInput ('_GET', 'players', 'varchar');
		$playerids = explode ('|', $input);
		
		$page->set ('action_url', $this->getUrl ('clearmultis', array ('players' => $input)));
		
		$players = array ();
		
		$ids = array ();
		
		$i = 0;
		
		foreach ($playerids as $v)
		{
			$player = $this->getPlayer ($v);
			
			if ($player)
			{
				$players[] = $player;
			
				$page->addListValue
				(
					'players',
					array
					(
						'id' => $player->getId (),
						'name' => $player->getDisplayName ()
					)
				);
			}
		}
		
		$this->process ($players);
		
		$clearances = $this->getClearances ($players);
		foreach ($clearances as $v)
		{
			$page->addListValue 
			(
				'clearances', 
				array
				(
					'player1' => $this->getPlayerName ($v['player1']),
					'player2' => $this->getPlayerName ($v['player2']),
					'remove_url' => $this->getUrl ('clearmultis', array ('players' => $input, 'remove' => $v['id'])),
					'reason' => $v['reason']
				)
			);
		}
		
		return $page->parse ('pages/admin/clearmultis/clearmultis.phpt');
	}
	
	private function getPlayerName ($player)
	{
		return '<a href="'.$this->getUrl ('user', array ('id' => $player->getId ())).'">'.$player->getName ().'</a>';
	}
	
	private function getPlayer ($id)
	{
		if (!isset ($this->playermap[$id]))
		{
			$player = Neuron_GameServer::getPlayer ($id);
			if ($player)
			{
				$this->playermap[$id] = $player;
			}
			else
			{
				return false;
			}
		}
		
		return $this->playermap[$id];
	}
	
	private function process ($players)
	{
		$input = $_REQUEST;
		
		if (isset ($input['remove']))
		{
			$this->removeClearance ($input['remove']);
		}
		
		$reason = isset ($input['reason']) ? $input['reason'] : null;
	
		$clear = array ();
			
		foreach ($players as $v)
		{
			$k = 'clear_chk_player_'.$v->getId ();
		
			if (isset ($input[$k]) && $input[$k] == 'clear')
			{
				$clear[] = $v;
			}
		}
		
		foreach ($clear as $v1)
		{
			foreach ($clear as $v2)
			{
				$this->addClearance ($v1, $v2, $reason);
			}
		}
	}
	
	private function removeClearance ($id)
	{
		$id = intval ($id);
		
		$db = Neuron_DB_Database::getInstance ();
		
		$db->query
		("
			DELETE FROM
				players_admin_cleared
			WHERE
				pac_id = {$id}
		");
	}
	
	private function addClearance ($player1, $player2, $reason)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		if ($player1->equals ($player2))
		{
			return false;
		}
	
		$id1 = $player1->getId ();
		$id2 = $player2->getId ();
		
		// Sort thze ids
		if ($id1 > $id2)
		{
			$tmp = $id2;
			$id2 = $id1;
			$id1 = $tmp;
		}
		
		$key = $id1.'_'.$id2;
		
		if (!isset ($this->added[$key]))
		{
			$this->added[$key] = true;
			
			$chk = $db->query
			("
				SELECT
					*
				FROM
					players_admin_cleared
				WHERE
					pac_plid1 = {$id1} AND
					pac_plid2 = {$id2}
			");
			
			if (count ($chk) == 0)
			{
				$db->query
				("
					INSERT INTO
						players_admin_cleared
					SET
						pac_plid1 = {$id1},
						pac_plid2 = {$id2},
						pac_reason = '{$db->escape ($reason)}'
				");
			}
		}
		
		return true;
	}
	
	private function getClearances ($players)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		if (count ($players) < 1)
		{
			return array ();
		}
		
		$sWhere = "";
		foreach ($players as $v)
		{
			$sWhere .= "(pac_plid1 = {$v->getId ()} OR pac_plid2 = {$v->getId ()}) OR ";
		}
		$sWhere = substr ($sWhere, 0, -4);
		
		$data = $db->query
		("
			SELECT
				*
			FROM
				players_admin_cleared
			WHERE
				$sWhere
		");
		
		$out = array ();
		foreach ($data as $v)
		{
			$out[] = array
			(
				'id' => $v['pac_id'],
				'player1' => $this->getPlayer ($v['pac_plid1']),
				'player2' => $this->getPlayer ($v['pac_plid2']),
				'reason' => $v['pac_reason']
			);
		}
		
		return $out;
	}
}
?>
