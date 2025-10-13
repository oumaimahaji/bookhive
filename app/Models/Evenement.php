<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = ['club_id', 'titre', 'description', 'date_event'];

<<<<<<< HEAD
=======
    protected $casts = [
        'date_event' => 'datetime'
    ];

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
    public function club()
    {
        return $this->belongsTo(ClubLecture::class, 'club_id');
    }
}