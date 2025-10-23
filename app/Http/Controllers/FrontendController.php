<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class FrontendController extends Controller
{
    /**
     * Find which column to use on categories table for the category name.
     */
    protected function detectCategoryNameColumn(): string
    {
        $candidates = ['name', 'nom', 'titre', 'label'];

        foreach ($candidates as $col) {
            if (Schema::hasColumn('categories', $col)) {
                return $col;
            }
        }

        return 'name';
    }

    public function index(Request $request)
    {
        // Livres récents triés par date de modification
        $latestBooks = Book::where('is_valid', true)
            ->with('category')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Query pour tous les livres
        $allBooksQuery = Book::where('is_valid', true)
            ->with('category')
            ->orderBy('updated_at', 'desc');

        // Détecter la colonne de nom de catégorie
        $categoryNameColumn = $this->detectCategoryNameColumn();

        // Recherche par terme
        if ($request->has('q') && !empty($request->q)) {
            $search = trim($request->q);
            $allBooksQuery->where(function ($q) use ($search, $categoryNameColumn) {
                $q->where('auteur', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($catQ) use ($search, $categoryNameColumn) {
                      $catQ->where($categoryNameColumn, 'like', '%' . $search . '%');
                  });
            });
        }

        // Filtre par catégorie
        if ($request->has('category') && !empty($request->category)) {
            $allBooksQuery->where('category_id', $request->category);
        }

        // Tri des résultats
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $allBooksQuery->orderBy('updated_at', 'asc');
                    break;
                case 'title_asc':
                    $allBooksQuery->orderBy('titre', 'asc');
                    break;
                case 'title_desc':
                    $allBooksQuery->orderBy('titre', 'desc');
                    break;
                case 'author_asc':
                    $allBooksQuery->orderBy('auteur', 'asc');
                    break;
                case 'author_desc':
                    $allBooksQuery->orderBy('auteur', 'desc');
                    break;
                default:
                    $allBooksQuery->orderBy('updated_at', 'desc');
                    break;
            }
        }

        $perPage = $request->get('perPage', 12);
        $allBooks = $allBooksQuery->paginate($perPage);

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
     * API: Recherche avec filtres et tri
     */
    public function searchBooks(Request $request): JsonResponse
    {
        $query = Book::where('is_valid', true)
            ->with(['category'])
            ->orderBy('updated_at', 'desc');

        // Détecter la colonne de nom de catégorie
        $categoryNameColumn = $this->detectCategoryNameColumn();

        // Recherche par terme
        if ($request->has('q') && !empty($request->q)) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search, $categoryNameColumn) {
                $q->where('auteur', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($catQ) use ($search, $categoryNameColumn) {
                      $catQ->where($categoryNameColumn, 'like', '%' . $search . '%');
                  });
            });
        }

        // Filtre par catégorie
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        // Tri des résultats
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('updated_at', 'asc');
                    break;
                case 'title_asc':
                    $query->orderBy('titre', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('titre', 'desc');
                    break;
                case 'author_asc':
                    $query->orderBy('auteur', 'asc');
                    break;
                case 'author_desc':
                    $query->orderBy('auteur', 'desc');
                    break;
                default:
                    $query->orderBy('updated_at', 'desc');
                    break;
            }
        }

        $perPage = $request->get('perPage', 12);
        $page = (int) $request->get('page', 1);
        $books = $query->paginate($perPage, ['*'], 'page', $page);

        $items = $books->map(function ($b) {
            // Sélection robuste du nom de catégorie
            $catName = null;
            if ($b->category) {
                $catName = $b->category->name ?? $b->category->nom ?? $b->category->titre ?? $b->category->label ?? null;
            }

            return [
                'id' => $b->id,
                'titre' => $b->titre,
                'auteur' => $b->auteur,
                'cover_image' => $b->cover_image,
                'description' => $b->description ? Str::limit($b->description, 220) : null,
                'description_full' => $b->description,
                'updated_at' => $b->updated_at ? $b->updated_at->toDateTimeString() : null,
                'category' => $b->category ? ['id' => $b->category->id, 'name' => $catName] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items->values(),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]
        ]);
    }

    /**
     * API: Suggestions pour autocomplete
     */
    public function suggestions(Request $request): JsonResponse
    {
        $q = (string) $request->get('q', '');
        $q = trim($q);

        $categoryNameColumn = $this->detectCategoryNameColumn();

        $authors = [];
        $categories = [];

        if ($q !== '') {
            // Suggestions d'auteurs
            $authors = Book::where('is_valid', true)
                ->where('auteur', 'like', '%' . $q . '%')
                ->distinct()
                ->limit(8)
                ->pluck('auteur')
                ->filter()
                ->values()
                ->all();

            // Suggestions de catégories
            $categories = Category::where($categoryNameColumn, 'like', '%' . $q . '%')
                ->limit(8)
                ->pluck($categoryNameColumn)
                ->filter()
                ->values()
                ->all();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'authors' => $authors,
                'categories' => $categories,
            ]
        ]);
    }
}