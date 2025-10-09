<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClubLecture;
use App\Models\Evenement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

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




    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Statistiques spécifiques au club manager
        $stats = [
            'managedClubs' => $user->managedClubs()->count(),
            'totalEvents' => $user->managedEvents()->count(),
            'clubMembers' => \App\Models\ClubMember::whereIn('club_id', $user->managedClubs()->pluck('id'))->count(),
        ];

        return view('club_manager.profile', compact('user', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return redirect()->route('club_manager.profile')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Update Profile Error: ' . $e->getMessage());
            return redirect()->route('club_manager.profile')->with('error', 'Error updating profile.');
        }
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        try {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->route('club_manager.profile')->with('error', 'Current password is incorrect.');
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return redirect()->route('club_manager.profile')->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Update Password Error: ' . $e->getMessage());
            return redirect()->route('club_manager.profile')->with('error', 'Error updating password.');
        }
    }
}
