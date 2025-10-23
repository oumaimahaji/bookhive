<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Services\SentimentAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    private $sentimentAnalyzer;

    public function __construct()
    {
        $this->sentimentAnalyzer = new SentimentAnalyzer();
    }

    public function index(Request $request)
    {
        $query = Post::with(['user', 'comments', 'reactions'])
        ->withCount(['comments', 'reactions']);
        
        // Advanced Search Filters
        if ($request->filled('title')) {
            $query->where('titre', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
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

        // AJOUT: Filtre pour les réactions
        if ($request->filled('reactions_count')) {
            if ($request->reactions_count === '0') {
                $query->has('reactions', '=', 0);
            } elseif ($request->reactions_count === '1-5') {
                $query->has('reactions', '>=', 1)->has('reactions', '<=', 5);
            } elseif ($request->reactions_count === '5+') {
                $query->has('reactions', '>', 5);
            }
        }

        // AJOUT: Filtre pour le sentiment
        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
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
                    $query->orderBy('created_at', $direction);
                    break;
                case 'reactions': // AJOUT: Tri par réactions
                    $query->withCount('reactions')->orderBy('reactions_count', $direction);
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // AJOUT: Compter les réactions pour chaque post
        $posts = $query->withCount('reactions')->paginate(10)->withQueryString();
        
        $users = User::all();
        $editPost = null;

        if ($request->has('edit')) {
            $editPost = Post::find($request->input('edit'));
        }

        // CORRECTION: S'assurer que toutes les variables passées à la vue sont simples
        return view('posts.index', compact('posts', 'users', 'editPost'));
    }

    // Show create post form
    public function create()
    {
        $users = User::all();
        return view('posts.create', compact('users'));
    }

    // Store new post - AVEC IMAGE ET SENTIMENT ANALYSIS
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ANALYSE DU SENTIMENT - NOUVEAU CODE
        $sentimentResult = $this->sentimentAnalyzer->analyze(
            $request->titre . ' ' . $request->contenu
        );

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // CORRECTION: Supprimer le champ date, Laravel gère created_at automatiquement
        Post::create([
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'image' => $imagePath,
            'sentiment' => $sentimentResult['sentiment'], // NOUVEAU
            'sentiment_confidence' => $sentimentResult['confidence'], // NOUVEAU
        ]);

        return redirect()->route('posts.index')->with('success', 'Post ajouté avec succès');
    }

    // Show edit post form
    public function edit(Post $post)
    {
        $users = User::all();
        return view('posts.edit', compact('post', 'users'));
    }

    // Update post - AVEC IMAGE ET SENTIMENT ANALYSIS
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ANALYSE DU SENTIMENT - NOUVEAU CODE
        $sentimentResult = $this->sentimentAnalyzer->analyze(
            $request->titre . ' ' . $request->contenu
        );

        // CORRECTION: Supprimer le champ date
        $updateData = [
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'sentiment' => $sentimentResult['sentiment'], // NOUVEAU
            'sentiment_confidence' => $sentimentResult['confidence'], // NOUVEAU
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

    /**
     * Show post reactions details
     */
    public function showReactions(Post $post)
    {
        $reactions = $post->reactions()
            ->with(['user', 'reaction'])
            ->get()
            ->groupBy('reaction.name')
            ->map(function ($group) {
                return [
                    'reaction' => $group->first()->reaction,
                    'count' => $group->count(),
                    'users' => $group->take(10)->map->user
                ];
            });

        return view('reactions.show', compact('post', 'reactions'));
    }

    /**
     * Get post reactions statistics for admin
     */
    public function getReactionsStatistics(Request $request)
    {
        $query = Post::withCount('reactions');
        
        // Filters
        if ($request->filled('title')) {
            $query->where('titre', 'like', '%' . $request->title . '%');
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Reactions count filter
        if ($request->filled('reactions_count')) {
            if ($request->reactions_count === '0') {
                $query->has('reactions', '=', 0);
            } elseif ($request->reactions_count === '1-5') {
                $query->has('reactions', '>=', 1)->has('reactions', '<=', 5);
            } elseif ($request->reactions_count === '5-10') {
                $query->has('reactions', '>=', 5)->has('reactions', '<=', 10);
            } elseif ($request->reactions_count === '10+') {
                $query->has('reactions', '>', 10);
            }
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'reactions_desc':
                    $query->orderBy('reactions_count', 'desc');
                    break;
                case 'reactions_asc':
                    $query->orderBy('reactions_count', 'asc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('reactions_count', 'desc');
            }
        } else {
            $query->orderBy('reactions_count', 'desc');
        }

        $perPage = $request->get('per_page', 10);
        $posts = $query->paginate($perPage)->withQueryString();

        $users = User::all();
        
        // Overall statistics
        $totalReactions = \App\Models\PostReaction::count();
        $mostReactedPost = Post::withCount('reactions')->orderBy('reactions_count', 'desc')->first();
        $reactionsByType = \App\Models\Reaction::withCount('postReactions')->get();

        return view('reactions.statistics', compact('posts', 'users', 'totalReactions', 'mostReactedPost', 'reactionsByType'));
    }

    /**
     * Delete all reactions for a specific post
     */
    public function deletePostReactions(Post $post)
    {
        try {
            // Supprimer toutes les réactions du post
            $post->reactions()->delete();
            
            // REDIRECTION CORRIGÉE : vers la page index unifiée
            return redirect()->route('admin.posts.reactions.index')
                            ->with('success', 'All reactions for the post have been deleted successfully.');
                            
        } catch (\Exception $e) {
            return redirect()->route('admin.posts.reactions.index')
                            ->with('error', 'An error occurred while deleting reactions: ' . $e->getMessage());
        }
    }

    public function reactionsIndex(Request $request)
    {
        // Récupérer tous les utilisateurs pour le filtre
        $users = User::all();
        
        // REQUÊTE CORRIGÉE : Voir tous les posts, pas seulement ceux avec réactions
        $query = Post::with(['user', 'reactions.user', 'reactions.reaction'])
                    ->withCount('reactions');

        // Appliquer les filtres
        if ($request->filled('title')) {
            $query->where('titre', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtres par nombre de réactions
        if ($request->filled('reactions_count')) {
            switch ($request->reactions_count) {
                case '0':
                    $query->whereDoesntHave('reactions');
                    break;
                case '1-5':
                    $query->has('reactions', '>=', 1)->has('reactions', '<=', 5);
                    break;
                case '5-10':
                    $query->has('reactions', '>=', 5)->has('reactions', '<=', 10);
                    break;
                case '10+':
                    $query->has('reactions', '>=', 10);
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Appliquer le tri
        switch ($request->sort) {
            case 'reactions_asc':
                $query->orderBy('reactions_count', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('reactions_count', 'desc');
                break;
        }

        $perPage = $request->per_page ?? 10;
        
        // PAGINATION
        $posts = $query->paginate($perPage)->withQueryString();

        // DEBUG CORRIGÉ : Utilisez Log avec l'import correct
        Log::info('PAGINATION DEBUG', [
            'total_posts' => $posts->total(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'has_pages' => $posts->hasPages(),
            'query' => $request->fullUrl()
        ]);

        // Statistiques globales
        $totalReactions = \App\Models\PostReaction::count();
        $mostReactedPost = Post::withCount('reactions')->orderBy('reactions_count', 'desc')->first();
        $reactionsByType = \App\Models\Reaction::withCount('postReactions')->get();

        // Si un post spécifique est sélectionné
        $selectedPost = null;
        $postReactions = collect();
        
        if ($request->filled('post_id')) {
            $selectedPost = Post::with(['user', 'reactions.user', 'reactions.reaction'])
                                ->find($request->post_id);
            
            if ($selectedPost) {
                // Grouper les réactions par type
                $postReactions = $selectedPost->reactions->groupBy('reaction.name')->map(function($reactions, $name) {
                    return [
                        'reaction' => $reactions->first()->reaction,
                        'count' => $reactions->count(),
                        'users' => $reactions->pluck('user')->unique('id')
                    ];
                });
            }
        }

        return view('reactions.index', compact(
            'posts', 
            'users', 
            'totalReactions', 
            'mostReactedPost', 
            'reactionsByType',
            'selectedPost',
            'postReactions'
        ));
    }

    /**
     * Delete a specific reaction
     */
    public function deleteSingleReaction($postId, $reactionId)
    {
        try {
            $reaction = \App\Models\PostReaction::where('post_id', $postId)
                                                ->where('id', $reactionId)
                                                ->firstOrFail();
            
            $userName = $reaction->user->name;
            $reactionType = $reaction->reaction->name;
            
            $reaction->delete();
            
            return redirect()->back()
                            ->with('success', "Reaction ($reactionType) from $userName has been deleted successfully.");
                            
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Error deleting reaction: ' . $e->getMessage());
        }
    }
}
