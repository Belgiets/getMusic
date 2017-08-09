<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Get music from Deezer and VK</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <?php include_once 'templates/header.php'; ?>
        <div class="container music-page">
<?php
include 'config.php';
include 'includes/Curl.php';

global $config;

if (empty($config['deezer']['token'])) {
    $params = [
        'app_id' => $config['deezer']['id'],
        'redirect_uri' => $config['deezer']['uri'],
        'perms' => $config['deezer']['perms'],
    ];

    print '<p><a href="' . $config['deezer']['oauth_url'] . '?' . urldecode(http_build_query($params)) . '">Get deezer</a></p>';
} else {
    $token = $config['deezer']['token'];

    include_once 'templates/getDeezerPlaylists.php';
}
?>
        </div>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
