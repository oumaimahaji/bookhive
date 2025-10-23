<?php

namespace App\Services;

use App\Models\ClubLecture;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClubRecommendationService
{
    /**
     * Obtenir les clubs recommandés pour un utilisateur
     * Basé sur le nombre d'événements et les centres d'intérêt
     */
    public function getRecommendedClubs(User $user, $limit = 5)
    {
        // Clubs avec le plus d'événements actifs
        $popularClubs = ClubLecture::withCount(['evenements' => function($query) {
                $query->where('date_event', '>=', now());
            }])
            ->whereHas('evenements', function($query) {
                $query->where('date_event', '>=', now());
            })
            ->orderBy('evenements_count', 'desc')
            ->limit($limit)
            ->get();

        return $popularClubs;
    }

    /**
     * Obtenir les clubs les plus actifs (avec le plus d'événements)
     */
    public function getMostActiveClubs($limit = 10)
    {
        return ClubLecture::withCount(['evenements' => function($query) {
                $query->where('date_event', '>=', now()->subDays(30));
            }])
            ->with(['createur'])
            ->orderBy('evenements_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Vérifier si un club est "populaire" (seuil d'événements)
     */
    public function isClubPopular($clubId, $eventThreshold = 5)
    {
        $eventCount = ClubLecture::where('id', $clubId)
            ->withCount(['evenements' => function($query) {
                $query->where('date_event', '>=', now()->subDays(30));
            }])
            ->first()
            ->evenements_count;

        return $eventCount >= $eventThreshold;
    }
}