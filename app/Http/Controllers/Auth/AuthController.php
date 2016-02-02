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

        $this->provider = \App::make('League\OAuth2\Client\Provider\GenericProvider');
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

            $user = $this->provider->getResourceOwner($token)->toArray();

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
        if ($authUser = User::where('spotify_id', $spotifyUser['id'])->first()) {
            $authUser->token = json_encode($spotifyToken);
            $authUser->save();

            return $authUser;
        }

        return User::create([
            'spotify_id' => $spotifyUser['id'],
            'name'       => $spotifyUser['display_name'],
            'avatar'     => ((isset($spotifyUser['images'][0]['url'])) ? $spotifyUser['images'][0]['url'] : ''),
            'token'      => json_encode($spotifyToken),
        ]);
    }

}
