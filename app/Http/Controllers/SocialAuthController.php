<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class SocialAuthController extends Controller
{
    /**
     * Google redirect
     */
    public function googleRedirect()
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('google');
        return $driver->stateless()->redirect();
    }

    /**
     * Google callback
     */
    public function googleCallback()
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('google');
        $googleUser = $driver->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(uniqid()), // mot de passe aléatoire
            ]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }

    /**
     * Facebook redirect
     */
    public function facebookRedirect()
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('facebook');
        return $driver->stateless()->redirect();
    }

    /**
     * Facebook callback
     */
    public function facebookCallback()
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('facebook');
        $fbUser = $driver->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $fbUser->getEmail()],
            [
                'name' => $fbUser->getName(),
                'password' => bcrypt(uniqid()), // mot de passe aléatoire
            ]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }
}
