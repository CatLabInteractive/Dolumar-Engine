<?php

require 'bootstrap.php';

function handleOpenIDConnectAuthentication() {
    $flow = getOpenIDConnectFlow();
    $authorizationCode = $flow->getAuthorizationCode();
    $accessToken = $flow->getAccessToken($authorizationCode);
    $userInfo = $flow->getUserInfo($accessToken);

    $id = $userInfo['id'];
    $email = $userInfo['email'];
    $username = $userInfo['username'];
    $emailVerified = $userInfo['verified_email'];

    if (!$emailVerified) {
        $loginUrl = Neuron_URLBuilder::getInstance()->getRawURL('oauth2/login', []);

        echo 'Please verify your email address before logging in and click <a href="'.$loginUrl.'">here</a> to try again.';
        return;
    }

    $openIdProfileUrl = OPENID_CONNECT_PROFILE_URL_PREFIX . $id;

    // We're going to follow the openid flow now.
    $_SESSION['openid_nickname'] = $username;
    $_SESSION['dolumar_openid_identity'] = $openIdProfileUrl;
    $_SESSION['dolumar_openid_email'] = $email;

    //$loginUrl = Neuron_URLBuilder::getInstance()->getRawURL('oauth2/login', []);

    $db = Neuron_Core_Database::__getInstance ();

    // Check if we need to convert an old account to a new account (based on email address)
    $acc = $db->select
    (
        'n_auth_openid',
        array ('user_id'),
        "openid_url = '".$db->escape ($openIdProfileUrl)."'"
    );

    if (count ($acc) == 0) {
        // Look for player with the same email address
        $player = Neuron_GameServer_Mappers_PlayerMapper::getFromEmail($email);
        if ($player) {
            $db->insert('n_auth_openid', [
                'openid_url' => $openIdProfileUrl,
                'user_id' => $player->getId()
            ]);
        }
    }

    $openid = new Neuron_Auth_OpenID ();
    $openid->handleAuthentication($openIdProfileUrl, $email);
}

handleOpenIDConnectAuthentication();
