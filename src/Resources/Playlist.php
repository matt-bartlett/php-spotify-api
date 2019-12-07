<?php

namespace Spotify\Resources;

use stdClass;

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
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken()),
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
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken()),
        ]);
    }
}
