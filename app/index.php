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


        // Set up authorization to API

        try {

            // Get access token

            $access_token = $connection->oauth('oauth/access_token', $config);

            // Connect to API

            extract($access_token);

            $instapaper = new Abraham\TwitterOAuth\TwitterOAuth($consumerKey, $consumerSecret, $oauth_token, $oauth_token_secret);

            $instapaper->setTimeouts(1200, 1200); // connection timeout, request timeout

        } catch (TwitterOAuthException $e) {

            die($e);

        }


        // Verify user

        try {

            $user = $instapaper->post('account/verify_credentials');

            $username = !empty($user) ? $user[0]->username : '';

        } catch (TwitterOAuthException $e) {

            die($e);

        }


        // Show the folders as a nav

        try {

            $folders = (array) $instapaper->post('folders/list');

        } catch (TwitterOAuthException $e) {

            die($e);

        }

        if (!empty($folders)) {

            // Loop over API object and create array for rendering

            foreach($folders as $i => $folder) {

                unset($folders[$i]);

                $folders[] = (array) $folder;

            }

            // Render the folders as a nav

            echo $twig->render('nav.twig', array('folders' => $folders, 'username' => $username));

        }


        // Show a back button

        echo $twig->render('back-button.twig');


        // Show the bookmarks

        // Set the API call parameters

        $fid = isset($_GET['fid']) ? $_GET['fid'] : 0;

        if (isset($fid)) {
            $parameters = array('limit' => 500, 'folder_id' => $fid);

            // Make the bookmark listing API call

            try {

                $bookmarks = (array) $instapaper->post('bookmarks/list', $parameters);

            } catch (TwitterOAuthException $e) {

                die($e);

            }

            // Process the highlights into array: `bookmark id` > `highlight id` > data

            $highlights = [];

            foreach ($bookmarks['highlights'] as $highlight) {

                $highlights[$highlight->bookmark_id][$highlight->highlight_id] = $highlight;

            }

            // Render highlight block

            echo $twig->render('highlights.twig', array('highlights' => $highlights));

            // Process the bookmarks

            // Loop over API object and create array for rendering

            foreach ($bookmarks['bookmarks'] as $i => $bookmark) {

                unset($bookmarks[$i]);

                $bookmark->highlights = isset($highlights) && isset($highlights[$bookmark->bookmark_id]) ? 1 : 0;

                $bookmarks[] = (array) $bookmark;

            }

            // Render bookmarks listing

            echo $twig->render('bookmarks.twig', array('bookmarks' => $bookmarks));

        }

        // Show a bookmark

        $bid = isset($_GET['bid']) ? $_GET['bid'] : 0;

        if (!empty($bid)) {

            // Make the bookmark text API call

            try {

                $text = $instapaper->post('bookmarks/get_text', array('bookmark_id' => $bid));

            } catch (TwitterOAuthException $e) {

                die($e);

            }

            // Make the bookmark highlights API call

            try {

                $highlightsArr = (array) $instapaper->post("bookmarks/$bid/highlights");

            } catch (TwitterOAuthException $e) {

                die($e);

            }

            // Reduce the highlights array to only include the text

            $highlights = array_map(function($highlight) {

                return $highlight->text;

            }, $highlightsArr);

            // Render

            echo $twig->render('bookmark.twig', array('text' => $text, 'highlights' => $highlights));

        }


        ?>
    <script defer src="assets/dist/main.min.js"></script>
</body>

</html>
