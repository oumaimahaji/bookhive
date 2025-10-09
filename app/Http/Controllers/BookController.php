<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // ✅ Ajout de la façade Auth
use Barryvdh\DomPDF\Facade\Pdf;

class BookController extends Controller
{
    /**
     * Affiche la liste de tous les livres
     */
    public function index(Request $request)
    {
        $query = Book::with('category')->orderBy('titre');

        // Recherche par auteur
        if ($request->filled('author')) {
            $query->where('auteur', 'like', "%{$request->author}%");
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->paginate(10); // Pagination

        $categories = Category::all();
        $editBook = null;

        if ($request->has('edit')) {
            $editBook = Book::find($request->input('edit'));
        }

        return view('books.index', compact('books', 'categories', 'editBook'));
    }


    /**
     * Affiche le formulaire de création d’un livre
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
        // ✅ Validation des champs du formulaire
        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string',
            'is_valid' => 'sometimes|boolean',
            'pdf' => 'nullable|mimes:pdf|max:2048', // max 2 Mo
        ]);

        // ✅ Création de la base du livre
        $book = new Book([
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'is_valid' => $request->has('is_valid') ? 1 : 0,
            'user_id' => Auth::id(), // qui a ajouté le livre
        ]);

        // ✅ Si un fichier PDF est uploadé et que l’utilisateur est admin
        if (Auth::check() && Auth::user()->role === 'admin' && $request->hasFile('pdf')) {
            // Stocke le fichier dans storage/app/public/books
            $path = $request->file('pdf')->store('books', 'public');
            $book->pdf_path = $path;
        }

        $book->save();

        return redirect()->route('books.index')->with('success', 'Livre ajouté avec succès');
    }

    /**
     * Affiche le formulaire d’édition d’un livre
     */
    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Met à jour les informations d’un livre existant
     */
    public function update(Request $request, Book $book)
    {
        // ✅ Validation des données
        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string',
            'is_valid' => 'sometimes|boolean',
            'pdf' => 'nullable|mimes:pdf|max:2048',
        ]);

        // ✅ Mise à jour des champs
        $book->update([
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'is_valid' => $request->has('is_valid') ? 1 : 0,
        ]);

        // ✅ Si un nouveau PDF est ajouté
        if (Auth::check() && Auth::user()->role === 'admin' && $request->hasFile('pdf')) {
            // Supprimer l'ancien PDF s'il existe
            if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
                Storage::disk('public')->delete($book->pdf_path);
            }

            // Stocker le nouveau fichier
            $path = $request->file('pdf')->store('books', 'public');
            $book->update(['pdf_path' => $path]);
        }

        return redirect()->route('books.index')->with('success', 'Livre mis à jour avec succès');
    }

    /**
     * Supprime un livre
     */
    public function destroy(Book $book)
    {
        // Supprimer le fichier PDF associé s’il existe
        if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
            Storage::disk('public')->delete($book->pdf_path);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Livre supprimé avec succès');
    }

    /**
     * Télécharge le PDF d’un livre
     */
    public function downloadPdf(Book $book)
    {
        if (!$book->pdf_path || !Storage::disk('public')->exists($book->pdf_path)) {
            return redirect()->back()->with('error', 'Aucun PDF disponible pour ce livre.');
        }

        return response()->download(storage_path('app/public/' . $book->pdf_path));
    }





    public function export(Request $request)
    {
        $query = Book::with('category')->orderBy('titre');

        if ($request->filled('author')) {
            $query->where('auteur', 'like', "%{$request->author}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->get();

        $pdf = Pdf::loadView('books.pdf', compact('books'));
        return $pdf->download('books.pdf');
    }
}
