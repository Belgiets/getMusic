<?php
include 'config.php';
include 'includes/Curl.php';

global $config;

if (isset($_GET['code'])) {
    $params = [
        'client_id' => $config['vk']['id'],
        'client_secret' => $config['vk']['secret'],
        'code' => $_GET['code'],
        'redirect_uri' => $config['vk']['uri']
    ];

    $token_url = 'https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params));

    $curl = new Curl($token_url);
    $response = $curl->sendRequest();

    if (isset($response->access_token)) {
        $token = $response->access_token;

        include_once 'templates/getMusic.php';
    }
}

