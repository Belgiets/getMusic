<?php

class Curl {
    private $curl;
    private $rt;
    private $url;

    public function __construct($url, $rt = 1) {
        $this->curl = curl_init();
        $this->rt = $rt;
        $this->url = $url;
    }

    public function sendRequest($type = 'json') {
        $curl = $this->getCurl();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => $this->getRt(),
            CURLOPT_URL => $this->getUrl()
        ]);

        $curl_result = curl_exec($curl);

        if ('json' === $type) {
            $curl_result = json_decode($curl_result);
        } elseif ('string' === $type) {
            parse_str($curl_result);

            if (!empty($access_token)) $curl_result = $access_token;
        }

        // Close request to clear up some resources
        curl_close($curl);

        if (!empty($curl_result->error)) {
            return false;
        }

        return $curl_result;
    }

    public function saveFile($dest) {
        $file = fopen($dest, 'w');
        $curl = $this->getCurl();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => $this->getRt(),
            CURLOPT_URL => $this->getUrl(),
//            CURLOPT_PROGRESSFUNCTION => 'progressCallback',
//            CURLOPT_NOPROGRESS => FALSE,
            CURLOPT_FAILONERROR => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
            CURLOPT_FILE => $file
        ]);

        $result = curl_exec($curl);
        curl_close($curl);

        fclose($file);

        return $result;
    }

    /**
     * @return mixed
     */
    public function getCurl() {
        return $this->curl;
    }

    /**
     * @param mixed $curl
     */
    public function setCurl($curl) {
        $this->curl = $curl;
    }

    /**
     * @return mixed
     */
    public function getRt() {
        return $this->rt;
    }

    /**
     * @param mixed $rt
     */
    public function setRt($rt) {
        $this->rt = $rt;
    }

    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }
}