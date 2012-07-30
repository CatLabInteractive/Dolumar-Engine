<?php
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