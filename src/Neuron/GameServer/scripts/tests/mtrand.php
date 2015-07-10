<?php

$chanse = isset ($_GET['probability']) ? $_GET['probability'] : 50;

$success = 0;
$failed = 0;

echo '<p>Probability: '.$chanse.'</p>';

echo '<pre>';
for ($i = 0; $i < 1000; $i ++)
{
	$rand = mt_rand (0, 99);
	
	if ($rand < 10)
	{
		echo ' ';
	}
	echo $rand . ' : ';
	
	if ($rand < $chanse)
	{
		$success ++;
		echo '<span style="color: green;">Success</span>';
	}
	else
	{
		$failed ++;
		echo '<span style="color: red;">Failure</span>';
	}
	
	echo "\n";
}
echo '</pre>';

echo '<p>Success: '.$success.'x<br />Failure: '.$failed.'x</p>';
echo '<p>Outcome: '.(($success / ($success + $failed))*100).'%</p>';

?>
