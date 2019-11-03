# Spotify PHP Library

This is a framework agnostic PHP library for interacting with the Spotify Web API.

## Intentions

Goals of this package:
* Create a package that's framework agnostic
* Handle the different Authorization Flows
* Perform transformation of API responses
* Abstract Session management

## Install
Install using Composer

```bash
$ composer require matt-bartlett/php-spotify-api
```

Head over to [Spotify](https://developer.spotify.com/dashboard/) and create an application. You'll need the following for later use:

* Spotify Client ID
* Spotify Client Secret
* Redirect URL

## Basic Usage
Before you start, you will need to instantiate a `Credentials` class and populate it with your Spotify credentials.

```php
$credentials = new \Spotify\Auth\Credentials(
    'your-spotify-client-id',
    'your-spotify-client-secret',
    'your-spotify-redirect-url'
);
```

### Generic Example
The easiest approach is to create `\Spotify\Auth\Manager` and inject that into each `Resource`. By using the Manager, you can leverage your session driver to fetch a previously requested access token, rather than requesting a fresh token each time (this is optional, we'll skip it in this example). For this example, I'll use the **Client Credentials** authentication flow.

```php
// Request depends on the GuzzleHttp Client. Resolve the Request class using your container.
$request = new \Spotfiy\Http\Request();

// Create the authenticator, using the credentials class we created above.
$flow = new \Spotify\Auth\Flows\ClientCredentials($request, $credentials);

// Inject the authenticator into the Manager.
$manager = new \Spotify\Auth\Manager($flow);

// Fetch a Playlist from Spotify using the Playlist resource.
$resource = (new \Spotify\Resources\Playlist($request, $manager)
    ->getPlaylist('0yQ8UYw9WEjKQrpzHtEYx0');
```

This example demonstrates the high level flow. I wouldn't recommend instantiating these classes in this manner. Instead, resolve these in your dependency container and call them when necessary.

### Laravel Example
Now, for a more specific (workable) example, I'll demonstrate how best to use this package within Laravel.

First, you'll resolve the required dependencies using a Service Provider. Within `App\Providers\AppServiceProvider`, add the following to the `register()` method.

```php
// These should be added to the top of your ServiceProvider class.
use Spotify\Http\Request;
use Spotify\Auth\Credentials;
use Illuminate\Session\Store;
use Spotify\Contracts\Store\Session;
use Spotify\Auth\Flows\ClientCredentials;
use Spotify\Contracts\Auth\Authenticator;
use Spotify\Sessions\LaravelSessionHandler;

// Bind Spotify credentials.
$this->app->singleton(Credentials::class, function ($app) {
    return new Credentials(
        getenv('SPOTIFY_CLIENT_ID'),
        getenv('SPOTIFY_CLIENT_SECRET'),
        getenv('SPOTIFY_REDIRECT_URL')
    );
});

// Bind Client Credentials to the Spotify authenticator.
$this->app->bind(Authenticator::class, function ($app) {
    return new ClientCredentials(
        $app->make(Request::class),
        $app->make(Credentials::class)
    );
});

// Optional. Only if you want to use your Session driver.
// Bind the Laravel Session handler.
$this->app->bind(Session::class, function ($app) {
    return new LaravelSessionHandler(
        $app->make(Store::class)
    );
});
```

`LaravelSessionHandler` extends Laravel's session interface, allowing you to leverage the session driver you have configured within your application.

With the Service Provider boilerplate added, I'll demonstrate how to pull this into your application.

```php
<?php

use Spotify\Resources\Playlist;

class ExampleController extends Controller
{
    protected $service;

    public function __construct(Playlist $service)
    {
        $this->service = $service;
    }

    public function show(Request $request)
    {
        $playlist = $this->service->getPlaylist($request->get('playlist-id');

        return $playlist;
    }
}
```

## MVP Functionality
- [ ] Allow [Client Credentials](https://developer.spotify.com/documentation/general/guides/authorization-guide/#client-credentials-flow) authentication
- [ ] Abstract Session Management
- [ ] Fetch [Playlist & Playlist Tracks](https://developer.spotify.com/documentation/web-api/reference/playlists/)

## Future Functionality
- [ ] Allow [Authorization Code Flow](https://developer.spotify.com/documentation/general/guides/authorization-guide/#authorization-code-flow)
- [ ] Allow Scope requesting
- [ ] Transform/Hydrate API responses into a consistent format

Expand to other Spotify API endpoints:

- [ ] [Create Playlists](https://developer.spotify.com/documentation/web-api/reference/playlists/create-playlist/)
- [ ] [Recommendations](https://developer.spotify.com/documentation/web-api/reference/browse/get-recommendations/)
- [ ] Allow for pagniation
