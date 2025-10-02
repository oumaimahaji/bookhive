<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            abort(403, 'User not authenticated');
        }

        // Initialiser les variables avec des valeurs par défaut
        $activeReservations = 0;
        $totalReviews = 0;
        $availableBooks = 0;

        try {
            // Compter les réservations de l'utilisateur
            $activeReservations = Reservation::where('user_id', $user->id)->count();
            
            // Compter les reviews de l'utilisateur
            $totalReviews = Review::where('user_id', $user->id)->count();
            
            // Compter les livres disponibles
            $availableBooks = Book::where('is_valid', true)->count();

        } catch (\Exception $e) {
            // En cas d'erreur, logger l'erreur mais continuer avec les valeurs par défaut
            Log::error('User Dashboard Error: ' . $e->getMessage());
        }

        // Passer les données à la vue
        return view('dashboard_user', [
            'activeReservations' => $activeReservations,
            'totalReviews' => $totalReviews,
            'availableBooks' => $availableBooks
        ]);
    }

    public function books()
    {
        try {
            $books = Book::where('is_valid', true)->get();
            return view('user.books', compact('books'));
        } catch (\Exception $e) {
            Log::error('User Books Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load books.');
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // Mise à jour du profil
            User::where('id', $user->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');

        } catch (\Exception $e) {
            Log::error('Update Profile Error: ' . $e->getMessage());
            return redirect()->route('user.profile')->with('error', 'Error updating profile.');
        }
    }
}