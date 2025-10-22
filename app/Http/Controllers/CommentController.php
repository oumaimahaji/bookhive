<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\SentimentAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    private $sentimentAnalyzer;

    public function __construct()
    {
        $this->sentimentAnalyzer = new SentimentAnalyzer();
    }

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

        // AJOUT: Filtre pour le sentiment
        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
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

    // Store new comment - AVEC SENTIMENT ANALYSIS
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'contenu' => 'required|string',
            'date' => 'required|date',
        ]);

        // ANALYSE DU SENTIMENT - NOUVEAU CODE
        $sentimentResult = $this->sentimentAnalyzer->analyze($request->contenu);

        Comment::create([
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
            'contenu' => $request->contenu,
            'date' => $request->date,
            'sentiment' => $sentimentResult['sentiment'], // NOUVEAU
            'sentiment_confidence' => $sentimentResult['confidence'], // NOUVEAU
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

    // Update comment - AVEC SENTIMENT ANALYSIS
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'contenu' => 'required|string',
            'date' => 'required|date',
        ]);

        // ANALYSE DU SENTIMENT - NOUVEAU CODE
        $sentimentResult = $this->sentimentAnalyzer->analyze($request->contenu);

        $comment->update([
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
            'contenu' => $request->contenu,
            'date' => $request->date,
            'sentiment' => $sentimentResult['sentiment'], // NOUVEAU
            'sentiment_confidence' => $sentimentResult['confidence'], // NOUVEAU
        ]);

        return redirect()->route('comments.index')->with('success', 'Commentaire mis à jour avec succès');
    }

    // Delete comment
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->route('comments.index')->with('success', 'Commentaire supprimé avec succès');
    }

    // AJOUT: Update user's own comment (for regular users)
    public function updateUserComment(Request $request, Comment $comment)
    {
        // Vérifier que l'utilisateur est bien le propriétaire du commentaire
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'contenu' => 'required|string',
        ]);

        // ANALYSE DU SENTIMENT - AJOUTÉ
        $sentimentResult = $this->sentimentAnalyzer->analyze($request->contenu);

        $comment->update([
            'contenu' => $request->contenu,
            'sentiment' => $sentimentResult['sentiment'], // AJOUTÉ
            'sentiment_confidence' => $sentimentResult['confidence'], // AJOUTÉ
        ]);

        return back()->with('success', 'Commentaire mis à jour avec succès');
    }
}