<?php
global $config;

$params = urldecode(http_build_query([
    'output' => 'json',
    'access_token' => $config['deezer']['token']
]));

$user_url = 'https://api.deezer.com/user/me' . '?' . $params;

$user = (new Curl($user_url))->sendRequest();

$playlists_url = "http://api.deezer.com/user/{$user->id}/playlists";

$playlists = (new Curl($playlists_url))->sendRequest()->data;
?>
<div>
    <h2>Deezer user</h2>
    <p><strong>name: </strong><a href="<?php print $user->link ?>" target="_blank"><?php print $user->name; ?></a></p>
    <p><img src="<?php print $user->picture; ?>"/></p>
    <h3>Playlists</h3>
    <ul class="media-list">
        <?php
        foreach ($playlists as $playlist) {
            print "<li>";
            print "<div class='media-left'><a href='/getTracks.php?playlist_id={$playlist->id}'><img class='media-object' src='{$playlist->picture}' alt='{$playlist->title}'></a></div>";
            print "<div class='media-body'><a href='/getTracks.php?playlist_id={$playlist->id}'><h4 class='media-heading'>{$playlist->title}</h4></div>";
            print "</li>";
        }
        ?>
    </ul>
</div>

