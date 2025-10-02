<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // List all posts
    public function index(Request $request)
    {
        $posts = Post::with('user', 'comments')->get();
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

    // Store new post
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'date' => 'required|date',
        ]);

        Post::create([
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'contenu' => $request->contenu,
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

    // Update post
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'date' => 'required|date',
        ]);

        $post->update([
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'date' => $request->date,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post mis à jour avec succès');
    }

    // Delete post
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post supprimé avec succès');
    }
}