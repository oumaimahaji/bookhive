<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'titre', 
        'contenu', 
        'date', 
        'image', 
        'reactions_count',
        'sentiment', 
        'sentiment_confidence'
    ];
    
    protected $casts = [
        'reactions_count' => 'array',
        'sentiment_confidence' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public function userReaction()
    {
        return $this->hasOne(PostReaction::class)->where('user_id', Auth::id());
    }

    public function getTotalReactionsAttribute()
    {
        if ($this->reactions_count) {
            return array_sum($this->reactions_count);
        }
        return 0;
    }

    public function getTopReactionsAttribute()
    {
        if (!$this->reactions_count) return collect();

        $reactions = Reaction::all()->keyBy('name');
        $topReactions = collect($this->reactions_count)
            ->filter(fn($count) => $count > 0)
            ->sortDesc()
            ->take(3)
            ->mapWithKeys(function ($count, $name) use ($reactions) {
                return [$name => [
                    'count' => $count,
                    'reaction' => $reactions->get($name)
                ]];
            });

        return $topReactions;
    }

     /**
     * Get sentiment badge color
     */
    public function getSentimentColorAttribute(): string
    {
        return match($this->sentiment) {
            'positive' => 'success',
            'negative' => 'danger', 
            'neutral' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get sentiment icon
     */
    public function getSentimentIconAttribute(): string
    {
        return match($this->sentiment) {
            'positive' => 'fas fa-smile text-success',
            'negative' => 'fas fa-frown text-danger', 
            'neutral' => 'fas fa-meh text-secondary',
            default => 'fas fa-meh text-secondary'
        };
    }

    /**
     * Get sentiment badge HTML
     */
    public function getSentimentBadgeAttribute(): string
    {
        if (!$this->sentiment) {
            return '';
        }

        return '<span class="badge bg-' . $this->sentiment_color . '">
            <i class="' . $this->sentiment_icon . '"></i>
            ' . ucfirst($this->sentiment) . ' (' . ($this->sentiment_confidence * 100) . '%)
        </span>';
    }
}
