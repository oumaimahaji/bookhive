<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('session.register');
    }

    public function store(Request $request)
    {
        // Validation
        $attributes = $request->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20'],
            'agreement' => ['accepted'] // pour accepter les CGU
        ]);

        // Hachage du mot de passe
        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['role'] = 'user'; // Tout nouvel inscrit est un simple utilisateur

        // Création utilisateur
        $user = User::create($attributes);

        // Connexion automatique
        Auth::login($user);

        // Message de succès
        session()->flash('success', 'Votre compte a été créé.');

        return redirect('/dashboard'); // page après inscription
    }
}
