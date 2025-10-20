<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
        // Livres récents triés par date de modification
        $latestBooks = Book::where('is_valid', true)
            ->with('category')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Query pour tous les livres avec recherche par auteur
        $allBooksQuery = Book::where('is_valid', true)
            ->with('category')
            ->orderBy('updated_at', 'desc');

        // Recherche par auteur si le paramètre est présent
        if ($request->has('author') && !empty($request->author)) {
            $authorName = trim($request->author);
            $allBooksQuery->where('auteur', 'like', '%' . $authorName . '%');
        }

        $allBooks = $allBooksQuery->paginate(12);

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
        if (!$book->is_valid) {
            abort(404);
        }

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

    /**
     * API: Recherche par auteur avec pagination
     */
    public function searchBooks(Request $request): JsonResponse
    {
        $query = Book::where('is_valid', true)
            ->with(['category'])
            ->orderBy('updated_at', 'desc');

        if ($request->has('author') && !empty($request->author)) {
            $authorName = trim($request->author);
            $query->where('auteur', 'like', '%' . $authorName . '%');
        }

        $perPage = 5;
        $books = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $books->items(),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]
        ]);
    }
}
