<?php
include 'config.php';
include 'includes/Curl.php';

global $config;

$params = urldecode(http_build_query([
    'output' => 'json',
    'access_token' => $config['deezer']['token']
]));

if (isset($_GET['playlist_id'])) {
    $tracks = [];

    $tracks_url = "http://api.deezer.com/playlist/" . $_GET['playlist_id'];

    $deezer_playlist = (new Curl($tracks_url))->sendRequest();
    $deezer_tracks = $deezer_playlist->tracks->data;
} elseif (isset($_GET['songs'])) {
    $songs = array_keys($_GET['songs'], "on");
    $qty = 0;

    foreach ($songs as $song) {
        $search_params = [
            'q' => urlencode($song),
            'auto_complete' => 1,
            'count' => 10,
            'access_token' => $config['vk']['token'],
            'v' => '5.60'
        ];

        if (!empty($config['vk']['token'])) {
            $search_url = 'https://api.vk.com/method/audio.search?' . urldecode(http_build_query($search_params));
            $search_result = (new Curl($search_url))->sendRequest();

            if ($search_result) {
                $items = $search_result->response->items;
                $result = FALSE;

                if (is_array($items) && count($items)) {
                    foreach ($items as $item) {
                        $result = (new Curl($item->url))->saveFile("music/{$item->artist}-{$item->title}.mp3");
                        if ($result) {
                            $qty ++;
                            break;
                        }
                    }
                }
            }
        }
    }
}
?>

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
    <h1>Playlist tracks</h1>
    <?php
    if (isset($_GET['playlist_id'])) {
        ?>
        <h3><?php if (!empty($deezer_playlist)) print $deezer_playlist->title; ?><span class="music-count"> (qty - <?php if (!empty($deezer_tracks)) print count($deezer_tracks); ?>)</span></h3>
        <form>
            <div class="row">
                <div class="col-xs-12 col-sm-10">
                    <ul class="media-list">
                        <?php

                        if (!empty($deezer_tracks) && is_array($deezer_tracks)) {
                            foreach ($deezer_tracks as $deezer_track) {
                                $nameFull = "{$deezer_track->artist->name} - {$deezer_track->title}";
                                $name = substr($nameFull, 0, 30)
                                ?>
                                <li>
                                    <div class="row">
                                        <div class="col-xs-11">
                                            <div class='media-left'><img class='media-object' src='<?php print $deezer_track->album->cover_small; ?>' alt='<?php print $deezer_track->title; ?>'></a></div>
                                            <div class='media-body'>
                                                <h4 class='media-heading'><?php print $nameFull; ?></h4>
                                                <audio controls><source src='<?php print $deezer_track->preview; ?>' type='audio/mpeg'>Your browser does not support the audio element.</audio>
                                            </div>
                                        </div>
                                        <div class="col-xs-1">
                                            <input type="checkbox" name="songs[<?php print $name; ?>]" checked>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <div class="music-buttons">
                        <input class="btn btn-default music-download" type="submit" value="Download">
                        <a class="btn btn-default music-uncheck">Uncheck all</a>
                        <a class="btn btn-default music-check">Check all</a>
                    </div>
                </div>
            </div>
        </form>
        <?php
    } else {
        ?>
        <h3>Downloaded <?php print $qty; ?> songs</h3>
        <a href="/music">Go to music</a>
        <?php
    }
    ?>
</div>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

