<?php
class Neuron_GameServer_Pages_Admin_Execute extends Neuron_GameServer_Pages_Admin_Page
{
	public function getBody ()
	{
		$myself = Neuron_GameServer::getPlayer ();
		if (!$myself->isAdmin ())
		{
			return '<p>You are not allowed to execute the commands. Only admins are.</p>';
		}
		
		// Check for input
		$record = Neuron_Core_Tools::getInput ('_GET', 'id', 'int');
		$action = Neuron_Core_Tools::getInput ('_GET', 'action', 'varchar');
		if ($record && $action)
		{
			$this->processAction ($record, $action == 'accept');
		}
	
		$page = new Neuron_Core_Template ();
		
		$db = Neuron_DB_Database::getInstance ();
		
		$list = $db->query
		("
			SELECT
				*
			FROM
				mod_actions
			WHERE
				ma_processed = 0
			ORDER BY
				ma_date ASC
		");
		
		foreach ($list as $v)
		{
			$params = json_decode ($v['ma_data'], true);
			
			$target = false;
			if (isset ($params['plid']))
			{
				$target = Neuron_GameServer::getPlayer ($params['plid']);
			}
			
			$actor = Neuron_GameServer::getPlayer ($v['ma_plid']);
		
			$page->addListValue
			(
				'actions',
				array
				(
					'date' => $v['ma_date'],
					'action' => $v['ma_action'],
					'target' => $target ? 
						$target->getDisplayName ()
						: null,
					'actor' => $actor ? 
						$actor->getDisplayName ()
						 : null,
					'reason' => !empty ($v['ma_reason']) ? 
						Neuron_Core_Tools::output_text ($v['ma_reason'])
						: null,
					'accept_url' => self::getUrl ('execute', array ('id' => $v['ma_id'], 'action' => 'accept')),
					'deny_url' => self::getUrl ('execute', array ('id' => $v['ma_id'], 'action' => 'deny')),
				)
			);
		}
		
		return $page->parse ('pages/admin/execute/list.phpt');
	}
	
	private function processAction ($id, $bExecute)
	{
		$id = intval ($id);
		$bExecute = $bExecute ? true : false;
		
		$db = Neuron_DB_Database::getInstance ();
		
		if ($bExecute)
		{
			$input = $db->query
			("
				SELECT
					*
				FROM
					mod_actions
				WHERE
					ma_id = {$id}
			");
			
			if (count ($input) == 1)
			{
				$this->executeAction ($input[0]);
			}
		}
		
		$db->query
		("
			UPDATE
				mod_actions
			SET
				ma_processed = 1,
				ma_executed = ".($bExecute ? '1' : '0')."
			WHERE
				ma_id = {$id}
		");
	}
	
	private function executeAction ($data)
	{
		$params = json_decode ($data['ma_data'], true);
	
		$target = false;
		if (isset ($params['plid']))
		{
			$target = Neuron_GameServer::getPlayer ($params['plid']);
		}
	
		switch ($data['ma_action'])
		{
			case 'reset':
			
				$title = $params['title'];
				$message = $params['message'];
				
				$myself = Neuron_GameServer::getPlayer ();
				
				$msgs = new Neuron_Structure_Messages ($myself);
				$msgs->sendMessage ($target, $title, $message);
		
				//customMail ($target->getEmail (), $title, $message);
				$target->execResetAccount ();
				
			break;
		}
	}
}
?>
