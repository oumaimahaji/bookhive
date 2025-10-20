<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\DuplicateDetectorService;
use App\Services\BookAIService;

class BookController extends Controller
{
    protected $duplicateDetector;
    protected $aiService;

    public function __construct()
    {
        $this->duplicateDetector = new DuplicateDetectorService();
        $this->aiService = new BookAIService();
    }

    /**
     * Affiche la liste de tous les livres
     */
    public function index(Request $request)
    {
        $query = Book::with('category');

        // RECHERCHE PAR TITRE ET AUTEUR UNIQUEMENT
        $search_query = $request->get('search', '');
        if ($search_query) {
            $query->where(function ($q) use ($search_query) {
                $q->where('titre', 'like', "%{$search_query}%")
                    ->orWhere('auteur', 'like', "%{$search_query}%");
            });
        }

        // TRI AVANCÉ
        $sort_by = $request->get('sort_by', 'titre');
        $sort_order = $request->get('sort_order', 'asc');

        $allowed_sort_columns = ['titre', 'auteur', 'created_at', 'updated_at', 'is_valid'];
        if (in_array($sort_by, $allowed_sort_columns)) {
            $query->orderBy($sort_by, $sort_order);
        } else {
            $query->orderBy('titre', 'asc');
        }

        // PAGINATION - CORRIGÉ : 5 livres par page
        $books = $query->paginate(5);
        
        // Garder les paramètres de recherche dans la pagination
        if ($request->hasAny(['search', 'sort_by', 'sort_order'])) {
            $books->appends($request->only(['search', 'sort_by', 'sort_order']));
        }

        $categories = Category::all();
        $editBook = null;

        // Récupérer le livre à éditer SI le paramètre edit existe
        if ($request->has('edit')) {
            $editBook = Book::find($request->input('edit'));
            if (!$editBook) {
                return redirect()->route('books.index')->with('error', 'Book not found.');
            }
        }

        return view('books.index', compact('books', 'categories', 'editBook', 'search_query', 'sort_by', 'sort_order'));
    }

    /**
     * Affiche le formulaire de création d'un livre
     */
    public function create(Request $request)
    {
        $categories = Category::all();
        
        // Vérifier si on a un titre pour les recommandations IA
        $aiData = [];
        $input = [];
        
        if ($request->has('ai_title')) {
            $title = $request->get('ai_title');
            $author = $request->get('ai_author', '');
            
            $aiData = $this->aiService->getAIRecommendations($title, $author);
            
            // Pré-remplir automatiquement les champs
            $input = [
                'titre' => $title,
                'auteur' => $author,
                'description' => $aiData['generated_description'] ?? ''
            ];
            
            // Pré-remplir la catégorie seulement si elle existe
            if (isset($aiData['recommended_category']['id'])) {
                $input['category_id'] = $aiData['recommended_category']['id'];
            }
        }
        
        return view('books.create', compact('categories', 'aiData', 'input'));
    }

