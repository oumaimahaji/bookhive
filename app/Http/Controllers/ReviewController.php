<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use App\Models\User;
use App\Models\Reservation;
<<<<<<< HEAD
=======
use App\Services\TwinwordSentimentService;
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
<<<<<<< HEAD
    public function index(Request $request)
    {
        $user = Auth::user();
        
=======
    protected $sentimentService;

    public function __construct(TwinwordSentimentService $sentimentService)
    {
        $this->sentimentService = $sentimentService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        if ($user->role === 'admin' || $user->role === 'moderator') {
            $reviews = Review::with(['user', 'book'])->get();
        } else {
            $reviews = Review::with(['user', 'book'])
                ->where('user_id', $user->id)
                ->get();
        }
<<<<<<< HEAD
        
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        $books = Book::where('is_valid', true)->get();
        $editReview = null;

        if ($request->has('edit')) {
            $editReview = Review::find($request->input('edit'));
        }

        return view('reviews.index', compact('reviews', 'books', 'editReview'));
    }

    public function create()
    {
        $user = Auth::user();
<<<<<<< HEAD
        
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        // Récupérer les livres que l'utilisateur a réservés et retournés
        $reservedBookIds = Reservation::where('user_id', $user->id)
            ->where('statut', 'retourne')
            ->pluck('book_id');
<<<<<<< HEAD
        
        $books = Book::whereIn('id', $reservedBookIds)->where('is_valid', true)->get();
        
=======

        $books = Book::whereIn('id', $reservedBookIds)->where('is_valid', true)->get();

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        return view('reviews.create', compact('books'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:1000',
            'date' => 'required|date',
        ]);

<<<<<<< HEAD
        // Vérifier si l'utilisateur a réservé ce livre (pour les users normaux)
=======
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        if ($user->role === 'user') {
            $hasReserved = Reservation::where('user_id', $user->id)
                ->where('book_id', $request->book_id)
                ->where('statut', 'retourne')
                ->exists();
<<<<<<< HEAD
            
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
            if (!$hasReserved) {
                return back()->with('error', 'Vous devez avoir réservé et retourné ce livre pour laisser un avis.');
            }
        }

<<<<<<< HEAD
        Review::create([
            'user_id' => $user->id, // Toujours l'utilisateur connecté
=======
        // Analyze sentiment
        $sentimentScore = $this->sentimentService->getSentimentScore($request->commentaire);
        $sentimentType = $this->sentimentService->getSentimentType($request->commentaire);

        Review::create([
            'user_id' => $user->id,
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
            'book_id' => $request->book_id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'date' => $request->date,
<<<<<<< HEAD
=======
            'sentiment_score' => $sentimentScore,
            'sentiment_type' => $sentimentType,
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        ]);

        return redirect()->route('reviews.index')->with('success', 'Avis créé avec succès.');
    }

    public function edit(Review $review)
    {
        $user = Auth::user();
<<<<<<< HEAD
        
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        // Vérifier les permissions
        if ($user->role === 'user' && $review->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Récupérer les livres disponibles pour l'édition
        if ($user->role === 'admin' || $user->role === 'moderator') {
            $books = Book::where('is_valid', true)->get();
        } else {
            // Pour les users, seulement les livres qu'ils ont réservés
            $reservedBookIds = Reservation::where('user_id', $user->id)
                ->where('statut', 'retourne')
                ->pluck('book_id');
            $books = Book::whereIn('id', $reservedBookIds)->where('is_valid', true)->get();
        }
<<<<<<< HEAD
        
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        return view('reviews.edit', compact('review', 'books'));
    }

    public function update(Request $request, Review $review)
    {
        $user = Auth::user();
<<<<<<< HEAD
        
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        // Vérifier les permissions
        if ($user->role === 'user' && $review->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:1000',
            'date' => 'required|date',
        ]);

        // Pour les users, vérifier qu'ils peuvent modifier ce livre
        if ($user->role === 'user') {
            $hasReserved = Reservation::where('user_id', $user->id)
                ->where('book_id', $request->book_id)
                ->where('statut', 'retourne')
                ->exists();
<<<<<<< HEAD
            
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
            if (!$hasReserved) {
                return back()->with('error', 'Vous devez avoir réservé et retourné ce livre pour modifier cet avis.');
            }
        }

<<<<<<< HEAD
=======
        // Analyze sentiment
        $sentimentScore = $this->sentimentService->getSentimentScore($request->commentaire);
        $sentimentType = $this->sentimentService->getSentimentType($request->commentaire);

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        $review->update([
            'book_id' => $request->book_id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'date' => $request->date,
<<<<<<< HEAD
=======
            'sentiment_score' => $sentimentScore,
            'sentiment_type' => $sentimentType,
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        ]);

        return redirect()->route('reviews.index')->with('success', 'Avis modifié avec succès.');
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();
<<<<<<< HEAD
        
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
        // Vérifier les permissions
        if ($user->role === 'user' && $review->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Avis supprimé avec succès.');
    }
}