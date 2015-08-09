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

class Neuron_GameServer_Mappers_BlockingChatMapper
	extends Neuron_GameServer_Mappers_CachedChatMapper
{
	const LEAVE_TIMEOUT = 60;

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

	protected function __construct ()
	{
		parent::__construct ();
	}

	/**
	* This method will be called whenever a user opens
	* a new window. This is basically to decide if
	* the mapper should "unblock" when receiving a
	* message or not. 
	* Information will probalby be stored in session.
	* If the user does not request new messages for
	* a "timeout" period of time, we can "unregister"
	*/
	public function joinChannel (Neuron_GameServer_Player $player, $channelId)
	{
		// Does not when not blocking.
	}

	/**
	* For now, probably not used.
	*/
	public function leaveChannel (Neuron_GameServer_Player $player, $channelId)
	{
		// Does not when not blocking.
	}

}