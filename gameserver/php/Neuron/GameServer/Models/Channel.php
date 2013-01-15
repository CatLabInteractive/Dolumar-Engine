<?php
class Neuron_GameServer_Models_Channel
{
	private $subscription = array ();
	private $updates = array ();

	public function subscribe ($channel)
	{
		$this->subscription[] = $channel;
	}

	public function getSubscriptions ()
	{
		return $this->subscription;
	}

	public function refresh ($channel)
	{
		$this->updates[] = array 
		(
			'channel' => $channel,
			'action' => 'refresh'
		);
	}

	public function getUpdates ()
	{
		return $this->updates;
	}
}