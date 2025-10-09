<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // Affiche la liste des catégories
    public function index(Request $request)
    {
        $categories = Category::withCount('books')->get();
        $editCategory = null;

        if ($request->has('edit')) {
            $editCategory = Category::find($request->input('edit'));
        }

        return view('categories.index', compact('categories', 'editCategory'));
    }

    // Formulaire pour créer une catégorie
    public function create()
    {
        return view('categories.create');
    }

    // Stocke une nouvelle catégorie
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès');
    }

    // Formulaire pour éditer une catégorie
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Met à jour une catégorie
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès');
    }

    // Supprime une catégorie
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès');
    }

    // ✅ NOUVELLE API : Validation en temps réel
    public function validateCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => [
                'required',
                'max:255',
                'unique:categories,nom',
                function ($attribute, $value, $fail) {
                    // Vérifier les noms similaires (insensible à la casse)
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
}
