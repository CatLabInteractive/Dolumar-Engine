<?php
class Neuron_GameServer_Map_SpeedMovement
	extends Neuron_GameServer_Map_Movement
{
	public function __construct 
	(
		$startTime,
		$startSpeed, 
		$endSpeed, 
		Neuron_GameServer_Map_Location $l1, 
		Neuron_GameServer_Map_Location $l2
	)
	{
		$arguments = func_get_args ();

		// Calculate startTime, endTime, speed & acceleration from startSpeed & endSpeed
		$startTime = array_shift ($arguments);
		$startSpeed = array_shift ($arguments);
		$endSpeed = array_shift ($arguments);
		
		$this->setPoints ($arguments);

		$this->setSpeedAndAcceleration ($startTime, $startSpeed, $endSpeed);
	}

	private function setSpeedAndAcceleration ($startTime, $startSpeed, $endSpeed)
	{
		$this->setStartTime ($startTime);

		$distance = $this->getDistance ();

		if ($startSpeed === $endSpeed)
		{
			// Constant velocity
			$duration = $distance / $startSpeed;
			$acceleration = 0;
		}
		else
		{
			// Duration of the trip
			$duration = (2 * $distance) / (($endSpeed + $startSpeed));

			// Calculate acceleration
			$acceleration = (pow ($endSpeed, 2) - pow ($startSpeed, 2)) / (2 * $distance);
		}

		// Set end time
		$this->setEndTime ($startTime + $duration);

		// Set speed & acceleration (only initial is required)
		$this->setAcceleration ($startSpeed, $acceleration);
	}
}