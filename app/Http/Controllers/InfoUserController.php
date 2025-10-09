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
    // Méthode existante pour le profil
    public function create()
    {
        return view('laravel-examples.user-profile');
    }

    public function store(Request $request)
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'phone' => ['max:50'],
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
        
        User::where('id', Auth::user()->id)->update([
            'name' => $attributes['name'],
            'email' => $attribute['email'],
            'phone' => $attributes['phone'],
        ]);

        return redirect('/user-profile')->with('success','Profile updated successfully');
    }

    // Gestion des utilisateurs
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

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher l'édition des admins
        if ($user->role == 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('laravel-examples.user-edit', compact('user'));
    }

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
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        return redirect()->route('user-management')->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role == 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user->delete();
        return redirect()->route('user-management')->with('success', 'User deleted successfully.');
    }





    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);
    /** @var \App\Models\User $user */

    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    $user->update([
        'password' => Hash::make($request->new_password)
    ]);

    return back()->with('success', 'Password updated successfully');
}
}