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

class Neuron_GameServer_Pages_Admin_Page extends Neuron_GameServer_Pages_Page
{
	public function __construct ()
	{
		Neuron_URLBuilder::getInstance ()->setOpenCallback (array ($this, '_getUrlCallback'));
		Neuron_URLBuilder::getInstance ()->setUpdateCallback (array ($this, '_getUrlCallback'));
	}
	
	public function _getUrlCallback ($module, $display, $data)
	{
		$module = strtolower ($module);
		return '<a href="'.$this->getUrl ($module, $data).'">'.$display.'</a>';
	}

	/*
		Return the body, without page specific content
	*/	
	public function getHTML ()
	{
		$page = new Neuron_Core_Template ();
		$page->set ('body', $this->getOuterBody ());
		$page->set ('stylesheet', 'admin');
		
		$page->set ('static_client_url', BASE_URL . 'gameserver/');
		
		return $page->parse ('pages/index.phpt');
	}
	
	public function getOuterBody ()
	{
		$page = new Neuron_Core_Template ();
		$page->set ('body', $this->getBody ());
		
		$page->set ('messages', $this->getUrl ('messages'));
		$page->set ('onlineurl', ABSOLUTE_URL.'test/online/');
		$page->set ('searchplayers', $this->getUrl ('searchplayers'));
		
		// Add navigations
		$player = Neuron_GameServer::getPlayer ();
		
		if ($player->isModerator ())
		{
			$page->set ('multiaccounts', $this->getUrl ('multis'));
		}
		
		if ($player->isAdmin ())
		{
			$page->set ('execute', $this->getUrl ('execute'));
		}
		
		if ($player->isDeveloper ())
		{
			$page->set ('bonusbuilding', $this->getUrl ('bonusbuilding'));
		}
		
		$page->set ('isModerator', $player->isModerator ());
		$page->set ('isAdmin', $player->isAdmin ());
		$page->set ('isDeveloper', $player->isDeveloper ());
		
		return $page->parse ('pages/admin/page.phpt');
	}
	
	public static function getUrl ($sUrl, $sArrs = null, $sBase = 'admin/')
	{
		return parent::getUrl ($sUrl, $sArrs, $sBase);
	}

	public function getBody ()
	{
		$myself = Neuron_GameServer::getPlayer ();
		return '<p>Welcome, '.$myself->getName ().'.</p>';
	}
	
	public function addModeratorAction ($sAction, $mParams, $reason, Neuron_GameServer_Player $player, $isDone = false)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$login = Neuron_Core_Login::getInstance ();
		$userid = intval ($login->getUserId ());
		
		$isDone = $isDone ? true : false;
		
		$processed = $isDone ? 1 : 0;
		$executed = $isDone ? 1 : 0;
		
		$db->query
		("
			INSERT INTO
				n_mod_actions
			SET
				ma_action = '".$db->escape ($sAction)."',
				ma_data = '".$db->escape (json_encode ($mParams))."',
				ma_plid = $userid,
				ma_date = NOW(),
				ma_reason = '".$db->escape ($reason)."',
				ma_target = '{$player->getId ()}',
				ma_processed = {$processed},
				ma_executed = {$executed}
		");
	}
}
?>
