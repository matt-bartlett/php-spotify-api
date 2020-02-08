<?php

namespace Spotify\Resources;

use stdClass;
use Spotify\Constants\Auth;
use Spotify\Constants\Http;
use GuzzleHttp\RequestOptions;

/**
 * Class Playlist
 *
 * @package Spotify\Resources
 */
class Playlist extends Resource
{
    /**
     * Get a playlist owned by a Spotify user.
     *
     * @param string $playlist
     *
     * @return stdClass
     */
    public function getPlaylist(string $playlist) : stdClass
    {
        $url = sprintf('%s/playlists/%s', self::API_BASE_URL, $playlist);

        $payload = [
            RequestOptions::HEADERS => [
                'Authorization' => sprintf('Bearer %s', $this->getManager()->getAccessToken(Auth::CLIENT_ENTITY)),
            ]
        ];

        return $this->request->send(Http::GET, $url, $payload);
    }

    /**
     * Get full details of the tracks of a playlist owned by a Spotify user.
     *
     * @param string $playlist
     *
     * @return stdClass
     */
    public function getPlaylistTracks(string $playlist) : stdClass
    {
        $url = sprintf('%s/playlists/%s/tracks', self::API_BASE_URL, $playlist);

        $payload = [
            RequestOptions::HEADERS => [
                'Authorization' => sprintf('Bearer %s', $this->getManager()->getAccessToken(Auth::CLIENT_ENTITY)),
            ]
        ];

        return $this->request->send(Http::GET, $url, $payload);
    }

    /**
     * Create a Playlist for the currently authenticated user.
     *
     * Scopes required:
     *
     *      playlist-modify-public  (if Play is to be Public)
     *      playlist-modify-private (if Playist is to be Private)
     *
     * @param string $name
     * @param bool $public
     * @param bool $collaborative
     * @param string $description
     *
     * @return stdClass
     */
    public function createPlaylist(
        string $name,
        bool $public = true,
        bool $collaborative = false,
        string $description = null
    ) : stdClass {
        $user = $this->getUserProfile();

        $url = sprintf('%s/users/%s/playlists', self::API_BASE_URL, $user->id);

        $payload = [
            RequestOptions::HEADERS => [
                'Authorization' => sprintf('Bearer %s', $this->getManager()->getAccessToken(Auth::USER_ENTITY)),
            ],
            RequestOptions::JSON => [
                'name' => $name,
                'public' => $public,
                'description' => $description,
                'collaborative' => $collaborative,
            ],
        ];

        return $this->request->send(Http::POST, $url, $payload);
    }
}
