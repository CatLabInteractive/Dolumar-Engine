<?php
class Neuron_Structure_Messages 
{
	private $objPlayer;
	private $error;
	
	private $fGetUrl = null;
	private $iPerPage = 10;

	public function __construct ($objPlayer, $iPerPage = 10)
	{
		$this->objPlayer = $objPlayer;
		$this->iPerPage = $iPerPage;
	}
	
	public function setGetUrl ($fGetUrl)
	{
		$this->fGetUrl = $fGetUrl;
	}
	
	public function setUserUrl ($fUserUrl)
	{
		$this->sUserUrl = $fUserUrl;
	}
	
	public function getPageHTML ($input)
	{
		$text = Neuron_Core_Text::__getInstance ();
		$text->setFile ('messages');
		
		$view = isset($input['view']) ? $input['view'] : 'inbox';
		if(isset($input['formAction']))
		{
			$view = $input['formAction'];
		}
		
		$remove = isset ($input['remove']) ? $input['remove'] : null;
		if ($remove > 0)
		{
			$this->removeMessage ($remove, $view);
		}

		switch ($view)
		{
			case 'read':
				return $this->getReadMessage ($input);
			break;
		
			case 'outbox':
				return $this->getOutbox ($input);
			break;
			
			case 'write':
				return $this->getWriteMessage ($input);
			break;
		
			case 'inbox':
			default:
				return $this->getInbox ($input);
			break;
		}
	}
	
