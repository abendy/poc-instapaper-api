<?php

if (!session_id()) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

// Credentials
$creds = __DIR__ . '/credentials.json';

if (file_exists($creds)) {
    $json = file_get_contents($creds);

    $config = json_decode($json, true);

    extract($config);

    $connection = new TwitterOAuth($consumerKey, $consumerSecret);
}
