<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClubLecture;
use App\Models\Evenement;
use App\Models\User;
use App\Models\Notification;
use App\Models\ClubMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\EventReminderMail;

class ClubManagerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $clubs = ClubLecture::where('createur_id', $user->id)->get();
        $totalEvents = Evenement::whereIn('club_id', $clubs->pluck('id'))->count();

        // Compter les notifications en attente pour la sidebar - CORRIGÉ
        $pendingCount = Notification::where('user_id', $user->id)
            ->where('type', 'join_request')
            ->where('status', 'pending')
            ->count();

        return view('dashboard_club_manager', compact('clubs', 'totalEvents', 'pendingCount'));
    }

    // CLUB CRUD - MODIFIÉ POUR INTÉGRER LA CRÉATION DANS L'INDEX
    public function indexClub(Request $request)
    {
        $user = Auth::user();
        $clubs = ClubLecture::where('createur_id', $user->id)->get();

        $editClub = null;
        $users = User::all(); // Toujours charger les users pour le formulaire

        if ($request->has('edit')) {
            $editClub = ClubLecture::where('createur_id', $user->id)
                ->findOrFail($request->edit);
        }

        return view('clublecture.index', compact('clubs', 'editClub', 'users'));
    }

    public function storeClub(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'createur_id' => 'required|exists:users,id',
        ]);

        // Vérifier que l'utilisateur ne peut créer que pour lui-même
        if ($request->createur_id != $user->id) {
            return redirect()->back()->withErrors(['createur_id' => 'Vous ne pouvez créer un club que pour vous-même.'])->withInput();
        }

        ClubLecture::create($request->all());

        return redirect()->route('club_manager.clubs.index')->with('success', 'Club créé avec succès.');
    }

    public function editClub($id)
    {
        $club = ClubLecture::where('createur_id', Auth::id())->findOrFail($id);
        $users = User::all();
        return view('clublecture.edit', compact('club', 'users'));
    }

    public function updateClub(Request $request, $id)
    {
        $user = Auth::user();
        $club = ClubLecture::where('createur_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'createur_id' => 'required|exists:users,id',
        ]);

        // Vérifier que l'utilisateur ne peut modifier que pour lui-même
        if ($request->createur_id != $user->id) {
            return redirect()->back()->withErrors(['createur_id' => 'Vous ne pouvez assigner un club qu\'à vous-même.'])->withInput();
        }

        $club->update($request->all());
        return redirect()->route('club_manager.clubs.index')->with('success', 'Club modifié avec succès.');
    }

    public function destroyClub($id)
    {
        $club = ClubLecture::where('createur_id', Auth::id())->findOrFail($id);

        // SUPPRIMER MANUELLEMENT LES NOTIFICATIONS LIÉES
        Notification::where('club_id', $club->id)->delete();

        // SUPPRIMER LES ÉVÉNEMENTS LIÉS
        Evenement::where('club_id', $club->id)->delete();

        // PUIS SUPPRIMER LE CLUB
        $club->delete();

        return redirect()->route('club_manager.clubs.index')->with('success', 'Club supprimé avec succès.');
    }

    // EVENT CRUD
    public function indexEvent(Request $request)
    {
        $user = Auth::user();
        $clubs = ClubLecture::where('createur_id', $user->id)->get();

        $editEvent = null;
        if ($request->has('edit')) {
            $editEvent = Evenement::whereIn('club_id', $clubs->pluck('id'))
                ->findOrFail($request->edit);
        }

        $club = null;
        if ($request->has('club_id')) {
            $club = ClubLecture::where('createur_id', $user->id)
                ->findOrFail($request->club_id);
            $evenements = $club->evenements;
        } else {
            $evenements = Evenement::whereIn('club_id', $clubs->pluck('id'))->get();
        }

        return view('evenement.index', compact('evenements', 'clubs', 'club', 'editEvent'));
    }

    public function showClubEvents($clubId)
    {
        $user = Auth::user();
        $club = ClubLecture::where('createur_id', $user->id)->findOrFail($clubId);
        $evenements = $club->evenements;
        $clubs = ClubLecture::where('createur_id', $user->id)->get();

        return view('evenement.index', compact('evenements', 'clubs', 'club'));
    }

    public function createEvent()
    {
        $clubs = ClubLecture::where('createur_id', Auth::id())->get();
        return view('evenement.create', compact('clubs'));
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_event' => 'required|date',
            'club_id' => 'required|exists:club_lectures,id',
        ]);

        $club = ClubLecture::where('createur_id', Auth::id())
            ->findOrFail($request->club_id);

        Evenement::create($request->all());

        return redirect()->route('club_manager.events.index')->with('success', 'Événement créé avec succès.');
    }

    public function editEvent($id)
    {
        $user = Auth::user();
        $clubs = ClubLecture::where('createur_id', $user->id)->get();
        $event = Evenement::whereIn('club_id', $clubs->pluck('id'))->findOrFail($id);

        return view('evenement.edit', compact('event', 'clubs'));
    }

    public function updateEvent(Request $request, $id)
    {
        $user = Auth::user();
        $clubs = ClubLecture::where('createur_id', $user->id)->get();
        $event = Evenement::whereIn('club_id', $clubs->pluck('id'))->findOrFail($id);

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_event' => 'required|date',
            'club_id' => 'required|exists:club_lectures,id',
        ]);

        $club = ClubLecture::where('createur_id', Auth::id())
            ->findOrFail($request->club_id);

        $event->update($request->all());
        return redirect()->route('club_manager.events.index')->with('success', 'Événement modifié avec succès.');
    }

    public function destroyEvent($id)
    {
        $user = Auth::user();
        $clubs = ClubLecture::where('createur_id', $user->id)->get();
        $event = Evenement::whereIn('club_id', $clubs->pluck('id'))->findOrFail($id);
        $event->delete();

        return redirect()->route('club_manager.events.index')->with('success', 'Événement supprimé avec succès.');
    }

    // AJOUTER LES MÉTHODES DE PROFIL SI NÉCESSAIRE
    public function profile()
    {
        $user = Auth::user();

        // Statistiques spécifiques au club manager
        $stats = [
            'managedClubs' => ClubLecture::where('createur_id', $user->id)->count(),
            'totalEvents' => Evenement::whereIn(
                'club_id',
                ClubLecture::where('createur_id', $user->id)->pluck('id')
            )->count(),
        ];

        return view('club_manager.profile', compact('user', 'stats'));
    }

    // GESTION DES MEMBRES
    public function indexMembers()
    {
        $user = Auth::user();

        // Récupérer tous les clubs du manager
        $clubs = ClubLecture::where('createur_id', $user->id)->get();

        // Récupérer tous les membres de tous les clubs du manager
        $members = ClubMember::whereIn('club_id', $clubs->pluck('id'))
            ->with(['user', 'club'])
            ->where('status', 'active')
            ->get();

        return view('club_manager.members.index', compact('members', 'clubs'));
    }

    public function removeMember($memberId)
    {
        try {
            $user = Auth::user();

            // Vérifier que le membre appartient bien à un club du manager
            $member = ClubMember::where('id', $memberId)
                ->whereHas('club', function ($query) use ($user) {
                    $query->where('createur_id', $user->id);
                })
                ->firstOrFail();

            // Supprimer le membre
            $member->delete();

            return redirect()->route('club_manager.members.index')
                ->with('success', 'Membre supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('club_manager.members.index')
                ->with('error', 'Erreur lors de la suppression du membre.');
        }
    }

    private function sendImmediateReminderIfNeeded(Evenement $event)
    {
        $eventDate = Carbon::parse($event->date_event);
        $today = Carbon::today();
        $daysDifference = $today->diffInDays($eventDate, false); // false pour avoir une différence signée

        // Si l'événement est dans 2 jours ou moins
        if ($daysDifference >= 0 && $daysDifference <= 2) {
            // Récupérer tous les membres actifs du club
            $members = ClubMember::where('club_id', $event->club_id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            $sentCount = 0;

            foreach ($members as $member) {
                if ($member->user && $member->user->email) {
                    try {
                        // Déterminer le nombre de jours restants
                        $daysUntilEvent = $daysDifference;

                        // Envoyer l'email de rappel
                        Mail::to($member->user->email)
                            ->send(new EventReminderMail($event, $member->user));

                        $sentCount++;

                        // Petite pause pour éviter le spam
                        usleep(100000); // 0.1 seconde
                    } catch (\Exception $e) {
                        Log::error("Erreur envoi email rappel immédiat: " . $e->getMessage());
                    }
                }
            }

            Log::info("{$sentCount} rappels immédiats envoyés pour l'événement: {$event->titre}");
        }
    }
}
