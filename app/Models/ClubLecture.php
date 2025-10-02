<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubLecture extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description', 'createur_id'];

    public function createur()
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    public function evenements()
    {
        return $this->hasMany(Evenement::class, 'club_id');
    }
}