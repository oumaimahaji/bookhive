<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\ClubLecture;
use App\Models\ClubMember;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        // Récupérer toutes les demandes de rejoindre les clubs
        $notifications = Notification::with(['applicant', 'club.createur'])
            ->where('type', 'join_request')
            ->orderBy('created_at', 'desc')
            ->get();

        $clubs = ClubLecture::all();
        $editNotification = null;

        if ($request->has('edit')) {
            $editNotification = Notification::findOrFail($request->edit);
        }

        return view('admin.notifications.index', compact('notifications', 'clubs', 'editNotification'));
    }

    public function accept(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $notification = Notification::findOrFail($id);

        // Ajouter l'utilisateur comme membre du club
        ClubMember::create([
            'club_id' => $notification->club_id,
            'user_id' => $notification->applicant_id,
            'status' => 'active',
            'joined_at' => now()
        ]);

        // Mettre à jour le statut de la notification
        $notification->update([
            'status' => 'accepted',
            'read_at' => now()
        ]);

        // Créer une notification pour l'utilisateur
        Notification::create([
            'user_id' => $notification->applicant_id,
            'club_id' => $notification->club_id,
            'type' => 'join_approved',
            'message' => "Votre demande pour rejoindre le club '{$notification->club->nom}' a été acceptée!",
            'status' => 'accepted'
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Demande acceptée avec succès. L\'utilisateur est maintenant membre du club.');
    }

    public function reject(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $notification = Notification::findOrFail($id);

        // Mettre à jour le statut de la notification
        $notification->update([
            'status' => 'rejected',
            'read_at' => now()
        ]);

        // Créer une notification pour l'utilisateur
        Notification::create([
            'user_id' => $notification->applicant_id,
            'club_id' => $notification->club_id,
            'type' => 'join_rejected',
            'message' => "Votre demande pour rejoindre le club '{$notification->club->nom}' a été refusée.",
            'status' => 'rejected'
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Demande refusée.');
    }

    public function destroy($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access for administrators.');
        }

        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }

    // Statistiques pour le dashboard
    public function getStats()
    {
        $pendingRequests = Notification::where('type', 'join_request')
            ->where('status', 'pending')
            ->count();

        $totalRequests = Notification::where('type', 'join_request')->count();
        $acceptedRequests = Notification::where('type', 'join_request')
            ->where('status', 'accepted')
            ->count();
        $rejectedRequests = Notification::where('type', 'join_request')
            ->where('status', 'rejected')
            ->count();

        return [
            'pending_requests' => $pendingRequests,
            'total_requests' => $totalRequests,
            'accepted_requests' => $acceptedRequests,
            'rejected_requests' => $rejectedRequests,
        ];
    }
}