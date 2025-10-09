<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class ModeratorController extends Controller
{
    public function dashboard()
    {
        $pendingBooks = Book::where('is_valid', false)->get();
        return view('dashboard_moderator', compact('pendingBooks'));
    }

    public function books()
    {
        $pendingBooks = Book::where('is_valid', false)->get();
        return view('dashboard_moderator', compact('pendingBooks'));
    }

    public function validateBook($id)
    {
        $book = Book::findOrFail($id);
        $book->is_valid = true;
        $book->save();

        return redirect()->back()->with('success', 'Book validated successfully.');
    }
}
