<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use League\OAuth2\Client\Token\AccessToken;

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
        'token',
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
            dd('expired');
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

}
