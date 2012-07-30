<?php

$report = Neuron_Core_Tools::getInput ('_GET', 'report', 'int');

if ($report)
{
	$report = new Dolumar_Battle_Report ($report, null, true);
	echo $report;
}

?>
