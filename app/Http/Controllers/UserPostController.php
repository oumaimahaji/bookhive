<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Services\SentimentAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserPostController extends Controller
{
    private $sentimentAnalyzer;

    public function __construct()
    {
        $this->sentimentAnalyzer = new SentimentAnalyzer();
    }

    // Show user's own posts (separate page)
    public function myPosts()
    {
        $user = Auth::user();
        $posts = Post::with(['user', 'comments.user', 'reactions.reaction'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('user.my-posts', compact('posts'));
    }

// Show all posts (community feed) - MODIFIED TO HANDLE SEARCH
public function communityPosts(Request $request)
{
    $search = $request->get('search', '');
    
    $posts = Post::with(['user', 'comments.user', 'reactions.reaction']);
    
    // Apply search filter if search term exists
    if (!empty($search)) {
        $posts = $posts->where(function($query) use ($search) {
            $query->where('titre', 'like', '%'.$search.'%')
                  ->orWhere('contenu', 'like', '%'.$search.'%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%'.$search.'%');
                  });
        });
    }
    
    $posts = $posts->orderBy('created_at', 'desc')->get();

    // Return JSON for AJAX requests
    if ($request->get('ajax') == '1') {
        return response()->json([
            'posts' => view('posts.partials.posts-list', compact('posts'))->render(),
            'count' => $posts->count()
        ]);
    }

    return view('posts.community', compact('posts', 'search'));
}

    

    // Dans UserPostController - méthode storePost
public function storePost(Request $request)
{
    $request->validate([
        'titre' => 'required|string|min:3|max:255',
        'contenu' => 'required|string|min:10',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], [
        'titre.required' => 'Le titre est obligatoire',
        'titre.min' => 'Le titre doit contenir au moins 3 caractères',
        'titre.max' => 'Le titre ne peut pas dépasser 255 caractères',
        'contenu.required' => 'Le contenu est obligatoire',
        'contenu.min' => 'Le contenu doit contenir au moins 10 caractères',
        'image.image' => 'Le fichier doit être une image',
        'image.mimes' => 'L\'image doit être au format JPEG, PNG, JPG ou GIF',
        'image.max' => 'L\'image ne peut pas dépasser 2MB',
    ]);

    // ANALYSE DU SENTIMENT - AJOUTÉ
    $sentimentResult = $this->sentimentAnalyzer->analyze(
        $request->titre . ' ' . $request->contenu
    );

    $imagePath = null;
    if ($request->hasFile('image')) {
        // Stocker l'image dans public/storage/posts/
        $imageName = time().'_'.$request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('storage/posts'), $imageName);
        $imagePath = 'posts/'.$imageName;
    }

    Post::create([
        'user_id' => Auth::id(),
        'titre' => $request->titre,
        'contenu' => $request->contenu,
        'image' => $imagePath,
        'sentiment' => $sentimentResult['sentiment'],
        'sentiment_confidence' => $sentimentResult['confidence'],
    ]);

    return redirect()->route('user.posts.my')->with('success', 'Post créé avec succès');
}

    // MÉTHODE UPDATE CRITIQUE - AVEC SENTIMENT ANALYSIS
    public function update(Request $request, Post $post)
    {
        // Vérifier que l'utilisateur est bien le propriétaire du post
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ANALYSE DU SENTIMENT - AJOUTÉ
        $sentimentResult = $this->sentimentAnalyzer->analyze(
            $request->titre . ' ' . $request->contenu
        );

        // Préparer les données de mise à jour
        $updateData = [
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'sentiment' => $sentimentResult['sentiment'], // AJOUTÉ
            'sentiment_confidence' => $sentimentResult['confidence'], // AJOUTÉ
        ];

        // Gestion de la suppression d'image
        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($post->image) {
                // Supprimer l'image du dossier public/storage/posts/
                $imagePath = public_path('storage/'.$post->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $updateData['image'] = null;
        }

        // Gestion du nouvel upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($post->image) {
                $oldImagePath = public_path('storage/'.$post->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // Stocker la nouvelle image dans public/storage/posts/
            $imageName = time().'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('storage/posts'), $imageName);
            $updateData['image'] = 'posts/'.$imageName;
        }

        // Mettre à jour le post
        $post->update($updateData);

        return redirect()->route('user.posts.my')->with('success', 'Post updated successfully!');
    }

    // Store comment on any post - AVEC SENTIMENT ANALYSIS
    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'contenu' => 'required|string',
        ]);

        // ANALYSE DU SENTIMENT - AJOUTÉ
        $sentimentResult = $this->sentimentAnalyzer->analyze($request->contenu);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'contenu' => $request->contenu,
            'date' => now()->toDateTimeString(),
            'sentiment' => $sentimentResult['sentiment'], // AJOUTÉ
            'sentiment_confidence' => $sentimentResult['confidence'], // AJOUTÉ
        ]);

        return back()->with('success', 'Commentaire ajouté avec succès');
    }

    // Delete user's own post
    public function deletePost(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Supprimer l'image si elle existe
        if ($post->image) {
            $imagePath = public_path('storage/'.$post->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $post->delete();
        return redirect()->route('user.posts.my')->with('success', 'Post supprimé avec succès');
    }

    // Delete user's own comment
    public function deleteComment(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();
        return back()->with('success', 'Commentaire supprimé avec succès');
    }

    // AJOUT: Update user's own comment (for regular users)
    public function updateComment(Request $request, Comment $comment)
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
