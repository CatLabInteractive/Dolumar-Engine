<?php
$sAction = isset ($sInputs[1]) ? $sInputs[1] : null;
$sOutput = Neuron_Core_Tools::getInput ('_GET', 'output', 'varchar');

switch ($sOutput)
{
	case 'print':
	break;
	
	case 'json':
	default:
		if ($sAction != 'plainmap')
		{
			header ('Content-type: application/json');
		}
	break;
}

switch ($sAction)
{
	case 'images':
		include (self::SCRIPT_PATH.'map/images.php');
	break;
	
	case 'region':
		include (self::SCRIPT_PATH.'map/jsonmap.php');
	break;
	
	case 'plainmap':
		include (self::SCRIPT_PATH.'map/plainmap.php');
	break;
	
	default:
	case 'objects':
		include (self::SCRIPT_PATH.'map/objects.php');
	break;
}
?>
