<?php

$filepaths = array
(
	BASE_PATH . 'dolumar/php/',
	BASE_PATH . 'gameserver/php/'
);

function includeAll ($filepath)
{
	echo "Scaning " . $filepath . "\n";
	$files = scandir ($filepath);

	foreach ($files as $v)
	{
		$firstletter = substr ($v, 0, 1);

		echo "- " . $v . "\n";

		if ($v === '.' || $v === '..' || $firstletter === '.' || $v === 'Auth' || $v === 'BBGS')
		{

		}

		else if (is_dir ($filepath . $v))
		{
			includeAll ($filepath . $v . '/');
		}

		elseif (is_file ($filepath . $v) && strtoupper ($firstletter) === $firstletter)
		{
			require_once ($filepath . $v);
		}
	}
}

echo '<pre>';
foreach ($filepaths as $v)
{
	includeAll ($v);
}
echo '</pre>';