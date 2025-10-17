<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // ✅ Affiche la liste des catégories (avec recherche, tri et pagination avancées)
    public function index(Request $request)
    {
        $query = Category::withCount('books');

        // RECHERCHE PAR NOM
        $search_query = $request->get('search', '');
        if ($search_query) {
            $query->where('nom', 'like', "%{$search_query}%");
        }

        // TRI AVANCÉ
        $sort_by = $request->get('sort_by', 'nom');
        $sort_order = $request->get('sort_order', 'asc');

        $allowed_sort_columns = ['nom', 'books_count', 'created_at'];
        if (in_array($sort_by, $allowed_sort_columns)) {
            // Gestion spéciale pour le tri par count de relations
            if ($sort_by === 'books_count') {
                $query->orderBy('books_count', $sort_order);
            } else {
                $query->orderBy($sort_by, $sort_order);
            }
        } else {
            $query->orderBy('nom', 'asc');
        }

        // PAGINATION AVANCÉE - 5 CATÉGORIES PAR PAGE
        $per_page = 5;
        $categories = $query->paginate($per_page);
        $categories->appends($request->all());

        $editCategory = null;
        if ($request->has('edit')) {
            $editCategory = Category::find($request->input('edit'));
        }

        return view('categories.index', compact('categories', 'editCategory', 'search_query', 'per_page', 'sort_by', 'sort_order'));
    }

    // ✅ Formulaire pour créer une catégorie
    public function create()
    {
        return view('categories.create');
    }

    // ✅ Stocke une nouvelle catégorie
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès');
    }

    // ✅ Formulaire pour éditer une catégorie
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // ✅ Met à jour une catégorie
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès');
    }

    // ✅ Supprime une catégorie
    public function destroy(Category $category)
    {
        // Vérifier si la catégorie a des livres avant de supprimer
        if ($category->books()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer la catégorie car elle contient des livres.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès');
    }

    // ✅ API : Validation en temps réel du nom de catégorie
    public function validateCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => [
                'required',
                'max:255',
                'unique:categories,nom',
                function ($attribute, $value, $fail) {
                    // Recherche de catégories similaires (insensible à la casse et aux espaces)
                    $similarCategories = Category::whereRaw('LOWER(nom) LIKE ?', ['%' . strtolower($value) . '%'])
                        ->orWhereRaw('LOWER(nom) LIKE ?', ['%' . strtolower(str_replace(' ', '', $value)) . '%'])
                        ->exists();

                    if ($similarCategories) {
                        $fail('Une catégorie similaire existe déjà. Vérifiez avant de créer.');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'errors' => $validator->errors()
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Nom de catégorie disponible'
        ]);
    }

    // ✅ Génère un PDF pour une catégorie spécifique
    public function pdf(Category $category)
    {
        // Charger les livres de la catégorie
        $category->load('books');
        
        $pdf = Pdf::loadView('categories.pdf', compact('category'));
        
        // Utilisation de Str::slug au lieu de str_slug (déprécié)
        return $pdf->download('category-' . $category->id . '-' . Str::slug($category->nom) . '.pdf');
    }
}