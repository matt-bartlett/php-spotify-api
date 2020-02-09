<?php

namespace Spotify\Tests\Resources;

use Spotify\Manager;
use Spotify\Http\Request;
use Spotify\Resources\Playlist;
use PHPUnit\Framework\TestCase;

class PlaylistTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $this->managerMock = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAccessToken'])
            ->getMock();

        $this->playlist = new Playlist(
            $this->requestMock,
            $this->managerMock
        );
    }

    /**
     * @return void
     */
    public function test_fetching_playlist_response() : void
    {
        $json = file_get_contents(__DIR__ . '/../Fixtures/playlist.json');

        $this->requestMock->expects($this->once())
            ->method('send')
            ->willReturn(json_decode($json));

        $this->managerMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access-token');

        $response = $this->playlist->getPlaylist('random-playlist-id');

        $this->assertEquals('Prouse', $response->name);
        $this->assertEquals('Matt Bartlett', $response->owner->display_name);
        $this->assertInternalType('array', $response->tracks->items);
        $this->assertCount(5, $response->tracks->items);
    }

    /**
     * @return void
     */
    public function test_fetching_playlist_tracks_response() : void
    {
        $json = file_get_contents(__DIR__ . '/../Fixtures/playlist-tracks.json');

        $this->requestMock->expects($this->once())
            ->method('send')
            ->willReturn(json_decode($json));

        $this->managerMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access-token');

        $response = $this->playlist->getPlaylistTracks('random-playlist-id');

        $this->assertInternalType('array', $response->items);
        $this->assertCount(5, $response->items);

        $firstTrack = $response->items[0];

        $this->assertEquals('Destiny - Solid Stone Remix', $firstTrack->track->name);
    }

    /**
     * @return void
     */
    public function test_creating_playlist() : void
    {
        $meJson = json_decode(file_get_contents(__DIR__ . '/../Fixtures/me.json'));
        $playlistJson = json_decode(file_get_contents(__DIR__ . '/../Fixtures/playlist.json'));

        $this->requestMock->expects($this->exactly(2))
            ->method('send')
            ->will($this->onConsecutiveCalls($meJson, $playlistJson));

        $this->managerMock->expects($this->exactly(2))
            ->method('getAccessToken')
            ->willReturn('access-token');

        $response = $this->playlist->createPlaylist('Prouse');

        $this->assertEquals('Prouse', $response->name);
        $this->assertEquals('Matt Bartlett', $response->owner->display_name);
    }

    /**
     * @return void
     */
    public function test_adding_tracks_to_playlist() : void
    {
        $json = json_decode(file_get_contents(__DIR__ . '/../Fixtures/add-tracks.json'));

        $this->requestMock->expects($this->once())
            ->method('send')
            ->willReturn($json);

        $this->managerMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access-token');

        $tracks = [
            'uris' => 'spotify:track:2IF0UnzPiWfYqJRbx6hJtP'
        ];

        $response = $this->playlist->addTracksToPlaylist('playlist-id', $tracks);

        $this->assertEquals('MiwxZmNjMzE4ZmVhNTQ5NTIyZjZiYzdkYzk1NTg1NjE0OGZkNDg4Yzdl', $response->snapshot_id);
    }
}
