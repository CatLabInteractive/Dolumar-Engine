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

class Neuron_GameServer_Mappers_CachedChatMapper
	extends Neuron_GameServer_Mappers_ChatMapper
{
	private $objCache;
	private $sPrefix;

	/**
	* We need one instance per player.
	*/
	public static function getInstance ()
	{
		static $in;
		if (!isset ($in))
		{
			$in = new self ();
		}
		return $in;
	}

	/**
	* C-c-c-construct!
	*/
	protected function __construct ()
	{
		$this->objCache = Neuron_Core_Memcache::getInstance ();
		$this->sPrefix = DB_DATABASE.'_chat_';
	}

	public function getChannelID ($channel)
	{
		// Can be cached, but too lazy for now.
		return parent::getChannelID ($channel);
	}

	/**
	* Return the latest messages since $since.
	* $since should be the last received message ID.
	*/
	public function getMessages ($channelId, $limit = 20, $since = null, $before = null)
	{
		$profiler = Neuron_Profiler_Profiler::getInstance ();

		if (isset ($since))
		{
			// Here we do some magic to check if the last id has changed
			$cName = $this->sPrefix . 'lastmessage_' . $channelId;

			$profiler->message ('Checking ' . $cName . ' against ' . $since);

			if (
				$this->objCache->hasCache ($cName)
				&& $this->objCache->getCache ($cName) <= $since
			) {
				$profiler->message ('Not checking for new messages');
				return array ();
			}
		}

		$profiler->message ('Checking for new messages');
		return parent::getMessages ($channelId, $limit, $since, $before);
	}

	public function sendMessage ($channelId, $message, Neuron_GameServer_Player $player)
	{
		$profiler = Neuron_Profiler_Profiler::getInstance ();

		// Here we *must* update the last channelId message
		$id = parent::sendMessage ($channelId, $message, $player);

		$cName = $this->sPrefix . 'lastmessage_' . $channelId;

		$profiler->message ('Setting ' . $cName . ' to ' . $id);

		//echo 'setting ' . $cName . ' to ' . $id;
		$this->objCache->setCache ($cName, $id);

		return $id;
	}

	/**
	*CREATE TABLE  `n_privatechat_updates` (
	*`pu_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	*`pu_from` INT NOT NULL ,
	*`pu_to` INT NOT NULL ,
	*`pu_date` DATETIME NOT NULL
	*) ENGINE = INNODB
	*/
	public function addPrivateChatUpdate ($msgid, Neuron_GameServer_Player $from, Neuron_GameServer_Player $target)
	{
		$id = parent::addPrivateChatUpdate ($msgid, $from, $target);
		return $id;
	}

	/**
	* If $since is said, return all events since "since".
	* If $since is not said, return the single latest event.
	*
	* ($since is obviously the pu_id)
	*/
	public function getPrivateChatUpdates (Neuron_GameServer_Player $target, $since = null)
	{
		if (isset ($since))
		{

		}

		return parent::getPrivateChatUpdates ($target, $since);
	}
}