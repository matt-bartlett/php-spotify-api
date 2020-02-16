<?php

// This example demonstrates how we might leverage this library using plain PHP (no frameworks).

require 'path/to/autoload.php';

// Start session.
session_start();

// Register dependencies. To make this easier on the eyes,
// I'll use the League Container as an example.
// Check it out here: https://container.thephpleague.com/
$container = new \League\Container\Container;

// Add Credentials.
$container->add(\Spotify\Auth\Credentials::class)
	->addArgument('your-client-id')
	->addArgument('your-client-secret')
	->addArgument('your-redirect-url');

// Add Guzzle.
$container->add(\GuzzleHttp\Client::class);

// Create the Request class, binding Guzzle Http Client.
$container->add(\Spotify\Http\Request::class)
	->addArgument(\GuzzleHttp\Client::class);

// Create the Authenticator, binding the Request and Credentials classes.
$container->add(\Spotify\Auth\Authenticator::class)
	->addArgument(\Spotify\Http\Request::class)
	->addArgument(\Spotify\Auth\Credentials::class);

// We'll use the GenericSessionHandler. Add it to the container.
$container->add(\Spotify\Sessions\GenericSessionHandler::class);

// Creating the Manager, binding the Authenticator and Session Handler classes.
$container->add(\Spotify\Manager::class)
	->addArgument(\Spotify\Auth\Authenticator::class)
	->addArgument(\Spotify\Sessions\GenericSessionHandler::class);

// Instantiate the relevant resource. We want to create a Playlist, so let's use that.
$playlist = new Spotify\Resources\Playlist(
	$container->get(Spotify\Http\Request::class),
	$container->get(Spotify\Manager::class)
);

// We'll use this file to handle the callback from Spotify.
// If a `code` is present in the URL, we'll exchange it for an
// access token and use it to create a playlist. If no `code`
// is present, we'll redirect the user to Spotify to be authorized.
if ($_REQUEST['code']) {
    // Exchange the code for an access token.
    $playlist->getManager()->handleCallback($_REQUEST['code']);

    // Make a request to Spotify.
    $response = $playlist->createPlaylist('My Playlist.');

    // Print out the response.
    print_r($response);
} else {
    // Generate authorization URL. We'll need the `playlist-modify-public` scope to make a playlist.
    $url = $playlist->getManager()->getAuthorizationUrl([Spotify\Constants\Scope::PLAYLIST_MODIFY_PUBLIC], true);

    // Redirect the user to Spotify and exit the script.
    header("Location: " . $url);
    exit;
}
