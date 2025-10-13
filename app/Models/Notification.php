<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = ['user_id', 'type', 'message', 'read_at'];

    protected $dates = ['read_at'];

=======
    protected $fillable = [
        'user_id', 
        'club_id',
        'applicant_id',
        'type', 
        'message', 
        'status',
        'read_at'
    ];

    protected $dates = ['read_at'];

    // Statuts possibles
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
<<<<<<< HEAD
=======

    public function club()
    {
        return $this->belongsTo(ClubLecture::class, 'club_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
}