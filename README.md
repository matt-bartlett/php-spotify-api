# Spotify PHP Library

This is a framework agnostic PHP library for interacting with the Spotify Web API.

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
This library revolves around `Resources`. Resources encapsulate specific Spotify functionality. Session handling has been abstracted, and while this library ships with it's own session handlers, you are free to write your own and bind it to the implementation. I strongly advise using some sort of dependency container to configure this library. I will go through some examples.

### Generic Example
If you're working in a plain PHP environment, then `examples/vanilla_php_example.php` should cover basic usage of the library.

### Laravel Example
This library ships with a `LaravelSessionHandler`. This class abstracts the session handler baked into Laravel, allowing you to use the same session driver controlling your application. As Laravel comes with it's own dependency container, we won't have to provide as much configuration. We will however, need to resolve the concrete implementations. We can do that using a service provider.

Create a new service provider within `App\Provider` and paste the following:

```php
<?php

namespace App\Providers;

use Spotify\Http\Request;
use Illuminate\Session\Store;
use Spotify\Auth\Credentials;
use Spotify\Auth\Authenticator;
use Spotify\Contracts\Store\Session;
use Illuminate\Support\ServiceProvider;
use Spotify\Sessions\LaravelSessionHandler;
use Spotify\Contracts\Auth\Authenticator as AuthenticatorInterface;

class SpotifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind Credentials.
        $this->app->singleton(Credentials::class, function ($app) {
            return new Credentials(
                getenv('SPOTIFY_CLIENT_ID'),
                getenv('SPOTIFY_CLIENT_SECRET'),
                getenv('SPOTIFY_REDIRECT_URL')
            );
        });

        // Bind Authenticator.
        $this->app->bind(AuthenticatorInterface::class, function ($app) {
            return new Authenticator(
                $app->make(Request::class),
                $app->make(Credentials::class)
            );
        });

        // Bind the Laravel Session handler.
        $this->app->bind(Session::class, function ($app) {
            return new LaravelSessionHandler(
                $app->make(Store::class)
            );
        });
    }
}
```

Next, in your `config/app.php`, be sure to add this service provider to the `providers` array.

We'll need to add our Spotify credentials to our `.env` file. Replace the values and paste it in.

```
SPOTIFY_CLIENT_ID=client-id
SPOTIFY_CLIENT_SECRET=client-secret
SPOTIFY_REDIRECT_URL=your-redirect-url
```

With everything configured, let's run through some examples.

#### Client Credential Flow
The Client Credentials authorization flow doesn't require any user authorization in order to generate an access token. Typically, this access token only allows fetching/reading actions, such as finding a Playlist and all it's tracks.

```php
<?php

namespace App\Http\Controllers;

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
        $playlist = $this->service->getPlaylist($request->get('playlist-id'));

        return $playlist;
    }
}
```

#### Authorization Code Flow
Actions such as creating a Playlist will need user authorization. In order to obtain to an access token, we must first ask the user to authorize and agree to the actions we wish to perform. Upon confirming, they will be redirected to the `redirect_url` set on the `Spotify\Auth\Credentials` class, along with a code we can exchange for an access token.

To demonstrate, I'll add 2 routes to our application. The first route will handle the redirection from Spotify. The second route will create a Playlist.

```php
Route::get('/spotify/redirect', 'SpotifyController@redirect');
Route::post('/spotify/playlist/create', 'SpotifyController@playlist');
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spotify\Constants\Scope;
use Spotify\Resources\Playlist;
use Spotify\Exceptions\UserRequiresAuthorizationException;

class SpotifyController extends Controller
{
    protected $service;

    public function __construct(Playlist $service)
    {
        $this->service = $service;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function redirect(Request $request)
    {
        $code  = $request->get('code');
        $error = $request->get('error');

        if ($error) {
            // Do something when an error is returned.
        }

        // Exchange code for access token.
        $this->service->getManager()->handleCallback($code);

        // Redirect the user back to the `playlist` route.
        $redirect = $request->session()->get('redirect', '/an/alternative/endpoint');

        // Let's clean up after ourselves, remove it from the session.
        $request->session()->forget('redirect');

        return redirect()-to($redirect);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function playlist(Request $request)
    {
        try {
            // Get the Playlist name from the request.
            $name = $request->get('playlist-name');

            // Create a Playlist with the given name.
            $playlist $this->service->createPlaylist($name));

            return response()->json($playlist);
        } catch (UserRequiresAuthorizationException $e) {
            // Generate authorization URL. We'll need the `playlist-modify-public` scope to make a playlist.
            $url = $this->service->getManager()->getAuthorizationUrl([Scope::PLAYLIST_MODIFY_PUBLIC], true);

            $currentRoute = url()->full();

            // To make this a bit more seamless, we'll set the intended URL to the session.
            $request->session()->put(['redirect' => $currentRoute]);

            return redirect()->to($url);
        }
    }
}
```

You may have a dedicated controller that handles redirection. This wouldn't necessarily tie in with a particular Resource. In this event, you can use the `Manager`.

```php
<?php

namespace App\Http\Controllers;

use Spotify\Manager;
use Spotify\Constants\Scope;
use Illuminate\Http\Request;
use Spotify\Exceptions\UserRequiresAuthorizationException;

class SpotifyRedirectController extends Controller
{
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function redirect(Request $request)
    {
        $code  = $request->get('code');
        $error = $request->get('error');

        if ($error) {
            // Do something when an error is returned.
        }

        // Exchange code for access token.
        $this->manager->handleCallback($code);

        return redirect('route.name');
    }
}
```
