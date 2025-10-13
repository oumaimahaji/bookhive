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

    // AJOUTEZ CETTE RELATION
    public function members()
    {
        return $this->hasMany(ClubMember::class, 'club_id');
    }

    // Méthode pour vérifier si un utilisateur est membre
    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->where('status', 'active')->exists();
    }

    // Méthode pour vérifier si un utilisateur a une demande en attente
    public function hasPendingRequest($userId)
    {
        return Notification::where('club_id', $this->id)
            ->where('applicant_id', $userId)
            ->where('type', 'join_request')
            ->where('status', 'pending')
            ->exists();
    }
}