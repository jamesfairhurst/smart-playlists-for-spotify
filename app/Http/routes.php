<?php

use App\Album;
use App\Artist;
use App\Playlist;
use App\Track;
use App\Events\UserVisitedTracksPage;
use Carbon\Carbon;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::get('/', function () {
        if (Auth::check()) {
            return redirect('/tracks');
        }
        return view('welcome');
    });

    // Route::get('/fire', function () {
    //    Illuminate\Support\Facades\Event::fire(new App\Events\UserSpotifyTracksRefreshed(Auth::user()));
    //     exit;
    // });

    Route::get('auth/spotify', 'Auth\AuthController@redirectToProvider');
    Route::get('auth/spotify/callback', 'Auth\AuthController@handleProviderCallback');
    Route::get('auth/logout', 'Auth\AuthController@logout');

    Route::get('/tracks', function () {
        if (!Auth::check()) {
            return redirect('/');
        }

        Illuminate\Support\Facades\Event::fire(new UserVisitedTracksPage(Auth::user()));

        // Init Spotify API library
        /*$api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken(Auth::user()->token['access_token']);

        try {
            $limit         = 50;
            $offset        = 50;
            $all           = false;
            $spotifyTracks = $api->getMySavedTracks(['limit' => $limit]);

            // Get saved Track count
            $spotifyTrackCount = $spotifyTracks->total;
            $trackCount = Track::where('user_id', Auth::user()->id)->count();

            // Spotify Tracks don't match saved Tracks so fetch them all
            if ($spotifyTracks->total != $trackCount) {
                // Get just the Tracks
                $spotifyTracks = $spotifyTracks->items;

                // Not dealt with all Tracks yet
                while ($all !== true) {
                    // Get next page of Spotify Tracks
                    $requestedTracks = $api->getMySavedTracks(['limit' => $limit, 'offset' => $offset]);

                    // Merge with current Tracks
                    $spotifyTracks = array_merge($spotifyTracks, $requestedTracks->items);

                    // Have all Tracks have been dealt with?
                    if (count($spotifyTracks) == $requestedTracks->total) {
                        $all = true;
                    } else {
                        $offset += $limit;
                    }
                }

                // Loop through all Spotify Tracks
                foreach ($spotifyTracks as $spotifyTrack) {
                    // Skip Track if it already exists
                    if (Track::where([
                        ['user_id', Auth::user()->id],
                        ['spotify_id', $spotifyTrack->track->id],
                    ])->count()) {
                        continue;
                    }

                    // Check for existing Artist
                    $artist = Artist::where('spotify_id', $spotifyTrack->track->artists[0]->id)->first();

                    if (is_null($artist)) {
                        $artist = Artist::create([
                            'spotify_id'   => $spotifyTrack->track->artists[0]->id,
                            'name'         => $spotifyTrack->track->artists[0]->name,
                            'spotify_href' => $spotifyTrack->track->artists[0]->href,
                            'spotify_uri'  => $spotifyTrack->track->artists[0]->uri,
                        ]);
                    }

                    // Check for existing Album
                    $album = Album::where('spotify_id', $spotifyTrack->track->album->id)->first();

                    // Album not found so create it
                    if (is_null($album)) {
                        // Get Spotify Album
                        $spotifyAlbumJson = file_get_contents($spotifyTrack->track->album->href);
                        $spotifyAlbum = json_decode($spotifyAlbumJson, true);

                        // Date changes based on precision so always make it a full date
                        if ($spotifyAlbum['release_date_precision'] == 'year') {
                            $spotifyAlbum['release_date'] .= '-01-01';
                        } elseif ($spotifyAlbum['release_date_precision'] == 'month') {
                            $spotifyAlbum['release_date'] .= '-01';
                        }

                        $album = Album::create([
                            'artist_id'    => $artist->id,
                            'spotify_id'   => $spotifyAlbum['id'],
                            'name'         => $spotifyAlbum['name'],
                            'released_at'  => $spotifyAlbum['release_date'],
                            'spotify_data' => $spotifyAlbumJson,
                        ]);
                    }

                    // Create Track
                    $track = Track::create([
                        'user_id'    => Auth::user()->id,
                        'artist_id'  => $artist->id,
                        'album_id'   => $album->id,
                        'spotify_id' => $spotifyTrack->track->id,
                        'album'      => $spotifyTrack->track->album->name,
                        'name'       => $spotifyTrack->track->name,
                        'added_at'   => Carbon::parse($spotifyTrack->added_at)->format('Y-m-d H:i:s')
                    ]);
                }
            }
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            Log::error($e->getMessage());
            return redirect('/tracks')
                ->withError('Something went wrong, please try again');
        }*/

        // Get all Tracks
        $tracks = Track::with('album')
            ->where('user_id', Auth::user()->id)
            ->orderBy('added_at', 'desc')
            ->paginate();

        return view('tracks', ['tracks' => $tracks]);
    });

    Route::get('/playlists', function () {
        if (!Auth::check()) {
            return redirect('/');
        }

        $playlists = Playlist::orderBy('created_at', 'asc')->paginate();

        return view('playlists', ['playlists' => $playlists]);
    });

    Route::post('/playlist', function (Request $request) {
        if (!Auth::check()) {
            return redirect('/');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            // 'rule.*.value' => 'required|max:255',
        ], [
            'rule.*.required' => 'This rule value must not be empty',
            // 'rule.*.max' => 'This rule value must be less than 255 characters',
        ]);

        if ($validator->fails()) {
            return redirect('/playlists')
                ->withInput()
                ->withErrors($validator);
        }

        try {
            $playlist = $request->user()->playlists()->create([
                'name'  => $request->name,
                'order' => $request->order,
                'limit' => $request->limit,
            ]);

            // Get Rules and remove ones with empty values
            // Playlists with empty values are allowed so that you could
            // create a "25 most recently added" playlist for example
            $rules = $request->get('rule');
            foreach ($rules as $key => $rule) {
                if (empty($rule['value'])) {
                    unset($rules[$key]);
                }
            }
            if (!empty($rules)) {
                $playlist->rules()->createMany($request->get('rule'));
            }
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            Log::error($e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
            return redirect('/playlists')
                ->withError('Something went wrong, please try again');
        }

        return redirect('/playlists')
            ->withSuccess('Playlist created');
    });

    Route::get('/playlist/{playlist}', function (Playlist $playlist) {
        if (!Auth::check()) {
            return redirect('/');
        }

        if ($playlist->user_id != Auth::user()->id) {
            abort(403, 'Unauthorized action');
        }

        return view('playlist', ['playlist' => $playlist, 'tracks' => $playlist->tracks()]);
    });

    Route::post('/playlist/{playlist}', function (Playlist $playlist) {
        if (!Auth::check()) {
            return redirect('/');
        }

        if ($playlist->user_id != Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken(Auth::user()->token['access_token']);

        try {
            // Get/Create Spotify Playlist
            if (!$playlist->spotify_id) {
                $spotifyPlaylist = $api->createUserPlaylist(Auth::user()->spotify_id, [
                    'name' => $playlist->name
                ]);

                $playlist->spotify_id = $spotifyPlaylist->id;
                $playlist->save();
            } else {
                $spotifyPlaylist = $api->getUserPlaylist(Auth::user()->spotify_id, $playlist->spotify_id);
            }

            // Chunk Spotify Track Ids into 100s as that's the limit
            $spotifyIdChunks = $playlist->tracks()->pluck('spotify_id')->chunk(100);
            foreach ($spotifyIdChunks as $key => $chunk) {
                // Replace the first chunk of Tracks
                // Basically clears the Playlist to start fresh
                if (!$key) {
                    $api->replaceUserPlaylistTracks(Auth::user()->spotify_id, $spotifyPlaylist->id, $chunk->toArray());

                // Add to the Spotify Playlist
                } else {
                    $api->addUserPlaylistTracks(Auth::user()->spotify_id, $spotifyPlaylist->id, $chunk->toArray());
                }
            }
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            Log::error($e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
            return redirect('/playlists')
                ->withError('Something went wrong, please try again');
        }

        return redirect('/playlists')
            ->withSuccess('Playlist pushed to Spotify');
    });

    Route::delete('/playlist/{playlist}', function (Playlist $playlist) {
        if (!Auth::check()) {
            return redirect('/');
        }

        if ($playlist->user_id != Auth::user()->id) {
            abort(403, 'Unauthorized action');
        }

        if ($playlist->spotify_id) {
            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken(Auth::user()->token['access_token']);

            try {
                // Unfollowing a Playlist is Spotify's way of deleting it
                $api->unfollowPlaylist(Auth::user()->spotify_id, $playlist->spotify_id);
            } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                Log::error($e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
                return redirect('/playlists')
                    ->withError('Something went wrong, please try again');
            }
        }

        $playlist->delete();

        return redirect('/playlists')
            ->withSuccess('Playlist deleted');
    });
});
