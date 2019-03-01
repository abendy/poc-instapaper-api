<?php

if (!session_id()) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

# https://github.com/abendy/instapaperoauth
use Abraham\TwitterOAuth\TwitterOAuth;

// Credentials

$creds = __DIR__ . '/credentials.json';

if (file_exists($creds)) {
    $json = file_get_contents($creds);

    $config = json_decode($json, true);

    extract($config);

    $connection = new TwitterOAuth($consumerKey, $consumerSecret);
}

// Twig
$loader = new Twig_Loader_Filesystem('../templates');

$twig = new Twig_Environment($loader, array(
    'cache' => '../templates/.cache',
    'debug' => true
));

// Add dump() function for debugging
$twig->addExtension(new Twig_Extension_Debug());
