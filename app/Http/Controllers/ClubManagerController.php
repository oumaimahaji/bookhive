<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClubLecture;
use App\Models\Evenement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClubManagerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $clubs = ClubLecture::where('createur_id', $user->id)->get();
        $totalEvents = Evenement::whereIn('club_id', $clubs->pluck('id'))->count();
        
        return view('dashboard_club_manager', compact('clubs', 'totalEvents'));
    }

    // CLUB CRUD (garder tel quel)
    public function indexClub(Request $request)
    {
        $user = Auth::user();
        $clubs = ClubLecture::where('createur_id', $user->id)->get();
        
        $editClub = null;
        $users = null;
        if ($request->has('edit')) {
            $editClub = ClubLecture::where('createur_id', $user->id)
                                  ->findOrFail($request->edit);
            $users = User::all();
        }
        
        return view('clublecture.index', compact('clubs', 'editClub', 'users'));
    }

    public function createClub()
    {
        $users = User::all();
        return view('clublecture.create', compact('users'));
    }

    public function storeClub(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'createur_id' => 'required|exists:users,id',
        ]);

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
        $club = ClubLecture::where('createur_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'createur_id' => 'required|exists:users,id',
        ]);

        $club->update($request->all());
        return redirect()->route('club_manager.clubs.index')->with('success', 'Club modifié avec succès.');
    }

    public function destroyClub($id)
    {
        $club = ClubLecture::where('createur_id', Auth::id())->findOrFail($id);
        $club->delete();

        return redirect()->route('club_manager.clubs.index')->with('success', 'Club supprimé avec succès.');
    }

    // EVENT CRUD - SIMPLIFIÉ comme le CRUD club
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

        // Vérifier que l'utilisateur peut créer un événement pour ce club
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

        // Vérifier que le nouveau club appartient aussi à l'utilisateur
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
}