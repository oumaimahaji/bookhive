<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'titre', 'contenu', 'date', 'image', 'reactions_count'];
    
    protected $casts = [
        'reactions_count' => 'array',
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
}