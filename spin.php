<?php

require_once './vendor/autoload.php';

use KS\HTTP\HTTP;

define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR', __DIR__);

$dirLucky = BASE_DIR . DS . 'lucky';
$cookiePath = BASE_DIR . DS . 'lazadahell_' . rand(1, 10000) . '.txt';

if (!file_exists($dirLucky) && !is_dir($dirLucky)) {
    mkdir($dirLucky);
}

$url = 'http://www.lazada.co.th/online-festival-spin-the-wheel/';
$urlToken = 'http://www.lazada.co.th/ajax/lottery/settings/?lang=th&platform=desktop&dpr=1';
$urlSpin = 'http://www.lazada.co.th/ajax/lottery/spinTheWheel/?lang=th&platform=desktop&wheelToken=';

$userAgent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';

$http = new HTTP($cookiePath);
$http->setUserAgent($userAgent);

$html = $http->get($url);
$html = $http->get($urlToken);

$json = json_decode($html, true);

if (empty($json['success']) || empty($json['data']) || $json['success'] != true) {
    echo "[!] Can't get wheel token.";
    exit(1);
}

$token = $json['data']['wheelToken'];

$urlSpinHell = $urlSpin . $token;

$count = 0;
$need_sectors = [1, 5, 9, 12];

while (true) {
    $count++;
    
    
    $html = $http->get($urlSpinHell);
    $json = json_decode($html, true);

    if (empty($json['success']) || empty($json['data']) || $json['success'] != true) {
        echo "[!] Can't get data spin wheel hell";
        exit(1);
    }

    $sector = $json['data']['sector'];
    echo "[-] Spin number : " . number_format($count) . " | Sector : " . $sector . "\n";

    if (in_array($sector, $need_sectors) == true) {
        echo "[!] W00t W00t lucky spin sector : " . $sector . "\n";
        file_put_contents($dirLucky . DS . microtime() . '.json', $html);
        break;
    }
    
    usleep(rand(0, 500000));
}






