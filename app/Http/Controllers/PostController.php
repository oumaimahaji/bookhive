<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // AJOUT IMPORT

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'comments']);
        
        // Advanced Search Filters
        if ($request->filled('title')) {
            $query->where('titre', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        
        if ($request->filled('content')) {
            $query->where('contenu', 'like', '%' . $request->content . '%');
        }
        
        if ($request->filled('comments_count')) {
            if ($request->comments_count === '0') {
                $query->has('comments', '=', 0);
            } elseif ($request->comments_count === '1-5') {
                $query->has('comments', '>=', 1)->has('comments', '<=', 5);
            } elseif ($request->comments_count === '5+') {
                $query->has('comments', '>', 5);
            }
        }
        
        // Sorting
        if ($request->filled('sort')) {
            $direction = $request->direction === 'desc' ? 'desc' : 'asc';
            
            switch ($request->sort) {
                case 'title':
                    $query->orderBy('titre', $direction);
                    break;
                case 'user':
                    $query->join('users', 'posts.user_id', '=', 'users.id')
                          ->orderBy('users.name', $direction)
                          ->select('posts.*');
                    break;
                case 'date':
                    $query->orderBy('date', $direction);
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $posts = $query->paginate(10)->withQueryString();
        $users = User::all();
        $editPost = null;

        if ($request->has('edit')) {
            $editPost = Post::find($request->input('edit'));
        }

        return view('posts.index', compact('posts', 'users', 'editPost'));
    }

    // Show create post form
    public function create()
    {
        $users = User::all();
        return view('posts.create', compact('users'));
    }

    // Store new post - AVEC IMAGE
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // AJOUT VALIDATION
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        Post::create([
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'image' => $imagePath, // AJOUT IMAGE
            'date' => $request->date,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post ajouté avec succès');
    }

    // Show edit post form
    public function edit(Post $post)
    {
        $users = User::all();
        return view('posts.edit', compact('post', 'users'));
    }

    // Update post - AVEC IMAGE
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // AJOUT VALIDATION
        ]);

        $updateData = [
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'date' => $request->date,
        ];

        // Gestion de la suppression d'image
        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $updateData['image'] = null;
        }

        // Gestion du nouvel upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            // Stocker la nouvelle image
            $updateData['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($updateData);

        return redirect()->route('posts.index')->with('success', 'Post mis à jour avec succès');
    }

    // Delete post - AVEC SUPPRESSION IMAGE
    public function destroy(Post $post)
    {
        // Supprimer l'image si elle existe
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post supprimé avec succès');
    }

    public function updateUserPost(Request $request, Post $post)
    {
        // Vérifier que l'utilisateur est bien le propriétaire du post
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
        ]);

        $post->update([
            'titre' => $request->titre,
            'contenu' => $request->contenu,
        ]);

        return redirect()->route('user.posts.my')->with('success', 'Post updated successfully!');
    }

    public function getPostComments(Post $post)
    {
        // Charger les commentaires avec l'utilisateur
        $comments = $post->comments()->with('user')->get();
        
        return response()->json($comments);
    }
}