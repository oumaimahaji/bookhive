<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'color'];

    // Relation avec les réactions de posts
    public function postReactions()
    {
        return $this->hasMany(PostReaction::class);
    }
}