<?php

namespace App\Listeners;

use App\Events\UserWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateDefaultPlaylists
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserWasCreated  $event
     * @return void
     */
    public function handle(UserWasCreated $event)
    {
        // Access the user using $event->user...

        // Create Playlists
        $playlist = $event->user->playlists()->create([
            'name'  => '1980s',
            'order' => 'year_asc',
            'limit' => 0,
        ]);

        $playlist->rules()->createMany([
            [
                'key' => 'year',
                'comparison_operator' => '>',
                'value' => '1979'
            ],
            [
                'key' => 'year',
                'comparison_operator' => '<',
                'value' => '1990'
            ],
        ]);

        $playlist = $event->user->playlists()->create([
            'name'  => '1990s',
            'order' => 'year_asc',
            'limit' => 0,
        ]);

        $playlist->rules()->createMany([
            [
                'key' => 'year',
                'comparison_operator' => '>',
                'value' => '1989'
            ],
            [
                'key' => 'year',
                'comparison_operator' => '<',
                'value' => '2000'
            ],
        ]);

        $playlist = $event->user->playlists()->create([
            'name'  => '2000s',
            'order' => 'year_asc',
            'limit' => 0,
        ]);

        $playlist->rules()->createMany([
            [
                'key' => 'year',
                'comparison_operator' => '>',
                'value' => '1999'
            ],
            [
                'key' => 'year',
                'comparison_operator' => '<',
                'value' => '2010'
            ],
        ]);
    }
}
