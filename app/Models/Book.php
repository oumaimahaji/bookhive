<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = ['titre', 'auteur', 'description', 'category_id', 'type', 'is_valid'];

   
=======
    protected $fillable = [
        'titre',
        'auteur',
        'description',
        'category_id',
        'type',
        'is_valid',
        'user_id',
        'pdf_path',
        'cover_image',
    ];


>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    // Scope pour les livres validés
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
