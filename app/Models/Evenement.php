<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = ['club_id', 'titre', 'description', 'date_event'];

    public function club()
    {
        return $this->belongsTo(ClubLecture::class, 'club_id');
    }
}