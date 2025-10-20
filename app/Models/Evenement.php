<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = ['club_id', 'titre', 'description', 'date_event'];

    protected $casts = [
        'date_event' => 'datetime',
    ];

    public function club()
    {
        return $this->belongsTo(ClubLecture::class, 'club_id');
    }
}
