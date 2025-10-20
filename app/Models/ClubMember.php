<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMember extends Model
{
    use HasFactory;

    protected $fillable = ['club_id', 'user_id', 'status', 'joined_at'];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function club()
    {
        return $this->belongsTo(ClubLecture::class, 'club_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}