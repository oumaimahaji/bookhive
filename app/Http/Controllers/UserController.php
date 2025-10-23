<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\ClubLecture;
use App\Models\Notification;
use App\Models\ClubMember;
use App\Models\Evenement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\ClubRecommendationService;

/** @var User $user */ // AJOUTEZ CETTE LIGNE pour aider Intelephense

class UserController extends Controller
{

    protected $recommendationService;

    public function __construct()
    {
        $this->recommendationService = new ClubRecommendationService();
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            abort(403, 'User not authenticated');
        }

        // Initialiser les variables avec des valeurs par défaut
        $activeReservations = 0;
        $totalReviews = 0;
        $availableBooks = 0;
        $totalClubs = 0;
        $recentClubs = collect();

        try {
            // Compter les réservations de l'utilisateur
            $activeReservations = Reservation::where('user_id', $user->id)->count();

            // Compter les reviews de l'utilisateur
            $totalReviews = Review::where('user_id', $user->id)->count();

            // Compter les livres disponibles
            $availableBooks = Book::where('is_valid', true)->count();

            // Compter tous les clubs disponibles
            $totalClubs = ClubLecture::count();

            // Récupérer les 3 clubs les plus récents (sans doublons)
            $recentClubs = ClubLecture::with('createur')
                ->latest()
                ->distinct()
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            // En cas d'erreur, logger l'erreur mais continuer avec les valeurs par défaut
            Log::error('User Dashboard Error: ' . $e->getMessage());
        }

