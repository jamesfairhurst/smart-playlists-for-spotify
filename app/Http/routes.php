<?php

use App\Track;
use App\Album;
use App\Playlist;
use Illuminate\Http\Request;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


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

    Route::get('auth/spotify', 'Auth\AuthController@redirectToProvider');
    Route::get('auth/spotify/callback', 'Auth\AuthController@handleProviderCallback');
    Route::get('auth/logout', 'Auth\AuthController@logout');

    Route::get('/tracks', function () {
        if (!Auth::check()) {
            return redirect('/');
        }

        // dd(Auth::check());
        // dd(Auth::user());
        // dd(Auth::logout());

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken(Auth::user()->token);

        try {
            // $userPlaylists = $api->getMyPlaylists();
            // $userTracks = $api->getMySavedTracks(['limit' => 50]);

            $limit  = 50;
            $offset = 50;
            $all    = false;
            $spotifyTracks = $api->getMySavedTracks(['limit' => 50]);
            // echo '<pre>';echo print_r($spotifyTracks, true);echo '</pre>';exit;

            /*$playlist = $api->createUserPlaylist(Auth::user()->spotify_id, ['name' => 'Test']);
            $api->addUserPlaylistTracks(Auth::user()->spotify_id, $playlist->id, ['3AL7gqIprtj52RdgTDpFUu','24JMNKkwLsQs3lpWgBri8B']);*/

            // Get saved Track count
            $trackCount = Track::where('user_id', Auth::user()->id)->count();

            // Spotify Tracks don't match saved Tracks so refresh
            if ($spotifyTracks->total != $trackCount) {
                // Delete all User Tracks
                Track::where('user_id', Auth::user()->id)->delete();

                // Get just the Tracks
                $spotifyTracks = $spotifyTracks->items;

                // Not dealt with all Tracks yet
                while ($all != true) {
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
                    // Not sure if this is really needed
                    if (Track::where([
                        ['user_id', Auth::user()->id],
                        ['spotify_id', $spotifyTrack->track->id],
                    ])->count()) {
                        continue;
                    }

                    // Check for existing Album
                    $album = Album::where('spotify_id', $spotifyTrack->track->album->id)->first();

                    // Album not found so create it
                    if (is_null($album)) {
                        // Get Spotify Album
                        $spotifyAlbum = json_decode(file_get_contents($spotifyTrack->track->album->href), true);

                        // Date changes based on precision so always make it a full date
                        if ($spotifyAlbum['release_date_precision'] == 'year') {
                            $spotifyAlbum['release_date'] .= '-01-01';
                        } elseif ($spotifyAlbum['release_date_precision'] == 'month') {
                            $spotifyAlbum['release_date'] .= '-01';
                        }

                        $album = Album::create([
                            'spotify_id' => $spotifyAlbum['id'],
                            'name' => $spotifyAlbum['name'],
                            'released_at' => $spotifyAlbum['release_date'],
                        ]);
                    }

                    // Create Track
                    $track = Track::create([
                        'user_id'    => Auth::user()->id,
                        'album_id'   => $album->id,
                        'spotify_id' => $spotifyTrack->track->id,
                        'artist'     => $spotifyTrack->track->artists[0]->name,
                        'album'      => $spotifyTrack->track->album->name,
                        'name'       => $spotifyTrack->track->name,
                        'added_at'   => Carbon::parse($spotifyTrack->added_at)->format('Y-m-d H:i:s')
                    ]);
                }
            }

            // Get all Tracks
            /*$tracks = Track::with('album')
                ->where('user_id', Auth::user()->id)
                // ->whereHas('album', function ($query) {
                //     $query->where(DB::raw('YEAR(released_at)'), 2015);
                // })
                // ->get();
                ->orderBy('added_at', 'desc')
                ->paginate();*/
            // echo '<pre>';echo print_r($tracks->toArray(), true);echo '</pre>';exit;

            // Sort Tracks & sort by decade
            /*$grouped = $tracks->sortBy('album.released_at')->groupBy(function ($item, $key) {
                return (int) floor($item->album->released_at->format('Y') / 10) * 10;
            });

            // Loop and create decade Playlist & add Tracks
            foreach ($grouped->toArray() as $decade => $tracks) {
                $playlist = $api->createUserPlaylist(Auth::user()->spotify_id, [
                    'name' => $decade . 's'
                ]);

                foreach ($tracks as $track) {
                    $api->addUserPlaylistTracks(Auth::user()->spotify_id, $playlist->id, $track['spotify_id']);
                }
            }*/

            // return view('home', ['tracks' => $tracks]);

        // Token expired
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            Auth::logout();

            return redirect('auth/spotify');
        }

        // Get all Tracks
        $tracks = Track::with('album')
            ->where('user_id', Auth::user()->id)
            ->orderBy('added_at', 'desc')
            ->paginate();

        return view('home', ['tracks' => $tracks]);
    });

    Route::get('/playlists', function () {
        $playlists = Playlist::orderBy('created_at', 'asc')->paginate();

        return view('playlists', ['playlists' => $playlists]);
    });

    Route::post('/playlist', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('/playlists')
                ->withInput()
                ->withErrors($validator);
        }

        try {

            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken(Auth::user()->token);

            $spotifyPlaylist = $api->createUserPlaylist(Auth::user()->spotify_id, [
                'name' => $request->name
            ]);

            $request->user()->playlists()->create([
                'spotify_id' => $spotifyPlaylist,
                'name' => $request->name,
            ]);

        // Token expired
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            Auth::logout();

            return redirect('auth/spotify');
        }

        return redirect('/playlists');
    });

    Route::delete('/playlist/{playlist}', function (Playlist $playlist) {
        $playlist->delete();

        return redirect('/playlists');
    });
});

/*Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');
});*/
