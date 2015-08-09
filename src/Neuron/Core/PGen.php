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

class Neuron_Core_PGen {

        //
        // PRIVATE CLASS VARIABLES
        //
        private $_start_time;
        private $_stop_time;
        private $_gen_time;

        //
        // USER DEFINED VARIABLES
        //
        public $round_to;

	public static function __getInstance ()
	{
	
		static $in;
		
		if (!isset ($in))
		{
		
			$in = new Neuron_Core_PGen ();
		
		}
		
		return $in;
	
	}

        //
        // FIGURE OUT THE TIME AT THE BEGINNING OF THE PAGE
        //
        public function start ()
        {

            $microstart = explode(' ',microtime());
            $this->_start_time = $microstart[0] + $microstart[1];

        }

        //
        // FIGURE OUT THE TIME AT THE END OF THE PAGE
        //
        public function stop ()
        {

            $microstop = explode(' ',microtime());
            $this->_stop_time = $microstop[0] + $microstop[1];

        }

        //
        // CALCULATE THE DIFFERENCE BETWEEN THE BEGINNNG AND THE END AND RETURN THE VALUE
        //
        public function gen ($round_to) 
        {

		$this->_gen_time = round ($this->_stop_time - $this->_start_time, $round_to);
		return $this->_gen_time; 
		
        }
}
?>