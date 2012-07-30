<?php

echo "<p>Market transaction test script</p>";
echo "<pre>";

$resources_to_send = array
(
	'earth' => mt_rand (0, 5),
	'stone' => mt_rand (0, 5),
	'blood' => mt_rand (0, 5),
	'blaat' => mt_rand (0, 5)
);

//$resources_to_send = array ();

echo "We want to send:\n";
print_r ($resources_to_send);
echo "\n\n";

$out = Dolumar_Buildings_Market::splitInTransactions ($resources_to_send, 2);

echo "So we are sending:\n";
print_r ($out);
echo "</pre>";

?>
