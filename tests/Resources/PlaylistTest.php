<?php

namespace Spotify\Tests\Resources;

use Spotify\Auth\Manager;
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
    public function test_fetching_playlist_returns_object() : void
    {
        $result = (object) [
            'name' => 'Playlist #1',
            'owner' => 'Matt Bartlett',
            'tracks' => [
                (object) [
                    'title' => 'Sanctuary',
                    'artist' => 'Gareth Emery',
                    'album' => 'Northern Lights'
                ]
            ]
        ];

        $this->requestMock->expects($this->once())
            ->method('get')
            ->willReturn($result);

        $this->managerMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access-token');

        $response = $this->playlist->getPlaylist('random-playlist-id');

        $this->assertInternalType('object', $response);
        $this->assertEquals('Playlist #1', $response->name);
        $this->assertEquals('Matt Bartlett', $response->owner);
    }
}
