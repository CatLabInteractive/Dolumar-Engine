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

class Neuron_GameServer_Pages_Admin_Login extends Neuron_GameServer_Pages_Admin_Page
{
	private $login;

	public function __construct (Neuron_Core_Login $login)
	{
		$this->login = $login;
	}
	
	public function getOuterBody ()
	{
		$username = Neuron_Core_Tools::getInput ('_POST', 'username', 'varchar');
		$password = Neuron_Core_Tools::getInput ('_POST', 'password', 'varchar');
	
		$page = new Neuron_Core_Template ();
	
		if ($username && $password)
		{
			$chk = $this->login->login ($username, $password, false);
			if ($chk)
			{
				$url = $this->getUrl ('index');
				header ('Location: '.$url);
				return '<p>Welcome! Click <a href="'.$url.'">here</a> to continue.</p>';
			}
			else
			{
				$page->set ('error', $this->login->getError ());
			}
		}
		
		$page->set ('action', '');
		return $page->parse ('pages/login.phpt');
	}
}
?>
