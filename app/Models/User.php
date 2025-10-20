<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ======= Méthodes de rôle =======
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

    // ======= Relations =======
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'applicant_id')
            ->orWhere('user_id', $this->id);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function managedClubs()
    {
        return $this->hasMany(ClubLecture::class, 'createur_id');
    }

    public function managedEvents()
    {
        return $this->hasMany(Evenement::class, 'createur_id');
    }

    public function clubMemberships()
    {
        return $this->hasMany(ClubMember::class, 'user_id');
    }

    // ======= Méthodes utilitaires =======
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->unreadNotifications()
            ->whereIn('type', ['join_approved', 'join_rejected'])
            ->count();
    }

    public function unreadNotificationsCount()
    {
        return $this->unread_notifications_count;
    }

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

    public function isMemberOfClub($clubId)
    {
        return $this->clubMemberships()
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->exists();
    }
}
