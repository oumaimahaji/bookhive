<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use App\Models\User;
use App\Models\Reservation;
use App\Services\TwinwordSentimentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ReviewController extends Controller
{
    protected $sentimentService;

    public function __construct(TwinwordSentimentService $sentimentService)
    {
        $this->sentimentService = $sentimentService;
    }

    /**
     * 🗂️ Afficher la liste des avis
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'moderator'])) {
            $reviews = Review::with(['user', 'book'])->get();
        } else {
            $reviews = Review::with(['user', 'book'])
                ->where('user_id', $user->id)
                ->get();
        }

        $books = Book::where('is_valid', true)->get();
        $editReview = $request->has('edit') ? Review::find($request->input('edit')) : null;

        return view('reviews.index', compact('reviews', 'books', 'editReview'));
    }

    /**
     * 📝 Formulaire de création d’un nouvel avis
     */
    public function create()
    {
        $user = Auth::user();

        // L’utilisateur ne peut commenter que les livres qu’il a réservés et retournés
        $reservedBookIds = Reservation::where('user_id', $user->id)
            ->where('statut', 'retourne')
            ->pluck('book_id');

        $books = Book::whereIn('id', $reservedBookIds)
            ->where('is_valid', true)
            ->get();

        return view('reviews.create', compact('books'));
    }

    /**
     * 💾 Enregistrer un avis
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:1000',
        ]);

        // Vérifie si l’utilisateur a réservé ce livre avant de commenter
        if ($user->role === 'user') {
            $hasReserved = Reservation::where('user_id', $user->id)
                ->where('book_id', $request->book_id)
                ->where('statut', 'retourne')
                ->exists();

            if (!$hasReserved) {
                return back()->with('error', 'Vous devez avoir réservé et retourné ce livre pour laisser un avis.');
            }
        }

        // 🔍 Analyse de sentiment
        $sentimentScore = $this->sentimentService->getSentimentScore($request->commentaire);
        $sentimentType = $this->sentimentService->getSentimentType($request->commentaire);

        Review::create([
            'user_id' => $user->id,
            'book_id' => $request->book_id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'date' => now(),
            'sentiment_score' => $sentimentScore,
            'sentiment_type' => $sentimentType,
        ]);

        return redirect()->route('reviews.index')->with('success', 'Avis créé avec succès.');
    }

    /**
     * ✏️ Formulaire d’édition
     */
    public function edit(Review $review)
    {
        $user = Auth::user();

        if ($user->role === 'user' && $review->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        if (in_array($user->role, ['admin', 'moderator'])) {
            $books = Book::where('is_valid', true)->get();
        } else {
            $reservedBookIds = Reservation::where('user_id', $user->id)
                ->where('statut', 'retourne')
                ->pluck('book_id');
            $books = Book::whereIn('id', $reservedBookIds)
                ->where('is_valid', true)
                ->get();
        }

        return view('reviews.edit', compact('review', 'books'));
    }

    /**
     * 🔄 Mettre à jour un avis
     */
    public function update(Request $request, Review $review)
    {
        $user = Auth::user();

        if ($user->role === 'user' && $review->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:1000',
            'date' => 'required|date',
        ]);

        if ($user->role === 'user') {
            $hasReserved = Reservation::where('user_id', $user->id)
                ->where('book_id', $request->book_id)
                ->where('statut', 'retourne')
                ->exists();

            if (!$hasReserved) {
                return back()->with('error', 'Vous devez avoir réservé et retourné ce livre pour modifier cet avis.');
            }
        }

        // 🧠 Nouvelle analyse IA du commentaire
        $sentimentScore = $this->sentimentService->getSentimentScore($request->commentaire);
        $sentimentType = $this->sentimentService->getSentimentType($request->commentaire);

        $review->update([
            'book_id' => $request->book_id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'date' => $request->date,
            'sentiment_score' => $sentimentScore,
            'sentiment_type' => $sentimentType,
        ]);

        return redirect()->route('reviews.index')->with('success', 'Avis modifié avec succès.');
    }

    /**
     * 🗑️ Supprimer un avis
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();

        if ($user->role === 'user' && $review->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $review->delete();

        return redirect()->route('reviews.index')->with('success', 'Avis supprimé avec succès.');
    }

    /**
     * 🤖 Résumé automatique des avis d’un livre via IA
     */
    public function summarizeBookReviews($bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            return redirect()->back()->with('error', 'Livre introuvable.');
        }

        $reviews = Review::where('book_id', $bookId)->pluck('commentaire')->toArray();

        if (empty($reviews)) {
            return view('reviews.summary', [
                'book' => $book,
                'summary' => 'Aucun avis trouvé pour ce livre.'
            ]);
        }

        try {
            // 🔗 Appel API vers ton microservice IA
            $response = Http::post(env('AI_SUMMARY_URL'), [
                'reviews' => $reviews
            ]);

            if ($response->successful()) {
                $summary = $response->json()['summary'] ?? 'Résumé non disponible.';
            } else {
                $summary = 'Erreur : impossible de générer le résumé.';
            }
        } catch (\Exception $e) {
            $summary = 'Erreur de connexion à l’IA : ' . $e->getMessage();
        }

        return view('reviews.summary', compact('summary', 'book'));
    }
}

