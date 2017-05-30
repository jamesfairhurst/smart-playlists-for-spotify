<?php

namespace App;

use Event;
use Exception;
use Log;
use App\Album;
use App\Artist;
use App\Events\UserSpotifyTracksRefreshed;
use App\Events\UserSpotifyTracksRetrieved;
use Carbon\Carbon;
use League\OAuth2\Client\Token\AccessToken;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spotify_id', 'name', 'avatar', 'token',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'token', 'remember_token'
    ];

    /**
     * Tracks
     */
    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    /**
     * Playlists
     */
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Get the JSON decoded access token
     *
     * @param  string $value
     * @return array
     */
    public function getTokenAttribute($value)
    {
        $token = json_decode($value, true);

        // Check Access Token and refresh if expired
        // Done here so that it's always valid when requested
        return $this->checkAccessToken($token);
    }

    /**
     * Check the User's Access Token and refresh if expired
     *
     * @param array $token
     * @return void
     */
    public function checkAccessToken($token)
    {
        // Setup Access Token from database
        $accessToken = new AccessToken([
            'access_token'  => $token['access_token'],
            'refresh_token' => $token['refresh_token'],
            'expires'       => $token['expires'],
        ]);

        // Has it expired?
        if ($accessToken->hasExpired()) {
            // Get Provider
            $provider = \App::make('League\OAuth2\Client\Provider\GenericProvider');

            // Get new Access Token
            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $token['refresh_token']
            ]);

            // Build new Access Token inc. refresh token
            // The above $newAccessToken doesn't have it set so would be unable
            // to refresh the Access Token again
            $accessToken = new AccessToken([
                'access_token'  => $newAccessToken->getToken(),
                'refresh_token' => $token['refresh_token'],
                'expires'       => $newAccessToken->getExpires(),
            ]);

            // Update passed token so we can return it
            $token = [
                'access_token'  => $newAccessToken->getToken(),
                'refresh_token' => $token['refresh_token'],
                'expires'       => $newAccessToken->getExpires(),
            ];

            $this->token = json_encode($accessToken);
            $this->save();
        }

        return $token;
    }

    /**
     * Refresh a User's Spotify Tracks
     *
     * @return boolean
     */
    public function refreshSpotifyTracks()
    {
        $api = new SpotifyWebAPI();
        $api->setAccessToken($this->token['access_token']);

        Log::info('Starting refreshSpotifyTracks');

        try {
            $limit         = 50;
            $offset        = 50;
            $all           = false;
            $spotifyTracks = $api->getMySavedTracks(['limit' => $limit]);

            // Get saved Track count
            $spotifyTrackCount = $spotifyTracks->total;
            $trackCount = $this->tracks()->count();

            Log::info('Getting rest of tracks');

            // Spotify Tracks don't match saved Tracks so fetch them all
            if ($spotifyTracks->total != $trackCount) {
                // Get just the Tracks
                $spotifyTracks = $spotifyTracks->items;

                // Not dealt with all Tracks yet
                while ($all !== true) {
                    // Sleep
                    sleep(1);

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

                Log::info('All tracks found');

                $spotifyTrackIds = collect($spotifyTracks)->map(function ($item, $key) {
                    return $item->track->id;
                })->toArray();
                $userTracks = $this->tracks->map(function ($item, $key) {
                    return $item->spotify_id;
                })->toArray();
                $removedTrackIds = collect($userTracks)->diff($spotifyTrackIds)->toArray();

                // Loop through all Spotify Tracks
                foreach ($spotifyTracks as $spotifyTrack) {
                    Log::info('Processing track ' . $spotifyTrack->track->id);

                    // Skip Track if it already exists
                    if (in_array($spotifyTrack->track->id, $userTracks)) {
                        continue;
                    }

                    Log::info('Checking artist');

                    // Check for existing Artist
                    $artist = Artist::where('spotify_id', $spotifyTrack->track->artists[0]->id)->first();

                    if (is_null($artist)) {
                        Log::info('Creating artist');

                        $artist = Artist::create([
                            'spotify_id'   => $spotifyTrack->track->artists[0]->id,
                            'name'         => $spotifyTrack->track->artists[0]->name,
                            'spotify_href' => $spotifyTrack->track->artists[0]->href,
                            'spotify_uri'  => $spotifyTrack->track->artists[0]->uri,
                        ]);
                    }

                    Log::info('Checking album');

                    // Check for existing Album
                    $album = Album::where('spotify_id', $spotifyTrack->track->album->id)->first();

                    // Album not found so create it
                    if (is_null($album)) {
                        Log::info('Creating album');

                        // Get Spotify Album
                        $api->setReturnType(SpotifyWebAPI::RETURN_ASSOC);
                        $spotifyAlbum = $api->getAlbum($spotifyTrack->track->album->id);

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
                            'spotify_data' => json_encode($spotifyAlbum),
                        ]);
                    }

                    Log::info('Creating track');

                    // Create Track
                    $this->tracks()->create([
                        'artist_id'  => $artist->id,
                        'album_id'   => $album->id,
                        'spotify_id' => $spotifyTrack->track->id,
                        'album'      => $spotifyTrack->track->album->name,
                        'name'       => $spotifyTrack->track->name,
                        'added_at'   => Carbon::parse($spotifyTrack->added_at)->format('Y-m-d H:i:s')
                    ]);
                }

                if (!empty($removedTrackIds)) {
                    Log::info('Removing tracks');

                    $this->tracks()->whereIn('spotify_id', $removedTrackIds)->delete();
                }

                if (!$trackCount) {
                    Event::fire(new UserSpotifyTracksRetrieved($this));
                } else {
                    Event::fire(new UserSpotifyTracksRefreshed($this));
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());

            return false;
        }

        Log::info('Finishing refreshSpotifyTracks');

        return true;
    }
}
