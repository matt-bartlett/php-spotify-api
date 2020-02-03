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
            ->setMethods(['get'])
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
            ->method('get')
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
            ->method('get')
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
}
