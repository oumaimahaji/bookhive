<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ModeratorController extends Controller
{
    public function dashboard()
    {
        $pendingBooksCount = Book::where('is_valid', false)->count();
        return view('dashboard_moderator', compact('pendingBooksCount'));
    }

    public function books()
    {
        $pendingBooks = Book::where('is_valid', false)->get();
        return view('moderator.books', compact('pendingBooks'));
    }

    public function validateBook($id)
    {
        // CORRECTION : Utiliser update() directement
        Book::where('id', $id)->update(['is_valid' => true]);

        return redirect()->back()->with('success', 'Book validated successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('moderator.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // CORRECTION : Utiliser update() directement
        User::where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('moderator.profile')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // CORRECTION : Utiliser update() directement
        User::where('id', $user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('moderator.profile')->with('success', 'Password updated successfully.');
    }
}