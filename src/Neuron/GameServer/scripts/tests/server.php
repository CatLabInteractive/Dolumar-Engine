<html>

<head>
	<style type="text/css">
		table
		{
			border-collapse: collapse;
		}
	
		td
		{
			border: 1px solid gray;
			padding: 5px;
		}
		
		td.message
		{
			width: 500px;
		}
		
		td.failed
		{
			color: red;
		}
		
		td.passed
		{
			color: green;
		}
	</style>
</head>

<?php

echo '<h2>Server check</h2>';

function checkFunction ($sMessage, $sFunction)
{
	echo '<tr>';
	
	echo '<td class="message">'.$sMessage.'</td>';

	if (function_exists ($sFunction))
	{
		echo '<td class="status passed">passed</td>';
	}
	else
	{
		echo '<td class="status failed">failed</td>';
	}
	
	echo '</tr>';
}

echo '<table>';

checkFunction ('Checking cURL extension', 'curl_init');
checkFunction ('Checking image processing and GD', 'imagecreate');
checkFunction ('Checking json_encode', 'json_encode');

echo '</table>';

?>
