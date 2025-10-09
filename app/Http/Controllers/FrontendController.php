<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;

class FrontendController extends Controller
{
        public function index()
        {
            $books = Book::valid()->with('category')->get();
            $categories = Category::withCount('books')->get();
            
            $stats = [
                'totalBooks' => Book::valid()->count(),
                'totalCategories' => Category::count(),
                'totalAuthors' => Book::valid()->distinct('auteur')->count('auteur'),
            ];
            
            return view('frontend.home', compact('books', 'categories', 'stats'));
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