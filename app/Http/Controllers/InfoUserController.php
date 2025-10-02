<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class InfoUserController extends Controller
{
    // Méthode existante pour le profil - CORRIGÉE
    public function create()
    {
        return view('laravel-examples/user-profile');
    }

    public function store(Request $request)
    {
        // ✅ SEULEMENT les champs qui existent dans le modèle User
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'phone' => ['max:50'], // ✅ phone existe dans $fillable
        ]);
        
        if($request->get('email') != Auth::user()->email)
        {
            if(env('IS_DEMO') && Auth::user()->id == 1)
            {
                return redirect()->back()->withErrors(['msg2' => 'You are in a demo version, you can\'t change the email address.']);
            }
        }
        else{
            $attribute = request()->validate([
                'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            ]);
        }
        
        // ✅ SEULEMENT les champs qui existent dans le modèle
        User::where('id', Auth::user()->id)->update([
            'name' => $attributes['name'],
            'email' => $attribute['email'],
            'phone' => $attributes['phone'],
        ]);

        return redirect('/user-profile')->with('success','Profile updated successfully');
    }

    // ✅ MÉTHODES POUR LA GESTION DES UTILISATEURS
    public function userManagement()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('laravel-examples.user-management', compact('users'));
    }

    public function createUser()
    {
        return view('laravel-examples.user-create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:moderator,club_manager,user',
            'phone' => 'nullable|string|max:50', // ✅ Ajout du phone
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone, // ✅ Ajout du phone
        ]);

        return redirect()->route('user-management')->with('success', 'User created successfully.');
    }

    public function editUser(User $user)
    {
        if ($user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        return view('laravel-examples.user-edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:moderator,club_manager,user',
            'phone' => 'nullable|string|max:50', // ✅ Ajout du phone
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone, // ✅ Ajout du phone
        ]);

        return redirect()->route('user-management')->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $user->delete();
        return redirect()->route('user-management')->with('success', 'User deleted successfully.');
    }
}