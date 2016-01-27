<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/tracks';

    /**
     * oAuth provider
     *
     * @var object
     */
    protected $provider = null;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->provider = new \Audeio\Spotify\Oauth2\Client\Provider\Spotify([
            'clientId'     => env('SPOTIFY_CLIENT_ID'),
            'clientSecret' => env('SPOTIFY_SECRET'),
            'redirectUri'  => env('SPOTIFY_REDIRECT_URI'),
            'scopes'       => ['playlist-modify-public', 'user-library-read'],
        ]);
    }

    /**
     * Logout a User
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return redirect($this->provider->getAuthorizationUrl());
    }

    /**
     * Obtain the user information from Spotify
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request)
    {
        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);

            $user = $this->provider->getUserDetails($token);
        } catch (Exception $e) {
            // @todo include error
            return redirect('/');
        }

        $authUser = $this->findOrCreateUser($user, $token);

        Auth::login($authUser, true);

        return redirect($this->redirectTo);
    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $spotifyUser
     * @param $spotifyToken
     * @return User
     */
    private function findOrCreateUser($spotifyUser, $spotifyToken)
    {
        if ($authUser = User::where('spotify_id', $spotifyUser->uid)->first()) {
            $authUser->token = json_encode($spotifyToken);
            $authUser->save();

            return $authUser;
        }

        return User::create([
            'spotify_id' => $spotifyUser->uid,
            'name' => $spotifyUser->name,
            'avatar' => $spotifyUser->imageUrl,
            'token' => json_encode($spotifyToken),
        ]);
    }

}
