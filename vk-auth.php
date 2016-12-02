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
    $token = $curl->sendRequest();

    if ($token) {
        $search_params = [
            'q' => 'Сплин',
            'auto_complete' => 1,
            'count' => 10
        ];

        $search_url = 'https://api.vk.com/method/audio.search?' . urldecode(http_build_query($search_params)) . '&access_token=' . $token->access_token . '&v=5.60';
        $curl = new Curl($search_url);
        $search_result = $curl->sendRequest();

        if ($search_result) {
            $items = $search_result->response->items;
            $result = FALSE;

            if (is_array($items)) {
                print "<div style='padding: 50px 0'>";
                    foreach ($items as $item) {
                        $download = new Curl($item->url);
                        $result = $download->saveFile("file-{$item->artist}-{$item->title}.mp3");

                        print "<p>artist - {$item->artist}</p>";
                        print "<p>title - {$item->title}</p>";
                        print "<a href='{$item->url}' download>download</a>";
                    }
                print "</div>";
            }
        }
    } else {
        print "Can't get token";
    }
}

