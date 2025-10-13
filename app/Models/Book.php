<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'auteur',
        'description',
        'category_id',
        'type',
        'is_valid',
        'user_id',
        'pdf_path',
<<<<<<< HEAD
        'cover_image',
=======
>>>>>>> 542202f4aa11f6ef658af99c6362a14a0e23898e
    ];



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
}
