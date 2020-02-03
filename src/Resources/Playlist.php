<?php

namespace Spotify\Resources;

use stdClass;
use Spotify\Constants\Auth;

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

        return $this->request->get($url, [
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken(Auth::CLIENT_ENTITY)),
        ]);
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

        return $this->request->get($url, [
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken(Auth::CLIENT_ENTITY)),
        ]);
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
    public function createPlaylist(string $name, bool $public = true, bool $collaborative = false, string $description = null) : stdClass
    {
        $user = $this->getUserProfile();

        $url = sprintf('%s/users/%s/playlists', self::API_BASE_URL, $user->id);

        $headers = [
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken(Auth::USER_ENTITY))
        ];

        $params = [
            'name' => $name,
            'public' => $public,
            'collaborative' => $collaborative,
            'description' => $description,
        ];

        return $this->request->json('POST', $url, $headers, $params);
    }
}
