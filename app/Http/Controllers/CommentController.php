<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // List all comments
    public function index(Request $request)
    {
        $comments = Comment::with('user', 'post')->get();
        $users = User::all();
        $posts = Post::all();
        $editComment = null;

        if ($request->has('edit')) {
            $editComment = Comment::find($request->input('edit'));
        }

        return view('comments.index', compact('comments', 'users', 'posts', 'editComment'));
    }

    // Show create comment form
    public function create()
    {
        $users = User::all();
        $posts = Post::all();
        return view('comments.create', compact('users', 'posts'));
    }

    // Store new comment
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'contenu' => 'required|string',
            'date' => 'required|date',
        ]);

        Comment::create([
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
            'contenu' => $request->contenu,
            'date' => $request->date,
        ]);

        return redirect()->route('comments.index')->with('success', 'Commentaire ajouté avec succès');
    }

    // Show edit comment form
    public function edit(Comment $comment)
    {
        $users = User::all();
        $posts = Post::all();
        return view('comments.edit', compact('comment', 'users', 'posts'));
    }

    // Update comment
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'contenu' => 'required|string',
            'date' => 'required|date',
        ]);

        $comment->update([
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
            'contenu' => $request->contenu,
            'date' => $request->date,
        ]);

        return redirect()->route('comments.index')->with('success', 'Commentaire mis à jour avec succès');
    }

    // Delete comment
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->route('comments.index')->with('success', 'Commentaire supprimé avec succès');
    }
}