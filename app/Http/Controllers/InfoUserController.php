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
    // Afficher le profil utilisateur
    public function create()
    {
        return view('laravel-examples.user-profile');
    }

    // Mise à jour du profil utilisateur connecté
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();

        // Empêcher la modification de l'email en mode démo
        if ($request->email != $user->email && env('IS_DEMO') && $user->id == 1) {
            return back()->withErrors(['msg2' => 'You are in a demo version, you can\'t change the email address.']);
        }

        $user->update($attributes);

        return redirect('/user-profile')->with('success', 'Profile updated successfully.');
    }

    // Gestion des utilisateurs (admin)
    public function userManagement(Request $request)
    {
        $query = User::where('role', '!=', 'admin');

        // Filtres de recherche
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        // Tri
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                case 'email':
                    $query->orderBy('email', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->get();
        $editUser = null;

        if ($request->has('edit')) {
            $editUser = User::find($request->input('edit'));
            if ($editUser && $editUser->role == 'admin') {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('laravel-examples.user-management', compact('users', 'editUser'));
    }

    public function createUser()
    {
        return view('laravel-examples.user-create');
    }

    // Ajout d’un nouvel utilisateur
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:moderator,club_manager,user',
            'phone' => 'nullable|string|max:50',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        return redirect()->route('user-management')->with('success', 'User created successfully.');
    }

    // Édition d’un utilisateur
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->role == 'admin') {
            abort(403, 'Unauthorized action.');
        }
        return view('laravel-examples.user-edit', compact('user'));
    }

    // Mise à jour d’un utilisateur
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->role == 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:moderator,club_manager,user',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('user-management')->with('success', 'User updated successfully.');
    }

    // Suppression d’un utilisateur
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->role == 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user->delete();

        return redirect()->route('user-management')->with('success', 'User deleted successfully.');
    }

    // Changement de mot de passe utilisateur connecté
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
