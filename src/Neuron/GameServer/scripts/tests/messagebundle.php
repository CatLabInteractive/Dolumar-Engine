<?php
echo '<h2>Loading message bundle.</h2>';

$_SESSION['opensocial_messagebundle'] = ABSOLUTE_URL.'api/messagebundle/';

$text = Neuron_Core_Text::getInstance ();

echo $text->get ('about', 'serverOffline', 'account', 'Text not found.');
?>
