<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Client\Provider\GenericProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GenericProvider::class, function ($app) {
            return new GenericProvider([
                'clientId'                => env('SPOTIFY_CLIENT_ID'),
                'clientSecret'            => env('SPOTIFY_SECRET'),
                'redirectUri'             => env('SPOTIFY_REDIRECT_URI'),
                'urlAuthorize'            => 'https://accounts.spotify.com/authorize',
                'urlAccessToken'          => 'https://accounts.spotify.com/api/token',
                'urlResourceOwnerDetails' => 'https://api.spotify.com/v1/me',
                'scopes'                  => ['playlist-modify-public', 'user-library-read'],
                'scopeSeparator'          => ' ',
            ]);
        });
    }
}
