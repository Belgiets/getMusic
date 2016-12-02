<?php
include 'config.php';
include 'includes/Curl.php';

global $config;

if (isset($_GET['code'])) {
    $params = [
        'app_id' => $config['deezer']['id'],
        'secret' => $config['deezer']['secret'],
        'code' => $_GET['code']
    ];

    $token_url = 'https://connect.deezer.com/oauth/access_token.php' . '?' . urldecode(http_build_query($params));

    $curl = new Curl($token_url);
    $token = $curl->sendRequest('string');

    if ($token) {
        include_once 'templates/getDeezerPlaylists.php';
    } else {
        print "Can't get token";
    }
}

