<?php

$returnUrl = Neuron_URLBuilder::getInstance()->getRawURL('oauth2/login/next', []);

$config = [
    'client_id' => OPENID_CONNECT_CLIENT_ID,
    'redirect_uri' => $returnUrl,
    'authorization_endpoint' => OPENID_CONNECT_AUTHORIZE_URL,
    'token_endpoint' => OPENID_CONNECT_TOKEN_URL,
    'authentication_info' => [
        'method' => 'client_secret_post',
        'params' => array(
            'client_secret' => OPENID_CONNECT_SECRET
        )
    ]
];

$flow = new \InoOicClient\Flow\Basic(['client_info' => $config ]);

$url = $flow->getAuthorizationRequestUri('openid email profile');
header('Location: ' . $url);
