<?php

require 'bootstrap.php';

$flow = getOpenIDConnectFlow();
$url = $flow->getAuthorizationRequestUri('openid email profile');
header('Location: ' . $url);