        // Passer les données à la vue
        return view('dashboard_user', [
            'activeReservations' => $activeReservations,
            'totalReviews' => $totalReviews,
            'availableBooks' => $availableBooks,
            'totalClubs' => $totalClubs,
            'recentClubs' => $recentClubs
        ]);
    }

    public function books()
    {
        try {
            $books = Book::where('is_valid', true)->get();
            return view('user.books', compact('books'));
        } catch (\Exception $e) {
            Log::error('User Books Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load books.');
        }
    }

    public function clubs()
    {
        try {
            $user = Auth::user();

            // Récupérer tous les clubs sans doublons
            $clubs = ClubLecture::with('createur')
                ->withCount(['evenements' => function ($query) {
                    $query->where('date_event', '>=', now());
                }])
                ->latest()
                ->distinct()
                ->get();

            // Pour chaque club, déterminer le statut de l'utilisateur
            $clubs->each(function ($club) use ($user) {
                $club->user_status = $this->getUserClubStatus($club, $user->id);
            });

            return view('user.clubs', compact('clubs'));
        } catch (\Exception $e) {
            Log::error('User Clubs Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load clubs.');
        }
    }

    public function joinClub(Request $request, $clubId)
    {
        try {
            $user = Auth::user();
            $club = ClubLecture::findOrFail($clubId);

            // Vérifier si l'utilisateur est déjà membre
            if ($club->isMember($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous êtes déjà membre de ce club.'
                ]);
            }

            // Vérifier si l'utilisateur a déjà une demande en attente
            if ($club->hasPendingRequest($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà une demande en attente pour ce club.'
                ]);
            }

            // Créer la notification pour le manager du club
            $notification = Notification::create([
                'user_id' => $club->createur_id,
                'applicant_id' => $user->id,
                'club_id' => $clubId,
                'type' => 'join_request',
                'message' => "{$user->name} souhaite rejoindre votre club '{$club->nom}'",
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Votre demande a été envoyée et est en attente d\'approbation.',
                'notification_id' => $notification->id
            ]);
        } catch (\Exception $e) {
            Log::error('Join Club Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi de votre demande.'
            ], 500);
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // Mise à jour du profil
            User::where('id', $user->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Update Profile Error: ' . $e->getMessage());
            return redirect()->route('user.profile')->with('error', 'Error updating profile.');
        }
    }

    /**
     * Récupérer les notifications de l'utilisateur avec les événements des clubs
     */
    public function notifications()
    {
        try {
            $user = Auth::user();

            // Récupérer uniquement les notifications d'acceptation/refus pour l'utilisateur
            $notifications = Notification::with(['club'])
                ->where(function ($query) use ($user) {
                    $query->where('applicant_id', $user->id) // L'utilisateur est le demandeur
                        ->whereIn('type', ['join_approved', 'join_rejected']);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id) // L'utilisateur est le destinataire
                        ->whereIn('type', ['join_approved', 'join_rejected']);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Récupérer les événements des clubs où l'utilisateur est membre
            $userClubs = ClubMember::where('user_id', $user->id)
                ->where('status', 'active')
                ->pluck('club_id');

            $clubEvents = Evenement::with('club')
                ->whereIn('club_id', $userClubs)
                ->where('date_event', '>=', now())
                ->orderBy('date_event', 'asc')
                ->get();

            // Convertir manuellement les dates_event en objets Carbon si nécessaire
            $clubEvents->each(function ($event) {
                if (!$event->date_event instanceof Carbon) {
                    $event->date_event = Carbon::parse($event->date_event);
                }
            });

            return view('user.notifications', compact('notifications', 'clubEvents'));
        } catch (\Exception $e) {
            Log::error('User Notifications Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load notifications.');
        }
    }

    /**
     * Marquer une notification comme lue
     */
    public function markNotificationAsRead($id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('id', $id)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('applicant_id', $user->id);
                })
                ->firstOrFail();

            $notification->update([
                'read_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue.'
            ]);
        } catch (\Exception $e) {
            Log::error('Mark Notification Read Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage de la notification.'
            ]);
        }
    }
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllNotificationsAsRead()
    {
        try {
            $user = Auth::user();

            Notification::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('applicant_id', $user->id);
            })
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications ont été marquées comme lues.'
            ]);
        } catch (\Exception $e) {
            Log::error('Mark All Notifications Read Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage des notifications.'
            ]);
        }
    }

    /**
     * Déterminer le statut de l'utilisateur pour un club
     */
    private function getUserClubStatus($club, $userId)
    {
        if ($club->isMember($userId)) {
            return 'member';
        }

        if ($club->hasPendingRequest($userId)) {
            return 'pending';
        }

        return 'not_member';
    }

    /**
     * Récupérer le nombre de notifications non lues (pour API/AJAX)
     */
    public function getUnreadNotificationsCount()
    {
        try {
            $user = Auth::user();
            
            // Utiliser la méthode du modèle User ou une requête directe
            $count = $this->getUnreadNotificationsCountForUser($user);

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Get Unread Notifications Count Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Méthode helper pour compter les notifications non lues
     */
    private function getUnreadNotificationsCountForUser(User $user)
    {
        return Notification::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('applicant_id', $user->id);
        })
        ->whereNull('read_at')
        ->whereIn('type', ['join_approved', 'join_rejected'])
        ->count();
    }

    /**
     * Récupérer l'activité récente de l'utilisateur
     */
    public function getRecentActivity()
    {
        try {
            $user = Auth::user();

            $recentReservations = Reservation::where('user_id', $user->id)
                ->with('book')
                ->latest()
                ->take(2)
                ->get();

            $recentReviews = Review::where('user_id', $user->id)
                ->with('book')
                ->latest()
                ->take(2)
                ->get();

            return response()->json([
                'success' => true,
                'reservations' => $recentReservations,
                'reviews' => $recentReviews
            ]);
        } catch (\Exception $e) {
            Log::error('Get Recent Activity Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reservations' => [],
                'reviews' => []
            ]);
        }
    }

    /**
     * Récupérer les posts communautaires
     */
    public function communityPosts()
    {
        try {
            // Récupérer tous les posts avec leurs auteurs
            $posts = \App\Models\Post::with('user')
                ->withCount('comments')
                ->latest()
                ->get();

            return view('user.community-posts', compact('posts'));
        } catch (\Exception $e) {
            Log::error('Community Posts Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load community posts.');
        }
    }

    /**
     * Récupérer les posts de l'utilisateur
     */
    public function myPosts()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $posts = $user->posts()
                ->withCount('comments')
                ->latest()
                ->get();

            return view('user.my-posts', compact('posts'));
        } catch (\Exception $e) {
            Log::error('My Posts Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load your posts.');
        }
    }

    /**
     * Récupérer les reviews de l'utilisateur
     */
    public function myReviews()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $reviews = $user->reviews()
                ->with('book')
                ->latest()
                ->get();

            return view('user.my-reviews', compact('reviews'));
        } catch (\Exception $e) {
            Log::error('My Reviews Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load your reviews.');
        }
    }

    /**
     * Récupérer la liste de lecture de l'utilisateur
     */
    public function readingList()
    {
        try {
            $user = Auth::user();
            // Implémentation basique - à adapter selon votre modèle
            $readingList = Book::whereHas('reservations', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereIn('statut', ['en_attente', 'confirmee']);
            })->get();

            return view('user.reading-list', compact('readingList'));
        } catch (\Exception $e) {
            Log::error('Reading List Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load reading list.');
        }
    }

    /**
     * Récupérer l'historique de lecture de l'utilisateur
     */
    public function readingHistory()
    {
        try {
            $user = Auth::user();
            // Implémentation basique - à adapter selon votre modèle
            $readingHistory = Book::whereHas('reservations', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('statut', 'termine');
            })->get();

            return view('user.reading-history', compact('readingHistory'));
        } catch (\Exception $e) {
            Log::error('Reading History Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load reading history.');
        }
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validation des données
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        try {
            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->route('user.profile')->with('error', 'Current password is incorrect.');
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return redirect()->route('user.profile')->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Update Password Error: ' . $e->getMessage());
            return redirect()->route('user.profile')->with('error', 'Error updating password.');
        }
    }

    /**
     * Afficher les clubs recommandés
     */
    public function recommendedClubs()
    {
        try {
            $user = Auth::user();

            // Clubs recommandés
            $recommendedClubs = $this->recommendationService->getRecommendedClubs($user, 6);

            // Clubs les plus actifs
            $activeClubs = $this->recommendationService->getMostActiveClubs(10);

            // Pour chaque club, déterminer le statut de l'utilisateur
            $recommendedClubs->each(function ($club) use ($user) {
                $club->user_status = $this->getUserClubStatus($club, $user->id);
            });

            $activeClubs->each(function ($club) use ($user) {
                $club->user_status = $this->getUserClubStatus($club, $user->id);
            });

            return view('user.recommended-clubs', compact('recommendedClubs', 'activeClubs'));
        } catch (\Exception $e) {
            Log::error('Recommended Clubs Error: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Unable to load recommended clubs.');
        }
    }

    /**
     * API pour obtenir les clubs recommandés (pour AJAX)
     */
    public function getRecommendedClubsApi()
    {
        try {
            $user = Auth::user();
            $clubs = $this->recommendationService->getRecommendedClubs($user, 8);

            // Ajouter le statut de l'utilisateur pour chaque club
            $clubs->each(function ($club) use ($user) {
                $club->user_status = $this->getUserClubStatus($club, $user->id);
                $club->is_popular = $this->recommendationService->isClubPopular($club->id);
            });

            return response()->json([
                'success' => true,
                'clubs' => $clubs
            ]);
        } catch (\Exception $e) {
            Log::error('Get Recommended Clubs API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to load recommended clubs.'
            ]);
        }
    }
}