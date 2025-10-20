<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'admin' || $user->role === 'moderator') {
            $reservations = Reservation::with(['user', 'book'])->get();
            $users = User::where('role', 'user')->get();
        } else {
            $reservations = Reservation::with(['user', 'book'])
                ->where('user_id', $user->id)
                ->get();
            $users = collect([$user]);
        }
        
        $books = Book::where('is_valid', true)->get();
        $editReservation = null;

        if ($request->has('edit')) {
            $editReservation = Reservation::find($request->input('edit'));
        }

        return view('reservations.index', compact('reservations', 'books', 'users', 'editReservation'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $books = Book::where('is_valid', true)->get();
            $users = User::where('role', 'user')->get();
        } else {
            $books = Book::where('is_valid', true)->get();
            $users = collect([$user]);
        }

        // Pré-sélectionner le livre si book_id est fourni
        $selectedBookId = $request->get('book_id');

        return view('reservations.create', compact('books', 'users', 'selectedBookId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'date_reservation' => 'required|date',
            'date_retour_prev' => 'required|date|after:date_reservation',
        ]);

        $book = Book::findOrFail($request->book_id);
        if (!$book->is_valid) {
            return back()->with('error', 'Ce livre n\'est pas disponible pour la réservation.');
        }

        $userId = ($user->role === 'admin' && $request->has('user_id')) 
                ? $request->user_id 
                : $user->id;

        Reservation::create([
            'user_id' => $userId,
            'book_id' => $request->book_id,
            'date_reservation' => $request->date_reservation,
            'date_retour_prev' => $request->date_retour_prev,
            'statut' => 'en_attente',
        ]);

        return redirect()->route('reservations.index')->with('success', 'Réservation créée avec succès.');
    }

    public function edit(Reservation $reservation)
    {
        $user = Auth::user();
        
        if ($user->role === 'user' && $reservation->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $books = Book::where('is_valid', true)->get();
        
        if ($user->role === 'admin') {
            $users = User::where('role', 'user')->get();
        } else {
            $users = collect([$user]);
        }
        
        return view('reservations.edit', compact('reservation', 'books', 'users'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $user = Auth::user();

        if ($user->role === 'user' && $reservation->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Si c'est juste une mise à jour de statut par admin/modérateur
        if (($user->role === 'admin' || $user->role === 'moderator') && $request->has('statut') && !$request->has('book_id')) {
            $reservation->update([
                'statut' => $request->statut,
            ]);

            $message = 'Statut de la réservation mis à jour avec succès.';
            if ($request->statut === 'confirmee') {
                $message = 'Réservation confirmée avec succès.';
            } elseif ($request->statut === 'retourne') {
                $message = 'Livre marqué comme retourné.';
            }

            return redirect()->route('reservations.index')->with('success', $message);
        }

        // Mise à jour complète (par utilisateur ou admin)
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'date_reservation' => 'required|date',
            'date_retour_prev' => 'required|date|after:date_reservation',
            'date_retour_effectif' => 'nullable|date|after:date_reservation',
        ]);

        $userId = ($user->role === 'admin' && $request->has('user_id'))
                ? $request->user_id
                : $reservation->user_id;

        $statut = ($user->role === 'admin' || $user->role === 'moderator') && $request->has('statut')
                ? $request->statut
                : $reservation->statut;

        $dateRetourEffectif = $request->date_retour_effectif;
        if ($statut === 'retourne' && !$dateRetourEffectif) {
            $dateRetourEffectif = now();
        }

        $reservation->update([
            'user_id' => $userId,
            'book_id' => $request->book_id,
            'date_reservation' => $request->date_reservation,
            'date_retour_prev' => $request->date_retour_prev,
            'statut' => $statut,
            'date_retour_effectif' => $dateRetourEffectif,
        ]);

        return redirect()->route('reservations.index')->with('success', 'Réservation modifiée avec succès.');
    }

    public function destroy(Reservation $reservation)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Réservation supprimée avec succès.');
    }

    public function markReturned(Reservation $reservation)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' && $user->role !== 'moderator') {
            abort(403, 'Accès non autorisé.');
        }

        $reservation->update([
            'statut' => 'retourne',
            'date_retour_effectif' => now(),
        ]);

        return redirect()->route('reservations.index')->with('success', 'Livre marqué comme retourné.');
    }
}