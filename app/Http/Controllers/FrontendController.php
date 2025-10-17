<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;

class FrontendController extends Controller
{
    public function index()
    {
        // Les 5 DERNIERS livres pour la section "Nouveautés"
        $latestBooks = Book::where('is_valid', true)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->take(5) // Seulement 5 livres pour les nouveautés
            ->get();

        // TOUS les livres paginés pour la section "Toute la collection"
        $allBooks = Book::where('is_valid', true)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(12); // 12 livres par page avec pagination

        $categories = Category::withCount(['books' => function ($query) {
            $query->where('is_valid', true);
        }])->get();

        $stats = [
            'totalBooks' => Book::where('is_valid', true)->count(),
            'totalCategories' => Category::count(),
            'totalAuthors' => Book::where('is_valid', true)->distinct('auteur')->count('auteur'),
        ];

        return view('frontend.home', compact('latestBooks', 'allBooks', 'categories', 'stats'));
    }

    public function showBook(Book $book)
    {
        // Vérifier que le livre est validé
        if (!$book->is_valid) {
            abort(404);
        }

        // Livres similaires
        $relatedBooks = Book::where('is_valid', true)
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->take(4)
            ->get();

        return view('frontend.books.show', compact('book', 'relatedBooks'));
    }

    public function about()
    {
        return view('frontend.about');
    }

    public function contact()
    {
        return view('frontend.contact');
    }
}