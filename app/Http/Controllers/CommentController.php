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
    $query = Comment::with(['user', 'post']);
    
    // Advanced Search Filters
    if ($request->filled('content')) {
        $query->where('contenu', 'like', '%' . $request->content . '%');
    }
    
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }
    
    if ($request->filled('post_id')) {
        $query->where('post_id', $request->post_id);
    }
    
    if ($request->filled('date')) {
        $query->whereDate('date', $request->date);
    }
    
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('date', [$request->start_date, $request->end_date]);
    }
    
    if ($request->filled('content_length')) {
        switch ($request->content_length) {
            case 'short':
                $query->whereRaw('LENGTH(contenu) < 50');
                break;
            case 'medium':
                $query->whereRaw('LENGTH(contenu) BETWEEN 50 AND 200');
                break;
            case 'long':
                $query->whereRaw('LENGTH(contenu) > 200');
                break;
        }
    }
    
    // Sorting
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'user':
                $query->join('users', 'comments.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc')
                      ->select('comments.*');
                break;
            case 'post':
                $query->join('posts', 'comments.post_id', '=', 'posts.id')
                      ->orderBy('posts.titre', 'asc')
                      ->select('comments.*');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    } else {
        $query->orderBy('created_at', 'desc');
    }
    
    $comments = $query->get();
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