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
        return view('session.login-session');
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
                // REDIRECT TO THE WORKING MODERATOR DASHBOARD URL
                return redirect()->intended('/moderator/dashboard')->with('success', 'Welcome Moderator!');
            } elseif ($user->isClubManager()) {
                // CORRECTION: Utiliser le bon nom de route
                return redirect()->route('club_manager.dashboard')->with('success', 'Welcome Club Manager!');
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