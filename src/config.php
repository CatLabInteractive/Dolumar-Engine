<?php

if (!defined ('EMAIL_FROM') || !EMAIL_FROM) {
	define ('EMAIL_FROM', 'support@catlab.eu');
}

if (!defined ('EMAIL_FROM_NAME') || !EMAIL_FROM_NAME) {
	define ('EMAIL_FROM_NAME', 'Dolumar');
}

/**
 * @return string[]
 */
if (!function_exists('getAdminUserEmailAddresses')) {
	function getAdminUserEmailAddresses()
	{
		return array(
			'thijs@catlab.be' => 9,
			'daedelson@gmail.com' => 9,
			'ken@catlab.be' => 4
		);
	}
}

/*
define ('EMAIL_SMTP_SERVER', 'localhost');
define ('EMAIL_SMTP_PORT', 587);
define ('EMAIL_SMTP_USERNAME', 'abc');
define ('EMAIL_SMTP_PASSWORD', 'abc');
define ('EMAIL_SMTP_SECURITY', 'tls');
*/