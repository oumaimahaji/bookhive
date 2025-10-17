<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserPostController extends Controller
{
    // Show user's own posts (separate page)
    public function myPosts()
    {
        $user = Auth::user();
        $posts = Post::with(['user', 'comments.user'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('user.my-posts', compact('posts'));
    }

    // Show all posts (community feed)
    public function communityPosts()
    {
        $posts = Post::with(['user', 'comments.user'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('posts.community', compact('posts'));
    }

    // Store new post (user created) - AVEC IMAGE
    public function storePost(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
           
        ]);

        return redirect()->route('user.posts.my')->with('success', 'Post créé avec succès');
    }

    // MÉTHODE UPDATE CRITIQUE - DANS UserPostController
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

        // Préparer les données de mise à jour
        $updateData = [
            'titre' => $request->titre,
            'contenu' => $request->contenu,
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

    // Store comment on any post
    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'contenu' => 'required|string',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'contenu' => $request->contenu,
            'date' => now()->toDateTimeString(),
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
}