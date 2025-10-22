<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;

class SentimentAnalyticsController extends Controller
{
    public function dashboard()
    {
        // Overall statistics
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        
        // Post sentiment statistics
        $postSentiments = Post::selectRaw('sentiment, COUNT(*) as count')
            ->whereNotNull('sentiment')
            ->groupBy('sentiment')
            ->get()
            ->pluck('count', 'sentiment')
            ->toArray();

        // Comment sentiment statistics  
        $commentSentiments = Comment::selectRaw('sentiment, COUNT(*) as count')
            ->whereNotNull('sentiment')
            ->groupBy('sentiment')
            ->get()
            ->pluck('count', 'sentiment')
            ->toArray();

        // Controversial posts (many negative comments)
        $controversialPosts = Post::withCount(['comments as negative_comments_count' => function($query) {
                $query->where('sentiment', 'negative');
            }])
            ->having('negative_comments_count', '>', 2)
            ->orderBy('negative_comments_count', 'desc')
            ->take(10)
            ->get();

        // Most appreciated posts (mostly positive)
        $appreciatedPosts = Post::where('sentiment', 'positive')
            ->orWhereHas('comments', function($query) {
                $query->where('sentiment', 'positive');
            })
            ->withCount(['comments as positive_comments_count' => function($query) {
                $query->where('sentiment', 'positive');
            }])
            ->orderBy('positive_comments_count', 'desc')
            ->take(10)
            ->get();

        // Users with most positive/negative content
        $activeUsers = User::withCount([
                'posts as positive_posts_count' => function($query) {
                    $query->where('sentiment', 'positive');
                },
                'posts as negative_posts_count' => function($query) {
                    $query->where('sentiment', 'negative');
                },
                'comments as positive_comments_count' => function($query) {
                    $query->where('sentiment', 'positive');
                },
                'comments as negative_comments_count' => function($query) {
                    $query->where('sentiment', 'negative');
                }
            ])
            ->having('positive_posts_count', '>', 0)
            ->orHaving('negative_posts_count', '>', 0)
            ->orHaving('positive_comments_count', '>', 0) 
            ->orHaving('negative_comments_count', '>', 0)
            ->orderBy('positive_posts_count', 'desc')
            ->take(15)
            ->get();

        return view('admin.sentiment-analytics.dashboard', compact(
            'totalPosts',
            'totalComments',
            'postSentiments', 
            'commentSentiments',
            'controversialPosts',
            'appreciatedPosts',
            'activeUsers'
        ));
    }
}