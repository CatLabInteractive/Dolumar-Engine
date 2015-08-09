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

class Neuron_GameServer_Models_ChatMessage
{
	private $id;
	private $message;
	private $timestamp;
	private $plid;
	private $isRead = true;

	public function setId ($id)
	{
		$this->id = $id;
	}

	public function setMessage ($message)
	{
		$this->message = $message;
	}

	public function setTimestamp ($timestamp)
	{
		$this->timestamp = $timestamp;
	}

	public function setPlid ($plid)
	{
		$this->plid = $plid;
	}

	public function getId ()
	{
		return $this->id;
	}

	public function getMessage ($maxchars = null)
	{
		if (isset ($maxchars))
		{
			return substr ($this->message, 0, $maxchars);
		}

		return $this->message;
	}

	public function getTimestamp ()
	{
		return $this->timestamp;
	}

	public function getDisplayDate ()
	{
		return date ('d/m/Y H:i', $this->getTimestamp ());
	}

	public function getPlayer ()
	{
		return Neuron_GameServer::getPlayer ($this->plid);
	}

	public function setRead ($read)
	{
		$this->isRead = $read;
	}

	public function isRead ()
	{
		return $this->isRead;
	}

}