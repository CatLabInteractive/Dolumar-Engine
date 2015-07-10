<?php
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