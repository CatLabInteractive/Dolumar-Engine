<?php
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
