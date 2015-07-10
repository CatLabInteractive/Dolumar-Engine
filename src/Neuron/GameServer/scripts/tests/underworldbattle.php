<?php

$army1 = Dolumar_Underworld_Mappers_ArmyMapper::getFromId (1);
$army2 = Dolumar_Underworld_Mappers_ArmyMapper::getFromId (3);

$battle = $army1->attack ($army2);

$report = $battle->getReport ();

$units = $report->getUnits ();

echo '<h2>Units</h2>';
foreach ($units as $k => $v)
{
	echo '<h3>' . $k . '</h3>';
	
	echo '<table>';
	
	echo '<tr>';
	echo '<th>Unit</th>';
	echo '<th>Amount</th>';
	echo '<th>Died</th>';
	echo '</tr>';
	
	foreach ($v as $unit)
	{
		echo '<tr>';
		
		echo '<td>' . $unit['unit']->getDisplayName () . '</td>';
		echo '<td>' . $unit['amount'] . '</td>';
		echo '<td>' . $unit['died'] . '</td>';

		echo '</tr>';
	}
	echo '</table>';
}

echo '<h2>Fight log</h2>';
echo $report->getFightLog (0, true);

?>