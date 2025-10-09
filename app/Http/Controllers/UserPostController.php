<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    // Store new post (user created)
    public function storePost(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'date' => now()->toDateTimeString(),
        ]);

        return redirect()->route('user.posts.my')->with('success', 'Post créé avec succès');
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