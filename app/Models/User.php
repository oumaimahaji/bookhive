<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

<<<<<<< HEAD
=======

>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
<<<<<<< HEAD
        
=======
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

<<<<<<< HEAD
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isModerator(): bool { return $this->role === 'moderator'; }
   
    public function isUser(): bool { return $this->role === 'user'; }
    public function isClubManager(): bool { return $this->role === 'club_manager'; }
=======
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
    public function isClubManager(): bool
    {
        return $this->role === 'club_manager';
    }

    /**
     * Relation pour les notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'applicant_id')
            ->orWhere('user_id', $this->id);
    }

    /**
     * Relation pour les notifications non lues
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    /**
     * Accesseur pour le compteur de notifications non lues
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->unreadNotifications()
            ->whereIn('type', ['join_approved', 'join_rejected'])
            ->count();
    }

    /**
     * Méthode pour le compteur de notifications non lues
     */
    public function unreadNotificationsCount()
    {
        return $this->unread_notifications_count;
    }

    // ========== AJOUTEZ CETTE MÉTHODE MANQUANTE ==========

    /**
     * Récupérer les notifications formatées
     */
    public function getFormattedNotifications()
    {
        return Notification::where(function ($query) {
            $query->where('applicant_id', $this->id)
                ->whereIn('type', ['join_approved', 'join_rejected']);
        })
            ->orWhere(function ($query) {
                $query->where('user_id', $this->id)
                    ->whereIn('type', ['join_approved', 'join_rejected']);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Récupérer les clubs où l'utilisateur est membre
     */
    public function clubMemberships()
    {
        return $this->hasMany(ClubMember::class, 'user_id');
    }

    /**
     * Vérifier si l'utilisateur est membre d'un club spécifique
     */
    public function isMemberOfClub($clubId)
    {
        return $this->clubMemberships()
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Récupérer les réservations de l'utilisateur
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Récupérer les reviews de l'utilisateur
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Récupérer les posts de l'utilisateur
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Récupérer les clubs créés par l'utilisateur
     */
    public function managedClubs()
    {
        return $this->hasMany(ClubLecture::class, 'createur_id');
    }

    /**
     * Récupérer les événements créés par l'utilisateur
     */
    public function managedEvents()
    {
        return $this->hasMany(Evenement::class, 'createur_id');
    }
>>>>>>> 688c610 (Ajout CRUD + FRONT ET BACK + API +AI Reservation et Review)
}
