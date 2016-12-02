<?php
include 'config.php';

global $config;

$params = [
    'client_id'     => $config['vk']['id'],
    'redirect_uri'  => $config['vk']['uri'],
    'response_type' => 'code'
];

echo $link = '<p><a href="' . $config['vk']['oauth_url'] . '?' . urldecode(http_build_query($params)) . '">Get music</a></p>';

