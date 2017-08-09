<?php
include 'config.php';
include 'includes/Curl.php';

global $config;

function getExecutionTime($time_start) {
    $seconds = (microtime(true) - $time_start);

    return ($seconds >= 60) ? round($seconds/60, 2) . ' minute(s)' : round($seconds, 1) . ' seconds';
}

$params = urldecode(http_build_query([
    'output' => 'json',
    'access_token' => $config['deezer']['token']
]));

$time_start = microtime(true);

if (empty($_POST['songs']) && isset($_GET['playlist_id'])) {
    $tracks = [];

    $tracks_url = "http://api.deezer.com/playlist/" . $_GET['playlist_id'];

    $deezer_playlist = (new Curl($tracks_url))->sendRequest();
    $deezer_tracks = $deezer_playlist->tracks->data;
} elseif (isset($_POST['songs'])) {
    $songs = [];

    foreach (array_filter($_POST['songs']) as $song_name => $song_link) {
        $download = new Curl($song_link);
        $download->setFileName($song_name);
        $result = $download->saveFile("music/$song_name.mp3");

        $songs[$song_name] = ($result) ? $song_name : 'error';
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
    if (empty($_POST['songs']) && isset($_GET['playlist_id'])) {
        ?>
        <h3><?php if (!empty($deezer_playlist)) print $deezer_playlist->title; ?><span class="music-count"> (qty - <?php if (!empty($deezer_tracks)) print count($deezer_tracks); ?>)</span></h3>
        <form method="post">
            <div class="row">
                <div class="col-xs-12 col-sm-10">
                    <ul class="media-list">
                        <?php
                        if (!empty($deezer_tracks) && is_array($deezer_tracks)) {
                            foreach ($deezer_tracks as $deezer_track) {
                                $nameFull = "{$deezer_track->artist->name} - {$deezer_track->title}";
                                $download_url = '';

                                $url = "http://get-tune.cc/search/f/";
                                $url .= str_replace(' ', '+', $deezer_track->artist->name);
                                $url .= '+' . str_replace(' ', '+', $deezer_track->title);

                                $ch = curl_init($url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                $result = curl_exec($ch);
                                curl_close($ch);

                                if ($result) {
                                    $dom = new DOMDocument;

                                    // see: http://stackoverflow.com/questions/9149180/domdocumentloadhtml-error
                                    // for info on why libxml_use_internal_errors is being used.
                                    libxml_use_internal_errors(true);
                                    $dom->loadHTML($result);
                                    libxml_use_internal_errors(false);

                                    $lists = $dom->getElementsByTagName('ul');
                                    foreach ($lists as $list) {
                                        $list_class = $list->getAttribute('class');

                                        if ('playlist' === $list_class) {
                                            $list_elements = $list->getElementsByTagName('li');

                                            foreach ($list_elements as $li) {
                                                $spans = $li->getElementsByTagName('span');

                                                foreach ($spans as $span) {
                                                    $duration = explode(':', $span->nodeValue);
                                                    if ($duration[0] >= 2) {
                                                        $links = $li->getElementsByTagName('a');

                                                        foreach ($links as $link) {
                                                            $link_classes = explode(' ', $link->getAttribute('class'));

                                                            if (in_array('playlist-btn-down', $link_classes)) {
                                                                $download_url = $link->getAttribute('href');

                                                                if (!empty($download_url)) {
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if (!empty($download_url)) {
                                                        break;
                                                    }
                                                }

                                                if (!empty($download_url)) {
                                                    break;
                                                }
                                            }
                                        }

                                        if (!empty($download_url)) {
                                            break;
                                        }
                                    }
                                }

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
                                            <?php
                                            if (!empty($download_url)) {
                                                print "<input type='hidden' name='songs[$nameFull]' value='$download_url'>";
                                                print "<a href='{$download_url}' download>Download</a>";
                                            }
                                            ?>
<!--                                            <input type="checkbox" name="songs[--><?php //print $name; ?><!--]" checked>-->
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
<!--                        <a class="btn btn-default music-uncheck">Uncheck all</a>-->
<!--                        <a class="btn btn-default music-check">Check all</a>-->
                        <div>List rendered at <?php print getExecutionTime($time_start); ?></div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    } else {
        ?>
        <h3>Download finished</h3>
        <div class="col-xs-12 col-sm-10">
            <?php
            $error_count = array_count_values($songs)['error'];

            if (!empty($error_count)) {
                ?>
                <p>Interrupted downloads:</p>
                <ul>
                    <?php
                    foreach ($songs as $song_name => $song_status) {
                        if ('error' === $song_status) print "<li>$song_name</li>";
                    }
                    ?>
                </ul>
                <?php
            }
            ?>
        </div>
        <div class="col-xs-12 col-sm-2">
            <div class="music-buttons">
                <div>List downloaded at <?php print getExecutionTime($time_start); ?></div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

