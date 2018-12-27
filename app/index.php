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

            // show loading bar

            echo $twig->render('loading-bar.twig');

            // Get access token

            $access_token = $connection->oauth('oauth/access_token', $config);

            extract($access_token);

            // Connect to API

            $instapaper = new Abraham\TwitterOAuth\TwitterOAuth($consumerKey, $consumerSecret, $oauth_token, $oauth_token_secret);

            $instapaper->setTimeouts(1200, 1200); // connection timeout, request timeout

            // $user = $instapaper->post('account/verify_credentials');

            // set var of `folder_id` url parameters if it exists

            $fid = isset($_GET['fid']) ? $_GET['fid'] : 0;

            // show the folders

            $folders = (array) $instapaper->post('folders/list');

            if (!empty($folders)) {

                foreach($folders as $i => $folder) {

                    unset($folders[$i]);

                    $folders[] = (array) $folder;

                }

                echo $twig->render('nav.twig', array('folders' => $folders));

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

            $bookmarks = (array) $instapaper->post('bookmarks/list', $parameters);

            // show the bookmarks

            foreach ($bookmarks as $i => $bookmark) {

                unset($bookmarks[$i]);

                $bookmarkArr = (array) $bookmark;

                extract($bookmarkArr);

                if (isset($bookmark_id) && isset($title) && isset($url)) {
                    $bookmarks[] = (array) $bookmark;
                }

            }

            echo $twig->render('bookmarks.twig', array('bookmarks' => $bookmarks));

        } catch (TwitterOAuthException $e) {
            d($e);
        }

        ?>
        <script defer src="assets/dist/main.js"></script>
    </body>
</html>
