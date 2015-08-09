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

/*
	This class constructs a notification and sends it to the server.
*/
class BrowserGamesHub_Invitation extends BBGS_Invite
{
	public function __construct ($senderMessage, $receiverMessage, $maxPerInterval = 1, $maxReceiverPerInterval = 0, $interval = 604800, $language = 'en')
	{
		parent::__construct ($senderMessage, $receiverMessage, $maxPerInterval, $maxReceiverPerInterval, $interval, $language);
		
		$this->setPrivateKey (file_get_contents (CATLAB_BASEPATH . 'php/Neuron/GameServer/certificates/credits_private.cert'));
		//$this->setPrivateKey ('bla bla bla');
	}
}