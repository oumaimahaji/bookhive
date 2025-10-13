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

class BookController extends Controller
{
    protected $duplicateDetector;

    public function __construct()
    {
        $this->duplicateDetector = new DuplicateDetectorService();
    }

    /**
     * Affiche la liste de tous les livres
     */
    public function index(Request $request)
    {
        $query = Book::with('category');

        // RECHERCHE AVANCÉE
        $search_query = $request->get('search', '');
        if ($search_query) {
            $query->where(function ($q) use ($search_query) {
                $q->where('titre', 'like', "%{$search_query}%")
                    ->orWhere('auteur', 'like', "%{$search_query}%")
                    ->orWhere('description', 'like', "%{$search_query}%")
                    ->orWhere('type', 'like', "%{$search_query}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search_query) {
                        $categoryQuery->where('nom', 'like', "%{$search_query}%");
                    });
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

        // PAGINATION AVANCÉE
        $per_page = $request->get('per_page', 10);
        $allowed_per_page = [5, 10, 25, 50, 100];
        if (!in_array($per_page, $allowed_per_page)) {
            $per_page = 10;
        }

        $books = $query->paginate($per_page);
        $books->appends($request->all());

        $categories = Category::all();
        $editBook = null;

        // CORRECTION : Récupérer le livre à éditer SI le paramètre edit existe
        if ($request->has('edit')) {
            $editBook = Book::find($request->input('edit'));
            // Si le livre n'existe pas, rediriger vers la liste
            if (!$editBook) {
                return redirect()->route('books.index')->with('error', 'Book not found.');
            }
        }

        return view('books.index', compact('books', 'categories', 'editBook', 'search_query', 'per_page', 'sort_by', 'sort_order'));
    }

    /**
     * Affiche le formulaire de création d'un livre
     */
    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau livre dans la base de données
     */
    public function store(Request $request)
    {
        // DEBUG: Vérifier les données reçues
        Log::info('=== DÉBUT STORE BOOK ===');
        Log::info('Données reçues:', $request->all());
        Log::info('Force create reçu:', ['value' => $request->force_create]);

        // VALIDATION DES DONNÉES
        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string',
            'is_valid' => 'sometimes|boolean',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        // 🎯 DÉTECTION IA DES DOUBLONS
        $duplicates = $this->duplicateDetector->findPotentialDuplicates(
            $request->titre,
            $request->auteur
        );

        // CORRECTION DÉFINITIVE : Vérification robuste de la case à cocher
        $forceCreation = $request->has('force_create') && $request->force_create == '1';

        Log::info('Résultat détection:', [
            'doublons' => !empty($duplicates),
            'force_create' => $forceCreation,
            'count_doublons' => count($duplicates)
        ]);

        // Si doublons détectés ET que l'utilisateur n'a PAS coché "forcer"
        if (!empty($duplicates) && !$forceCreation) {
            Log::info('🚫 REDIRECTION: Doublons détectés sans forçage');
            $categories = Category::all();
            return view('books.create', compact('categories', 'duplicates'))
                ->with('input', $request->all());
        }

        Log::info('✅ CRÉATION: Pas de doublons OU création forcée');

        // Si pas de doublons OU création forcée, procéder à la création
        // Upload de la photo de couverture
        $coverPath = $request->file('cover_image')->store('covers', 'public');

        // Création du livre
        $book = new Book([
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'is_valid' => $request->has('is_valid') ? 1 : 0,
            'user_id' => Auth::id(),
            'cover_image' => $coverPath,
        ]);

        // Gestion du PDF pour admin
        if (Auth::check() && Auth::user()->role === 'admin' && $request->hasFile('pdf')) {
            $path = $request->file('pdf')->store('books', 'public');
            $book->pdf_path = $path;
        }

        $book->save();

        Log::info('✅ LIVRE CRÉÉ: ' . $book->titre);

        // Message différent si création forcée
        if (!empty($duplicates) && $forceCreation) {
            Log::info('📢 MESSAGE: Création forcée avec doublons');
            return redirect()->route('books.index')
                ->with('warning', 'Livre ajouté avec succès - Attention: doublons détectés mais création forcée');
        }

        Log::info('📢 MESSAGE: Création normale');
        return redirect()->route('books.index')
            ->with('success', 'Livre ajouté avec succès');
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
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string',
            'is_valid' => 'sometimes|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        // 🎯 DÉTECTION IA DES DOUBLONS (optionnelle - décommentez si nécessaire)
        /*
        $duplicates = $this->duplicateDetector->findPotentialDuplicates(
            $request->titre,
            $request->auteur,
            $book->id
        );

        $forceCreation = $request->has('force_create') && $request->force_create == '1';

        // Si doublons détectés ET que l'utilisateur n'a PAS coché "forcer"
        if (!empty($duplicates) && !$forceCreation) {
            $categories = Category::all();
            return redirect()->route('books.index', ['edit' => $book->id])
                ->with('duplicates', $duplicates)
                ->withInput();
        }
        */

        // Mise à jour si pas de doublons ou création forcée
        $book->update([
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'is_valid' => $request->has('is_valid') ? 1 : 0,
        ]);

        // Gestion des fichiers
        if ($request->hasFile('cover_image')) {
            // Supprimer l'ancienne photo si elle existe
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }

            // Stocker la nouvelle photo
            $coverPath = $request->file('cover_image')->store('covers', 'public');
            $book->update(['cover_image' => $coverPath]);
        }

        // Si un nouveau PDF est ajouté
        if (Auth::check() && Auth::user()->role === 'admin' && $request->hasFile('pdf')) {
            // Supprimer l'ancien PDF s'il existe
            if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
                Storage::disk('public')->delete($book->pdf_path);
            }

            // Stocker le nouveau fichier
            $path = $request->file('pdf')->store('books', 'public');
            $book->update(['pdf_path' => $path]);
        }

        // CORRECTION : Rediriger vers la liste SANS le paramètre edit
        return redirect()->route('books.index')->with('success', 'Book updated successfully');
    }

    /**
     * Supprime un livre
     */
    public function destroy(Book $book)
    {
        // Supprimer le fichier PDF associé s'il existe
        if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
            Storage::disk('public')->delete($book->pdf_path);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Livre supprimé avec succès');
    }

    /**
     * Télécharge le PDF d'un livre
     */
    public function downloadPdf(Book $book)
    {
        if (!$book->pdf_path || !Storage::disk('public')->exists($book->pdf_path)) {
            return redirect()->back()->with('error', 'Aucun PDF disponible pour ce livre.');
        }

        return response()->download(storage_path('app/public/' . $book->pdf_path));
    }

    /**
     * Exporte les livres en PDF
     */
    public function export(Request $request)
    {
        $query = Book::with('category');

        // Appliquer la même recherche que dans index()
        $search_query = $request->get('search', '');
        if ($search_query) {
            $query->where(function ($q) use ($search_query) {
                $q->where('titre', 'like', "%{$search_query}%")
                    ->orWhere('auteur', 'like', "%{$search_query}%")
                    ->orWhere('description', 'like', "%{$search_query}%")
                    ->orWhere('type', 'like', "%{$search_query}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search_query) {
                        $categoryQuery->where('nom', 'like', "%{$search_query}%");
                    });
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
