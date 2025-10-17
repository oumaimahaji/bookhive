<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClubLecture;
use App\Models\Evenement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminClubEventController extends Controller
{
    // CLUB MANAGEMENT METHODS

    public function indexClub(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $clubs = ClubLecture::with(['createur', 'evenements'])
            ->withCount('evenements')
            ->latest()
            ->get();

        $editClub = null;
        $clubManagers = User::where('role', 'club_manager')->get();

        if ($request->has('edit')) {
            $editClub = ClubLecture::findOrFail($request->edit);
        }

        return view('admin.clubs.index', compact('clubs', 'editClub', 'clubManagers'));
    }

    public function storeClub(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $request->validate([
            'nom' => 'required|string|max:255|unique:club_lectures,nom',
            'description' => 'required|string',
            'createur_id' => 'required|exists:users,id',
        ]);

        ClubLecture::create($request->all());

        return redirect()->route('admin.clubs.index')->with('success', 'Club créé avec succès.');
    }

    public function updateClub(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $club = ClubLecture::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255|unique:club_lectures,nom,' . $id,
            'description' => 'required|string',
            'createur_id' => 'required|exists:users,id',
        ]);

        $club->update($request->all());
        return redirect()->route('admin.clubs.index')->with('success', 'Club modifié avec succès.');
    }

    public function destroyClub($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $club = ClubLecture::findOrFail($id);
        $club->delete();

        return redirect()->route('admin.clubs.index')->with('success', 'Club supprimé avec succès.');
    }

    // EVENT MANAGEMENT METHODS

    public function indexEvent(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $editEvent = null;
        $clubs = ClubLecture::all();

        if ($request->has('edit')) {
            $editEvent = Evenement::findOrFail($request->edit);
        }

        $club = null;
        if ($request->has('club_id')) {
            $club = ClubLecture::findOrFail($request->club_id);
            $evenements = $club->evenements()->with('club.createur')->latest()->get();
        } else {
            $evenements = Evenement::with('club.createur')->latest()->get();
        }

        return view('admin.events.index', compact('evenements', 'clubs', 'club', 'editEvent'));
    }

    public function storeEvent(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_event' => 'required|date',
            'club_id' => 'required|exists:club_lectures,id',
        ]);

        Evenement::create($request->all());

        return redirect()->route('admin.events.index')->with('success', 'Événement créé avec succès.');
    }

    public function updateEvent(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $event = Evenement::findOrFail($id);

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_event' => 'required|date',
            'club_id' => 'required|exists:club_lectures,id',
        ]);

        $event->update($request->all());
        return redirect()->route('admin.events.index')->with('success', 'Événement modifié avec succès.');
    }

    public function destroyEvent($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $event = Evenement::findOrFail($id);
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Événement supprimé avec succès.');
    }
}