    /**
     * Obtenir les recommandations IA via AJAX
     */
    public function getAIRecommendations(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255'
        ]);

        try {
            $recommendations = $this->aiService->getAIRecommendations(
                $request->title,
                $request->author
            );

            return response()->json($recommendations);

        } catch (\Exception $e) {
            Log::error('Erreur API recommandation IA:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'error' => 'Impossible d\'obtenir les recommandations IA'
            ], 500);
        }
    }

    /**
     * Enregistre un nouveau livre dans la base de données
     */
    public function store(Request $request)
    {
        // DEBUG DÉTAILLÉ
        Log::info('=== DÉBUT STORE BOOK ===');
        Log::info('Données reçues:', $request->except(['cover_image', 'pdf']));

        // VALIDATION DES DONNÉES - CORRIGÉ : cover_image EST required
        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string|max:100',
            'is_valid' => 'sometimes|boolean',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // REQUIS
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        // DÉTECTION IA DES DOUBLONS
        $duplicates = $this->duplicateDetector->findPotentialDuplicates(
            $request->titre,
            $request->auteur
        );

        $forceCreation = $request->has('force_create') && $request->force_create == '1';

        // Si doublons détectés ET que l'utilisateur n'a PAS coché "forcer"
        if (!empty($duplicates) && !$forceCreation) {
            $categories = Category::all();
            return view('books.create', compact('categories', 'duplicates'))
                ->with('input', $request->all());
        }

        // UPLOAD DE LA PHOTO DE COUVERTURE - MAINTENANT OBLIGATOIRE
        Log::info('=== UPLOAD COVER IMAGE ===');
        $coverPath = null;
        
        // Cette partie est maintenant garantie d'avoir un fichier grâce à la validation
        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            $file = $request->file('cover_image');

            // Générer un nom de fichier unique
            $fileName = 'book_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $coverPath = $file->storeAs('covers', $fileName, 'public');

            Log::info('Cover upload details:', [
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $fileName,
                'storage_path' => $coverPath,
                'file_exists' => Storage::disk('public')->exists($coverPath),
                'file_size' => $file->getSize()
            ]);
        } else {
            // Normalement on ne devrait jamais arriver ici grâce à la validation
            Log::error('Cover image upload failed but validation passed');
            return back()->with('error', 'Erreur lors du téléchargement de l\'image de couverture.')->withInput();
        }

        // CRÉATION DU LIVRE - cover_image est maintenant toujours présent
        $bookData = [
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'is_valid' => $request->has('is_valid') ? 1 : 0,
            'user_id' => Auth::id(),
            'cover_image' => $coverPath, // Toujours présent
        ];

        $book = new Book($bookData);

        // GESTION DU PDF POUR ADMIN
        if (Auth::check() && Auth::user()->role === 'admin' && $request->hasFile('pdf')) {
            $pdfFile = $request->file('pdf');
            if ($pdfFile->isValid()) {
                $pdfFileName = 'book_pdf_' . time() . '_' . uniqid() . '.pdf';
                $pdfPath = $pdfFile->storeAs('books', $pdfFileName, 'public');
                $book->pdf_path = $pdfPath;

                Log::info('PDF upload details:', [
                    'original_name' => $pdfFile->getClientOriginalName(),
                    'stored_name' => $pdfFileName,
                    'storage_path' => $pdfPath
                ]);
            }
        }

        $book->save();

        Log::info('✅ LIVRE CRÉÉ: ' . $book->titre . ' (ID: ' . $book->id . ')');

        // Message de confirmation
        $message = !empty($duplicates) && $forceCreation
            ? 'Livre ajouté avec succès - Attention: doublons détectés mais création forcée'
            : 'Livre ajouté avec succès';

        return redirect()->route('books.index')->with('success', $message);
    }

    /**
     * Affiche le formulaire d'édition d'un livre (version séparée)
     */
    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Met à jour les informations d'un livre existant
     */
    public function update(Request $request, Book $book)
    {
        // VALIDATION
        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string|max:100',
            'is_valid' => 'sometimes|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        Log::info('=== UPDATE BOOK ===', ['book_id' => $book->id]);

        // Mise à jour des champs de base
        $book->update([
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'is_valid' => $request->has('is_valid') ? 1 : 0,
        ]);

        // GESTION DE LA NOUVELLE IMAGE DE COUVERTURE
        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            Log::info('Updating cover image for book: ' . $book->id);

            // Supprimer l'ancienne photo si elle existe
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
                Log::info('Old cover deleted: ' . $book->cover_image);
            }

            // Stocker la nouvelle photo
            $file = $request->file('cover_image');
            $fileName = 'book_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $coverPath = $file->storeAs('covers', $fileName, 'public');

            $book->update(['cover_image' => $coverPath]);

            Log::info('New cover stored:', [
                'path' => $coverPath,
                'file_exists' => Storage::disk('public')->exists($coverPath)
            ]);
        }

        // GESTION DU NOUVEAU PDF POUR ADMIN
        if (Auth::check() && Auth::user()->role === 'admin' && $request->hasFile('pdf')) {
            $pdfFile = $request->file('pdf');
            if ($pdfFile->isValid()) {
                // Supprimer l'ancien PDF s'il existe
                if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
                    Storage::disk('public')->delete($book->pdf_path);
                }

                // Stocker le nouveau fichier
                $pdfFileName = 'book_pdf_' . time() . '_' . uniqid() . '.pdf';
                $pdfPath = $pdfFile->storeAs('books', $pdfFileName, 'public');
                $book->update(['pdf_path' => $pdfPath]);
            }
        }

        Log::info('✅ BOOK UPDATED: ' . $book->titre);
        
        // Redirection avec les paramètres de recherche conservés
        $redirectParams = [];
        if ($request->has('search')) $redirectParams['search'] = $request->search;
        if ($request->has('sort_by')) $redirectParams['sort_by'] = $request->sort_by;
        if ($request->has('sort_order')) $redirectParams['sort_order'] = $request->sort_order;
        
        return redirect()->route('books.index', $redirectParams)->with('success', 'Livre mis à jour avec succès');
    }

    /**
     * Supprime un livre
     */
    public function destroy(Book $book)
    {
        Log::info('=== DELETE BOOK ===', ['book_id' => $book->id, 'title' => $book->titre]);

        // Supprimer le fichier PDF associé s'il existe
        if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
            Storage::disk('public')->delete($book->pdf_path);
            Log::info('PDF deleted: ' . $book->pdf_path);
        }

        // Supprimer l'image de couverture si elle existe
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
            Log::info('Cover image deleted: ' . $book->cover_image);
        }

        $bookTitle = $book->titre;
        $book->delete();

        Log::info('✅ BOOK DELETED: ' . $bookTitle);
        
        // Redirection avec conservation des paramètres
        $redirectParams = request()->only(['search', 'sort_by', 'sort_order']);
        return redirect()->route('books.index', $redirectParams)->with('success', 'Livre "' . $bookTitle . '" supprimé avec succès');
    }

    /**
     * Télécharge le PDF d'un livre
     */
    public function download(Book $book)
    {
        if (!$book->pdf_path || !Storage::disk('public')->exists($book->pdf_path)) {
            return redirect()->back()->with('error', 'Aucun PDF disponible pour ce livre.');
        }

        return response()->download(storage_path('app/public/' . $book->pdf_path));
    }

    /**
     * Exporte les livres en PDF (fonctionnalité optionnelle)
     */
    public function export(Request $request)
    {
        $query = Book::with('category');

        // Appliquer la même recherche que dans index()
        $search_query = $request->get('search', '');
        if ($search_query) {
            $query->where(function ($q) use ($search_query) {
                $q->where('titre', 'like', "%{$search_query}%")
                    ->orWhere('auteur', 'like', "%{$search_query}%");
            });
        }

        // Appliquer le même tri que dans index()
        $sort_by = $request->get('sort_by', 'titre');
        $sort_order = $request->get('sort_order', 'asc');

        $allowed_sort_columns = ['titre', 'auteur', 'created_at', 'updated_at', 'is_valid'];
        if (in_array($sort_by, $allowed_sort_columns)) {
            $query->orderBy($sort_by, $sort_order);
        }

        // Si un book_id spécifique est fourni, exporter seulement ce livre
        if ($request->filled('book_id')) {
            $query->where('id', $request->book_id);
        }

        $books = $query->get();

        $pdf = Pdf::loadView('books.pdf', compact('books'));

        // Nom du fichier basé sur si c'est un livre spécifique ou tous les livres
        $filename = $request->filled('book_id') ? 'book_' . $request->book_id . '.pdf' : 'books.pdf';

        return $pdf->download($filename);
    }
}