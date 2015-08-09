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

class Neuron_GameServer_Windows_Guide extends Neuron_GameServer_Windows_Window
{
	const TEMPLATE_PATH = 'guide/';

	public function setSettings ()
	{

		// Window settings
		$this->setNoBorder ();
		//$this->setSize ('100%', '41px');
		//$this->setPosition ('auto', 'auto', '0px', '0px');
		$this->setFixed ();
		$this->setZ (10000);
		$this->setClass ('guide');
		
		$this->setType ('panel');
		
		$this->setAllowOnlyOnce ();
	
	}
	
	private function getRightMessage ($page)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$text = Neuron_Core_Text::getInstance ();
		$player = Neuron_GameServer::getPlayer ();
		
		if (!$player)
		{
			//return '<p>Welcome! Please login first!</p>';
			$text = Neuron_Core_Text::getInstance ();
			$out = array 
			(
				'html' => Neuron_Core_Tools::output_text
				(
					$text->getTemplate (self::TEMPLATE_PATH . 'login')
				),
				'class' => 'happy',
				'highlight' => null,
				'record' => 0
			);
		}
		
		else
		{
			$data = array ();
		
			if (!isset ($page))
			{
				// Get first unread message
				$data = $db->query
				("
					SELECT
						COUNT(pg_id) AS aantal
					FROM
						n_players_guide
					WHERE
						plid = {$player->getId ()} AND
						pg_read = '0'
				");
			
				$page = count ($data) == 1 ? $data[0]['aantal'] - 1 : 0;
				$page = max (0, $page);
			}
		
			$page = intval ($page);
		
			$data = $db->query
			("
				SELECT
					*
				FROM
					n_players_guide
				WHERE
					plid = {$player->getId ()}
				ORDER BY
					pg_id DESC
				LIMIT {$page}, 1
			");
		
			if (count ($data) > 0)
			{
				$out = $this->getMessage ($data[0], $page);
			}
			else
			{
				$out = array
				(
					'html' => '<p>'.$text->get ('noadvice', 'guide', 'guide').'</p>',
					'class' => '',
					'highlight' => null,
					'record' => 0
				);
			}
		}
		
		$out['page'] = $page;
		
		return $out;
	}
	
	private function getMessage ($input, $page)
	{
		$player = Neuron_GameServer::getPlayer ();
	
		if (!$input['pg_read'])
		{
			$db = Neuron_DB_Database::getInstance ();
			
			$db->query
			("
				UPDATE
					n_players_guide
				SET
					pg_read = '1'
				WHERE
					pg_id = {$input['pg_id']}
			");
		}
	
		$qdata = Neuron_GameServer_LogSerializer::decode ($input['pg_data']);
		
		$data = array ();
		foreach ($qdata as $k => $v)
		{
			$data[$k] = $v->getName ();
		}
		
		// Add some extra fields
		$data['player'] = $player->getName ();
		
		$text = Neuron_Core_Text::getInstance ();
		
		$txt = $text->getTemplate (self::TEMPLATE_PATH . $input['pg_template'], $data);
		
		return array
		(
			'html' => Neuron_Core_Tools::output_text ($txt),
			'class' => $this->getClassname ($input['pg_character'], $input['pg_mood']),
			'highlight' => $input['pg_highlight'],
			'record' => $input['pg_id']
		);
	}
	
	protected function getClassname ($character, $mood)
	{
		return $character . ' ' . $mood;
	}
	
	private function hasUnreadMessages (Neuron_GameServer_Player $player)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$chk = $db->query
		("
			SELECT
				COUNT(pg_id) AS aantal
			FROM
				n_players_guide
			WHERE
				plid = {$player->getId ()} AND
				pg_read = '0'
		");
		
		return count ($chk) > 0 && $chk[0]['aantal'] > 0;
	}
	
	private function hasPrevious (Neuron_GameServer_Player $player, $page)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$cnt = $db->query
		("
			SELECT
				COUNT(pg_id) AS aantal
			FROM
				n_players_guide
			WHERE
				plid = {$player->getId ()}
		");
		
		return (count ($cnt) == 1 && $cnt[0]['aantal'] - 1 > $page);
	}
	
	public function getContent ()
	{	
		$guide_img = IMAGE_URL . 'characters/guide.png';
		$guide_icon_img = IMAGE_URL . 'characters/guide-icon.png';
		
		$text = Neuron_Core_Text::getInstance ();
		$player = Neuron_GameServer::getPlayer ();
		
		$input = $this->getInputData ();
		$page = isset ($input['page']) ? $input['page'] : null;
		
		if (isset ($input['action']))
		{
			switch ($input['action'])
			{
				case 'show':
					$this->updateRequestData (array ('display' => 'show'));
				break;
				
				case 'hide':
					$this->updateRequestData (array ('display' => 'hide'));
				break;
			}
		}
		
		$data = $this->getRequestData ();
		
		$openup = false;
		
		if 
		(
			(isset ($data['display']) && $data['display'] == 'show') || 
			($openup = $player && $this->hasUnreadMessages ($player))
		)
		{		
			$txt = $this->getRightMessage ($page);
			$page = $txt['page'];
			
			$lastrec = isset ($data['record']) ? $data['record'] : 0;
			
			if (!empty ($txt['highlight']) && $txt['record'] != $lastrec)
			{
				$this->highlight ($txt['highlight']);
			}
			
			$this->updateRequestData (array ('display' => 'show', 'record' => $txt['record']));
			
			$hide_url = Neuron_URLBuilder::getInstance ()->getUpdateUrl 
			(
				'Guide', 
				$text->get ('hide', 'navigation', 'guide'), 
				array 
				(
					'action' => 'hide'
				),
				$text->get ('hide', 'navigation', 'guide')
			);
		
			$next_url = false;
			$previous_url = false;
		
			if ($page > 0)
			{
				$next_url = Neuron_URLBuilder::getInstance ()->getUpdateUrl 
				(
					'Guide', 
					$text->get ('next', 'navigation', 'guide'), 
					array 
					(
						'page' => $page - 1
					),
					$text->get ('shownext', 'navigation', 'guide')
				);
			}
		
			if ($player && $this->hasPrevious ($player, $page))
			{
				$previous_url = Neuron_URLBuilder::getInstance ()->getUpdateUrl 
				(
					'Guide', 
					$text->get ('previous', 'navigation', 'guide'), 
					array 
					(
						'page' => $page + 1
					),
					$text->get ('showprevious', 'navigation', 'guide')
				);
			}
		
			$out = '<blockquote class="bubble triangle-right right">'.
				$txt['html'] .
				'<ul class="navigation">';
			
			if ($previous_url)
			{
				$out .= '<li>'.$previous_url.'</li>';
			}
			
			if ($next_url)
			{
				$out .= '<li>'.$next_url.'</li>';
			}
			
			if ($page < 1)
			{
				$out .= '<li>'.$hide_url.'</li>';
			}
			
			$out .= '</ul>'.
				'</blockquote><div class="avatar '.$txt['class'].'"></div>';
			
			return $out;
		}
		else
		{
			$url = Neuron_URLBuilder::getInstance ()->getUpdateUrl 
			(
				'Guide', 
				'<span>'.$text->get ('callguide', 'navigation', 'guide').'</span>', 
				array 
				(
					'action' => 'show'
				),
				$text->get ('callguide', 'navigation', 'guide')
			);	
	
			return '<div class="icon">'.$url.'</div>';
		}
	}
}
?>
