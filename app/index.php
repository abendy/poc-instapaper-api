<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>poc-instapaper-api</title>

        <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon" />
        <link rel="stylesheet" href="assets/dist/main.css">
    </head>
    <body>
        <?php

        require_once __DIR__ . '/../config/config.php';

        try {

            // Get access token

            $access_token = $connection->oauth('oauth/access_token', $config);

            extract($access_token);

            // Connect to API

            $instapaper = new Abraham\TwitterOAuth\TwitterOAuth($consumerKey, $consumerSecret, $oauth_token, $oauth_token_secret);

            $instapaper->setTimeouts(1200, 1200); // connection timeout, request timeout

            // $user = $instapaper->post('account/verify_credentials');

            // set var of `folder_id` url parameters if it exists

            $fid = isset($_GET['fid']) ? $_GET['fid'] : 0;

            // if the `folder_id` url parameter doesn't exist show the folders

            $folders = empty($fid) ? $instapaper->post('folders/list') : array();

            if (!empty($folders)) {
                foreach($folders as $folder) {

                    $folderArr = (array) $folder;

                    extract($folderArr);

                    echo "<a href='?fid=$folder_id'>$display_title</a> | ";

                }
            }

            // set the default config for the bookmark listing api call

            $parameters = array('limit' => 500);

            // if the `folder_id` url parameter exists add it to the config for the bookmark listing api call

            if (!empty($fid)) {

                $parameters['folder_id'] = $fid;

                // show a back button

                echo $twig->render('back-button.twig');

            }

            // make the bookmark listing api call

            $bookmarks = $instapaper->post('bookmarks/list', $parameters);

            // show bookmarks

            foreach ($bookmarks as $bookmark) {

                $bookmarkArr = (array) $bookmark;

                extract($bookmarkArr);

                if (isset($bookmark_id) && isset($title) && isset($url)) {

                    echo "<p><strong><a href='$url' target='_blank'>$title</a></strong>  (<a href='https://www.instapaper.com/read/$bookmark_id'>paper read</a>)</p>";

                }

            }

        } catch (TwitterOAuthException $e) {
            d($e);
        }

        ?>
        <script defer src="assets/dist/main.js"></script>
    </body>
</html>
