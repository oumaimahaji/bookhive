<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // List all books
   public function index(Request $request)
{
    $books = Book::with('category')->get();
    $categories = Category::all();
    $editBook = null;

    if ($request->has('edit')) {
        $editBook = Book::find($request->input('edit'));
    }

    return view('books.index', compact('books', 'categories', 'editBook'));
}

    // Show create book form
    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    // Store new book
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string',
            'is_valid' => 'sometimes|boolean',
        ]);

        Book::create([
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'is_valid' => $request->has('is_valid') ? $request->is_valid : false,
        ]);

        return redirect()->route('books.index')->with('success', 'Livre ajouté avec succès');
    }

    // Show edit book form
    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    // Update book
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string',
            'is_valid' => 'sometimes|boolean',
        ]);

        $book->update([
            'titre' => $request->titre,
            'auteur' => $request->auteur,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'is_valid' => $request->has('is_valid') ? $request->is_valid : $book->is_valid,
        ]);

        return redirect()->route('books.index')->with('success', 'Livre mis à jour avec succès');
    }

    // Delete book
    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Livre supprimé avec succès');
    }








    
}


