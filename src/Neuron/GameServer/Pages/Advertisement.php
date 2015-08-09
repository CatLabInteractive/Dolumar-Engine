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

function getAge( $p_strDate ) {
    list($Y,$m,$d)    = explode("-",$p_strDate);
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
}

class Neuron_GameServer_Pages_Advertisement extends Neuron_GameServer_Pages_Page
{
	public function getHTML ()
	{
		$container = isset ($_SESSION['opensocial_container']) ? $_SESSION['opensocial_container'] : null;
		
		$page = new Neuron_Core_Template ();
		
		$player = Neuron_GameServer::getPlayer ();
		
		if ($player)
		{
			$page->set ('plid', $player->getId ());
		}
		else
		{
			$page->set ('plid', '');
		}
		
		if (isset ($_SESSION['birthday']))
		{
			$page->set ('birthday', date ('Y-m-d', $_SESSION['birthday']));
			$page->set ('age', getAge (date ('Y-m-d', $_SESSION['birthday'])));
		}	
		
		if (isset ($_SESSION['gender']))
			$page->set ('gender', $_SESSION['gender']);
		
		$page->set ('container', $container);
		
		//print_r ($_SESSION);
		
		return $page->parse ('neuron/advertisement/loading.phpt');
	}
}
?>
