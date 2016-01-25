<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Auth;
use Socialite;

class AuthController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/tracks';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
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
        return Socialite::with('spotify')
            ->scopes(['playlist-modify-public', 'user-library-read'])
            ->redirect();
    }

    /**
     * Obtain the user information from Spotify
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('spotify')->user();
        } catch (Exception $e) {
            return redirect('/');
        }

        $authUser = $this->findOrCreateUser($user);

        Auth::login($authUser, true);

        return redirect($this->redirectTo);
    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $spotifyUser
     * @return User
     */
    private function findOrCreateUser($spotifyUser)
    {
        if ($authUser = User::where('spotify_id', $spotifyUser->getId())->first()) {
            $authUser->token = $spotifyUser->token;
            $authUser->save();

            return $authUser;
        }

        return User::create([
            'spotify_id' => $spotifyUser->getId(),
            'name' => $spotifyUser->getNickname(),
            'avatar' => $spotifyUser->getAvatar(),
            'token' => $spotifyUser->token,
        ]);
    }
}
