<?php
$sAction = isset ($sInputs[1]) ? $sInputs[1] : null;

$sOutput = Neuron_Core_Tools::getInput ('_GET', 'output', 'varchar');
switch ($sAction)
{
	case 'minimap':
		require_once (self::SCRIPT_PATH.'image/minimap.php');
	break;
	
	case 'snapshot':
		require_once (self::SCRIPT_PATH.'image/snapshot.php');
	break;
	
	case 'playercard':
		$player_id = isset ($sInputs[2]) ? $sInputs[2] : null;
		require_once (self::SCRIPT_PATH.'image/snapshot.php');
	break;
	
	case 'world':
		require_once (self::SCRIPT_PATH.'image/world.php');
	break;
}
?>