	private function removeMessage ($msgId, $box = null)
	{
		// Fetch the inbox
		$message = $this->getMessage ($msgId, $this->objPlayer->getId ());
		
		if ($message)
		{
			$db = Neuron_DB_Database::getInstance ();
			
			if (isset ($box))
			{
				$isSender = $box == 'outbox';
			}
			else
			{
				$isSender = $message['objFrom']->equals ($this->objPlayer);
			}
			
			$field = $isSender ? 'm_removed_sender' : 'm_removed_target';
			
			$db->query
			("
				UPDATE
					messages
				SET
					$field = 1
				WHERE
					m_id = {$message['id']}
			");
		}
	}
	
	private function getInbox ($input)
	{
		$text = Neuron_Core_Text::__getInstance ();
		
		$page = new Neuron_Core_Template ();
		
		$curpage = isset ($input['page']) ? $input['page'] : 1;
		$this->getMessages ($page, $curpage, 'inbox');
		
		return $page->parse ('messages/inbox.tpl');
	}
	
	private function getOutbox ($input)
	{
		$page = new Neuron_Core_Template ();
		
		$text = Neuron_Core_Text::__getInstance ();
		
		$curpage = isset ($input['page']) ? $input['page'] : 1;
		$this->getMessages ($page, $curpage, 'outbox');
		
		return $page->parse ('messages/outbox.tpl');
	}
	
	private function getMessage ($mId, $plid)
	{
		// Get the message
		$db = Neuron_Core_Database::__getInstance ();
		
		$l = $db->select
		(
			'messages',
			array ('*, UNIX_TIMESTAMP(m_date) AS u_date'),
			"m_id = ".((int)$mId)." AND (m_from = '".$plid."' OR m_target = '".$plid."')"
		);
		
		if(count($l) == 1)
		{
			$objFrom = Neuron_GameServer::getPlayer ($l[0]['m_from']);
			$objTo = Neuron_GameServer::getPlayer ($l[0]['m_target']);
			
			return array
			(
				'id' => $l[0]['m_id'],
				'objFrom' => $objFrom,
				'objTo' => $objTo,
				'sSubject' => $l[0]['m_subject'],
				'sText' => $l[0]['m_text'],
				'isRead' => $l[0]['m_isRead'],
				'sDate' => date ('c', $l[0]['u_date'])
			);	
		}
		else
		{
			return false;
		}
	}
	
	private function getReadMessage ($input)
	{		
		$mId = isset ($input['msg']) ? $input['msg'] : 0;
		$plid = $this->objPlayer->getId ();
		
		$message = $this->getMessage ($mId, $plid);
		
		if ($message)
		{		
			// Check for "unread":
			if (intval ($message['isRead']) == 0 && $message['objTo']->getId () == $plid)
			{
				$db = Neuron_DB_Database::__getInstance ();
				
				$db->query
				("
					UPDATE
						messages
					SET
						m_isRead = 1
					WHERE
						m_id = {$message['id']}
				");
			}
		
			$text = Neuron_Core_Text::__getInstance ();
			$text->setFile ('messages');
			$text->setSection ('read');
			
			$page = new Neuron_Core_Template ();
			
			$page->set ('from', $text->get ('msg_from'));
			$page->set ('to', $text->get ('msg_to'));
			
			$page->set ('to_name', $message['objTo']->getDisplayName ());
			$page->set ('from_name', $message['objFrom']->getDisplayName ());
			
			$page->set ('to_plid', $message['objTo']->getId ());
			$page->set ('from_plid', $message['objFrom']->getId ());
			
			$page->set ('subject', Neuron_Core_Tools::output_varchar ($message['sSubject']));
			$page->set ('message', Neuron_Core_Tools::output_text ($message['sText']));
			
			$page->set ('reply', $text->get ('reply'));
			
			$page->set 
			(
				'replyUrl', 
				$this->getUrl 
				(
					array 
					(
						'view' => 'write', 
						'msg' => $message['id']
					), 
					$text->get('reply')
				)
			);
			
			$isSender = $message['objFrom']->equals ($this->objPlayer);
			
			$page->set 
			(
				'removeUrl', 
				$this->getUrl 
				(
					array 
					(
						'view' => $isSender ? 'outbox' : 'inbox', 
						'remove' => $message['id']
					), 
					$text->get('remove')
				)
			);
			
			$page->set ('remove', $text->get ('remove'));
			
			$page->set ('msgId', $message['id']);
			
			if ($this->objPlayer->getId() == $message['objFrom']->getId())
			{
				$page->set ('toOutbox', $text->getClickTo ($text->get ('toOutbox')));
				$page->set ('toOutboxUrl', $this->getUrl (array ('view' => 'outbox'), $text->getClickTo ($text->get ('toOutbox'))));
			}
			else
			{
				$page->set ('toInbox', $text->getClickTo ($text->get ('toInbox')));
				$page->set ('toInboxUrl', $this->getUrl (array ('view' => 'inbox'), $text->getClickTo ($text->get ('toInbox'))));
			}
			
			return $page->parse ('messages/message.tpl');
		}
		else
		{
			return $this->getInbox ($input);
		}
	}
	
	private function getMessages ($page, $curpage, $box = 'inbox')
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		if ($box == 'outbox')
		{
			$where = "m_from = '".$this->objPlayer->getId ()."' AND m_removed_sender = 0";
		}
		else
		{
			$where = "m_target = '".$this->objPlayer->getId ()."' AND m_removed_target = 0";
		}
		
		$limit = Neuron_Core_Tools::splitInPages 
		(
			$page, 
			$db->countRows ('messages', $where), 
			$curpage, 
			$this->iPerPage, 
			7, 
			($box == 'outbox' ? 'view=outbox' : 'view=inbox'),
			'messages'
		);
		
		$text = Neuron_Core_Text::__getInstance ();
		
		$page->set ('from', $text->get ('from', 'messages', 'messages'));
		$page->set ('to', $text->get ('to', 'messages', 'messages'));
		$page->set ('date', $text->get ('date', 'messages', 'messages'));
		$page->set ('subject', $text->get ('subject', 'messages', 'messages'));

		$page->set ('toOutbox', $text->get ('toOutbox', 'messages', 'messages'));
		$page->set ('toInbox', $text->get ('toInbox', 'messages', 'messages'));
		
		$page->set ('toOutboxUrl', $this->getUrl (array ('view' => 'outbox'), $text->get ('toOutbox', 'messages', 'messages')));
		$page->set ('toInboxUrl', $this->getUrl (array ('view' => 'inbox'), $text->get ('toInbox', 'messages', 'messages')));
		
		$page->set ('inbox', $text->get ('inbox', 'messages', 'messages'));
		$page->set ('outbox', $text->get ('outbox', 'messages', 'messages'));
		
		$page->set ('newmsg', $text->getClickTo($text->get('toNewMsg', 'inbox', 'messages')));
		$page->set ('newmsgurl', $this->getUrl (array ('view' => 'write'), $text->getClickTo($text->get('toNewMsg', 'inbox', 'messages'))));
		
		$l = $db->select
		(
			'messages',
			array ('*', 'UNIX_TIMESTAMP(m_date) AS u_date'),
			$where,
			'm_date DESC',
			$limit['limit']
		);
		
		foreach ($l as $v)
		{
			$subject = $v['m_subject'];
			if (empty ($subject))
			{
				$subject = $text->get ('noSubject', 'messages', 'messages');
			}
		
			$from = Neuron_GameServer::getPlayer ($v['m_from']);
			$to = Neuron_GameServer::getPlayer ($v['m_target']);
			
			$isRead = $v['m_isRead'] == 1;
			
			$page->addListValue
			(
				'messages',
				array
				(
					'subject' => Neuron_Core_Tools::output_varchar ($subject),
					'date' => date (DATETIME, $v['u_date']),
					'from' => Neuron_Core_Tools::output_varchar ($from->getNickname ()),
					'from_id' => $from->getId (),
					'from_url' => $from->getDisplayName (),
					'to' => Neuron_Core_Tools::output_varchar ($to->getNickname ()),
					'to_id' => $to->getId (),
					'to_url' => $to->getDisplayName (),
					'msgId' => $v['m_id'],
					'isRead' => $isRead,
					'sUrl' => $this->getUrl 
					(
						array 
						(
							'view' => 'read', 
							'msg' => $v['m_id']
						), 
						Neuron_Core_Tools::output_varchar ($subject)
					),
					'sReadUrl' => $this->getUrl
					(
						array
						(
							'view' => 'read', 
							'msg' => $v['m_id']
						),
						'<span class="icon message '.($isRead ? 'read' : 'unread').
							'" title="'.$text->get ('read', 'messages', 'messages').'">'.
							'<span>'.$text->get ('read', 'messages', 'messages').'</span></span>'
					),
					'sRemoveUrl' => $this->getUrl
					(
						array
						(
							'view' => $box,
							'remove' => $v['m_id']
						),
						'<span class="icon message remove'.
							'" title="'.$text->get ('remove', 'messages', 'messages').'">'.
							'<span>'.$text->get ('remove', 'messages', 'messages').'</span></span>',
						$text->get ('confirmremove', 'messages', 'messages')
					)
				)
			);
		}
	}
	
	private function getReTitle ($message)
	{
		$sSubject = $message['sSubject'];
		if (strtolower (substr ($sSubject, 0, 3)) != 're:')
		{
			$sSubject = 'Re: '.$sSubject;
		}
		return $sSubject;
	}
	
	private function getQuoteText ($message, $player, $date)
	{
		return '[quote name="'.$player->getNickname ().'" date="'.$date.'"]'.$message['sText'].'[/quote]';
	}
	
	private function getWriteMessage ($input)
	{
		$msgId = isset($input['msg']) ? $input['msg'] : false;
		
		$text = Neuron_Core_Text::__getInstance ();
		$text->setFile('messages');
		$text->setSection('write');
		
		$page = new Neuron_Core_Template();
		
		$page->set ('yourMessage', $text->get ('yourMessage'));
		$page->set ('to', $text->get ('to'));
		$page->set ('from', $text->get ('from'));
		$page->set ('subject', $text->get ('subject'));
		$page->set ('sendMessage', $text->get ('sendMessage'));
		$page->set ('message', $text->get ('message'));
		
		if ($msgId > 0)
		{
			// Load the message
			$message = $this->getMessage ($msgId, $this->objPlayer->getId ());
			
			$page->set ('to_value', Neuron_Core_Tools::output_form ($message['objFrom']->getNickname ()));
			$page->set ('subject_value', Neuron_Core_Tools::output_form ($this->getReTitle ($message)));
			$page->set ('text_value', Neuron_Core_Tools::output_form ($this->getQuoteText ($message, $message['objFrom'], $message['sDate'])));
		}
		else
		{
			if (isset ($input['target']))
			{
				$page->set ('to_value', Neuron_Core_Tools::output_form ($input['target']));
			}
			
			if (isset ($input['subject']))
			{
				$page->set ('subject_value', Neuron_Core_Tools::output_form ($input['subject']));
			}
			
			if (isset ($input['message']))
			{
				$page->set ('text_value', Neuron_Core_Tools::output_form ($input['message']));
			}
		}
		
		// Check for input
		if (isset ($input['target']) && isset ($input['subject']) && isset ($input['message']))
		{
			// Try to send the mail
			if (!$this->doSendMessage ($input['target'], $input['subject'], $input['message']))
			{
				$page->set ('error', $text->get ($this->getError ()));
			}
			else
			{
				$page->set ('sent', $text->get ('sent'));
			}
		}
		
		$page->set ('toInbox', $text->getClickTo ($text->get ('toInbox')));
		
		$page->set ('toInboxUrl', $this->getUrl (array ('view' => 'inbox'), $text->getClickTo ($text->get ('toInbox'))));
		
		$page->set ('write', $text->get ('write'));
		
		return $page->parse ('messages/write.tpl');
	}
	
	public function sendMessage ($player, $subject, $message)
	{
		$db = Neuron_Core_Database::__getInstance ();
		
		$msgId = $db->insert
		(
			'messages',
			array
			(
				'm_from' => $this->objPlayer->getId (),
				'm_target' => $player->getId (),
				'm_subject' => $subject,
				'm_text' => $message,
				'm_date' => 'NOW()'
			)
		);
		
		// Add the log
		$objLogs = Dolumar_Players_Logs::__getInstance ();
		
		$village = $this->objPlayer->getMainVillage ();
		
		if ($village)
		{			
			$objLogs->addLog 
			(
				$village, 
				'sendMsg', 
				array ($msgId, $player)
			);
		}
		
		$village = $player->getMainVillage ();
		
		if ($village)
		{
			$objLogs->addLog 
			(
				$village, 
				'receiveMsg', 
				array ($msgId, $this->objPlayer),
				true
			);
		}
		
		// Send "receive" notification
		$player->sendNotification ('received', 'messages', array ('player' => $this->objPlayer), $this->objPlayer);
	}
	
	private function doSendMessage ($target, $subject, $message)
	{
		// Search for player
		$player = Neuron_GameServer::getServer()->searchPlayer ($target, 0, 10, true);
		
		if (count ($player) == 1)
		{
			$this->sendMessage ($player[0], $subject, $message);
		
			return true;
		}
		else
		{
			$this->error = 'player_not_found';
			return false;
		}
	}
	
	/*
		This function generates the links.
	*/
	protected function getUrl ($data, $title, $confirm = null)
	{
		if (isset ($this->fGetUrl))
		{
			$f = $this->fGetUrl;
			return $f ($data, $title);
		}
	
		$data = str_replace ('"', "'", json_encode ($data));
		
		if (is_array ($title))
		{
			return $title[0].'<a href="javascript:void(0);" onclick="windowAction(this,'.$data.');">'.$title[1].'</a>'.$title[2];
		}
		elseif (isset ($confirm))
		{
			$confirm = str_replace ("'", "\'", $confirm);
			return '<a href="javascript:void(0);" onclick="confirmAction(this,'.$data.',\''.$confirm.'\');">'.$title.'</a>';
		}
		else
		{
			return '<a href="javascript:void(0);" onclick="windowAction(this,'.$data.');">'.$title.'</a>';
		}
	}
	
	private function getError ()
	{
		return $this->error;
	}
}
?>
