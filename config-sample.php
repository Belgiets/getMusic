<?php
/**
 * This is config sample file
 * just copy it to config.php and fill the config array
*/

//https://developers.deezer.com/api
$config = [
    'deezer' => [
        'token'  => 'access token',
        'id'     => 'deezer app id',
        'secret' => 'deezer token',
        'uri'       => 'auth uri',
        'oauth_url' => 'https://connect.deezer.com/oauth/auth.php',
        'perms' => 'offline_access'
    ],
];