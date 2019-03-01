<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>poc-instapaper-api</title>

    <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon" />

    <link rel="stylesheet" href="assets/dist/main.min.css">
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

        } catch (TwitterOAuthException $e) {

            d($e);

        }

        // verify user

        $user = $instapaper->post('account/verify_credentials');

        $username = !empty($user) ? $user[0]->username : '';


        // show the folders

        $folders = (array) $instapaper->post('folders/list');

        if (!empty($folders)) {

            foreach($folders as $i => $folder) {

                unset($folders[$i]);

                $folders[] = (array) $folder;

            }

            echo $twig->render('nav.twig', array('folders' => $folders, 'username' => $username));

        }

        // show a back button

        echo $twig->render('back-button.twig');

        // show the bookmarks

        if (!empty($_GET['fid'])) {

            $parameters = array('limit' => 500, 'folder_id' => $_GET['fid']);

            // make the bookmark listing api call

            $bookmarks = (array) $instapaper->post('bookmarks/list', $parameters);

            $highlights = [];

            foreach ($bookmarks['highlights'] as $highlight) {

                $highlights[$highlight->bookmark_id][$highlight->highlight_id] = $highlight;

            }

            echo $twig->render('highlights.twig', array('highlights' => $highlights));

            // show the bookmarks

            foreach ($bookmarks['bookmarks'] as $i => $bookmark) {

                unset($bookmarks[$i]);

                extract((array) $bookmark);

                if (isset($bookmark_id) && isset($title) && isset($url)) {

                    $bookmark->highlights = (isset($highlights) && isset($highlights[$bookmark_id])) ? 1 : 0;

                    $bookmarks[] = (array) $bookmark;

                }

            }

            echo $twig->render('bookmarks.twig', array('bookmarks' => $bookmarks));
        }

        // show a bookmark

        $bid = isset($_GET['bid']) ? $_GET['bid'] : 0;

        if (!empty($bid)) {

            $parameters = array('bookmark_id' => $bid);

            // make the bookmark listing api call

            $text = $instapaper->post('bookmarks/get_text', $parameters);

            $highlightsArr = (array) $instapaper->post("bookmarks/$bid/highlights");

            $highlights = array_map(function($highlight) {

                return $highlight->text;

            }, $highlightsArr);

            echo $twig->render('bookmark.twig', array('text' => $text, 'highlights' => $highlights));

        }

        ?>
    <script defer src="assets/dist/main.min.js"></script>
</body>

</html>
