<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reaction;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReactionController extends Controller
{
    public function react(Request $request, Post $post)
    {
        // Créer les réactions automatiquement
        $this->ensureReactionsExist();

        $reactionName = $request->reaction;
        
        // Validation simple
        $validReactions = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];
        if (!in_array($reactionName, $validReactions)) {
            return response()->json(['error' => 'Invalid reaction'], 422);
        }

        $reaction = Reaction::where('name', $reactionName)->first();
        
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Vérifier si l'utilisateur a déjà réagi à ce post
        $userReaction = PostReaction::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        Log::info('User reaction check:', [
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'existing_reaction' => $userReaction ? $userReaction->reaction->name : 'none',
            'new_reaction' => $reactionName
        ]);

        if ($userReaction) {
            if ($userReaction->reaction_id === $reaction->id) {
                // Même réaction : supprimer (toggle)
                Log::info('Removing reaction');
                $userReaction->delete();
                $this->updateReactionsCount($post, $reactionName, -1);
                
                return response()->json([
                    'message' => 'Reaction removed',
                    'total_reactions' => $post->fresh()->total_reactions,
                    'user_reaction' => null,
                    'reactions_count' => $post->fresh()->reactions_count
                ]);
            } else {
                // Réaction différente : mettre à jour
                Log::info('Updating reaction');
                $oldReactionName = $userReaction->reaction->name;
                $userReaction->update(['reaction_id' => $reaction->id]);
                
                $this->updateReactionsCount($post, $oldReactionName, -1);
                $this->updateReactionsCount($post, $reactionName, 1);

                return response()->json([
                    'message' => 'Reaction updated',
                    'total_reactions' => $post->fresh()->total_reactions,
                    'user_reaction' => $reaction->name,
                    'reactions_count' => $post->fresh()->reactions_count
                ]);
            }
        } else {
            // Nouvelle réaction
            Log::info('Adding new reaction');
            PostReaction::create([
                'user_id' => Auth::id(),
                'post_id' => $post->id,
                'reaction_id' => $reaction->id
            ]);
            
            $this->updateReactionsCount($post, $reactionName, 1);

            return response()->json([
                'message' => 'Reaction added',
                'total_reactions' => $post->fresh()->total_reactions,
                'user_reaction' => $reaction->name,
                'reactions_count' => $post->fresh()->reactions_count
            ]);
        }
    }

    public function getReactions(Post $post)
    {
        $this->ensureReactionsExist();
        
        try {
            $reactions = $post->reactions()
                ->with(['reaction', 'user'])
                ->latest()
                ->get()
                ->groupBy('reaction.name')
                ->map(function ($group) {
                    return [
                        'reaction' => $group->first()->reaction,
                        'count' => $group->count(),
                        'users' => $group->take(10)->map(function($postReaction) {
                            return $postReaction->user;
                        })
                    ];
                });

            return response()->json([
                'reactions' => $reactions,
                'user_reaction' => $post->userReaction?->reaction->name
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reactions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load reactions'], 500);
        }
    }

    private function ensureReactionsExist()
    {
        $reactions = [
            ['name' => 'like', 'icon' => 'fas fa-thumbs-up', 'color' => 'text-blue-600'],
            ['name' => 'love', 'icon' => 'fas fa-heart', 'color' => 'text-red-600'],
            ['name' => 'haha', 'icon' => 'fas fa-laugh', 'color' => 'text-yellow-600'],
            ['name' => 'wow', 'icon' => 'fas fa-surprise', 'color' => 'text-yellow-500'],
            ['name' => 'sad', 'icon' => 'fas fa-sad-tear', 'color' => 'text-blue-500'],
            ['name' => 'angry', 'icon' => 'fas fa-angry', 'color' => 'text-red-700'],
        ];

        foreach ($reactions as $reactionData) {
            Reaction::firstOrCreate(
                ['name' => $reactionData['name']],
                $reactionData
            );
        }
    }

    private function updateReactionsCount(Post $post, string $reactionName, int $change)
    {
        $reactionsCount = $post->reactions_count ?? [];
        $currentCount = $reactionsCount[$reactionName] ?? 0;
        $reactionsCount[$reactionName] = max(0, $currentCount + $change);
        
        $post->update(['reactions_count' => $reactionsCount]);
    }
}