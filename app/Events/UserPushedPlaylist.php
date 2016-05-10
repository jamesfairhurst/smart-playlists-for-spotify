<?php

namespace App\Events;

use Log;
use App\Playlist;
use App\Events\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UserPushedPlaylist extends Event
{
    use SerializesModels;

    public $playlist;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;

        Log::info('A user (' . $playlist->user->spotify_id . ') pushed a playlist to Spotify');
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
