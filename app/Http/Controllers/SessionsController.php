<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SessionsController extends Controller
{
    // Afficher la page de login
    public function create()
    {
        return view('session.login-session'); // correspond à ta structure
    }

    // Authentification et redirection selon rôle
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($attributes)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended('/dashboard')->with('success', 'Welcome Admin!');
            } elseif ($user->isModerator()) {
    return view('dashboard_moderator'); // correspond à ta vue
}            elseif ($user->isClubManager()) {
                return redirect()->route('dashboard.club_manager')->with('success', 'Welcome Club Manager!');
            } else {
                return redirect()->route('dashboard.user')->with('success', 'Welcome!');
            }
        }

        return back()->withErrors(['email' => 'Email or password invalid.']);
    }

    // Déconnexion
    public function destroy()
    {
        Auth::logout();
        return redirect('/login')->with('success', 'You have been logged out.');
    }
}
