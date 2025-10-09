<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\ClubLecture;
use App\Models\ClubMember;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer les notifications pour le manager
        $notifications = Notification::with(['applicant', 'club'])
            ->where('user_id', $user->id)
            ->where('type', 'join_request')
            ->orderBy('created_at', 'desc')
            ->get();

        $clubs = ClubLecture::where('createur_id', $user->id)->get();
        $editNotification = null;

        if ($request->has('edit')) {
            $editNotification = Notification::where('user_id', $user->id)
                ->findOrFail($request->edit);
        }

        return view('notification.index', compact('notifications', 'clubs', 'editNotification'));
    }

    public function accept(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

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

        return redirect()->route('club_manager.notifications.index')
            ->with('success', 'Demande acceptée avec succès. L\'utilisateur est maintenant membre du club.');
    }

    public function reject(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

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

        return redirect()->route('club_manager.notifications.index')
            ->with('success', 'Demande refusée.');
    }

    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        $notification->delete();

        return redirect()->route('club_manager.notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }
}