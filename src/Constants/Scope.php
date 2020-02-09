<?php

namespace Spotify\Constants;

/**
 * Class Scope
 *
 * @package Spotify\Constants
 */
class Scope
{
    /**
     * Image Scope
     *
     * @var string Write access to user-provided images.
     */
    public const UGC_IMAGE_UPLOAD = 'ugc-image-upload';

    /**
     * Library Scopes
     *
     * @var string Read access to a user's "Your Music" library.
     * @var string Write/delete access to a user's "Your Music" library.
     */
    public const USER_LIBRARY_READ = 'user-library-read';
    public const USER_LIBRARY_MODIFY = 'user-library-modify';

    /**
     * Playback Scopes
     *
     * @var string Control playback of a Spotify track.
     * @var string Remote control playback of Spotify.
     */
    public const STREAMING = 'streaming';
    public const APP_REMOTE_CONTROL = 'app-remote-control';

    /**
     * Playlists Scopes
     *
     * @var string Read access to user's private playlists.
     * @var string Include collaborative playlists when requesting a user's playlists.
     * @var string Write access to a user's public playlists.
     * @var string Write access to a user's private playlists.
     */
    public const PLAYLIST_READ_PRIVATE = 'playlist-read-private';
    public const PLAYLIST_READ_COLLABORATIVE = 'playlist-read-collaborative';
    public const PLAYLIST_MODIFY_PUBLIC = 'playlist-modify-public';
    public const PLAYLIST_MODIFY_PRIVATE = 'playlist-modify-private';

    /**
     * Follow Scopes
     *
     * @var string Write/delete access to the list of artists and other users that the user follows.
     * @var string Read access to the list of artists and other users that the user follows.
     */
    public const USER_FOLLOW_MODIFY = 'user-follow-modify';
    public const USER_FOLLOW_READ = 'user-follow-read';

    /**
     * Listening History Scopes
     *
     * @var string Read access to a user’s recently played tracks.
     * @var string Read access to a user's top artists and tracks.
     */
    public const USER_READ_RECENTLY_PLAYED = 'user-read-recently-played';
    public const USER_TOP_READ = 'user-top-read';

    /**
     * User Scopes
     *
     * @var string Read access to user’s subscription details (type of user account).
     * @var string Read access to user’s email address.
     */
    public const USER_READ_PRIVATE = 'user-read-private';
    public const USER_READ_EMAIL = 'user-read-email';

    /**
     * Spotify Connect Scopes
     *
     * @var string Read access to a user’s currently playing track.
     * @var string Read access to a user’s player state.
     * @var string Write access to a user’s playback state.
     */
    public const USER_READ_CURRENTLY_PLAYLING = 'user-read-currently-playing';
    public const USER_READ_PLAYBACK_STATE = 'user-read-playback-state';
    public const USER_MODIFY_PLAYBACK_STATE = 'user-modify-playback-state';
}
