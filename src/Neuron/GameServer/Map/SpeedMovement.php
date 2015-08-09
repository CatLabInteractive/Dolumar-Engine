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