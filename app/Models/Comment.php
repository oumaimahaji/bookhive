<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'post_id', 
        'contenu', 
        'date', 
        'sentiment', 
        'sentiment_confidence'
    ];

    protected $casts = [
        'sentiment_confidence' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
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