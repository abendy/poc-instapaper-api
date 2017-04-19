<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>poc-instapaper-api</title>
        <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon" />

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
        <link rel="stylesheet" href="assets/material-design-lite/material.min.css">
        <link rel="stylesheet" href="assets/html5-boilerplate/dist/css/normalize.css">
        <link rel="stylesheet" href="assets/html5-boilerplate/dist/css/main.css">
        <link rel="stylesheet" href="assets/style.css">
    </head>
    <body>
        <?php

        require_once __DIR__ . '/../config/config.php';

        try {

            $access_token = $connection->oauth('oauth/access_token', $config);

            extract($access_token);
    
            $instapaper = new Abraham\TwitterOAuth\TwitterOAuth($consumerKey, $consumerSecret, $oauth_token, $oauth_token_secret);



        } catch (TwitterOAuthException $e) {
            d($e);
        }

        ?>
        <script defer src="assets/material-design-lite/material.min.js"></script>
    </body>
</html>
