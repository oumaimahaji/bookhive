<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evenement;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReminderMail;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send email reminders for events happening in 2 days';

    public function handle()
    {
        $today = Carbon::today();
        $targetDate = $today->addDays(2);

        $this->info("Recherche des événements pour le: " . $targetDate->format('Y-m-d'));

        // Récupérer les événements dans 2 jours
        $events = Evenement::whereDate('date_event', $targetDate->toDateString())
            ->with('club') // Charger la relation club
            ->get();

        $this->info("Événements trouvés: " . $events->count());

        $sentCount = 0;

        foreach ($events as $event) {
            $this->info("Traitement de l'événement: {$event->titre} (Club: {$event->club->nom})");

            // Récupérer tous les membres actifs du club via ClubMember
            $members = ClubMember::where('club_id', $event->club_id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            $this->info("Membres actifs trouvés: " . $members->count());

            foreach ($members as $member) {
                if ($member->user && $member->user->email) {
                    $this->info("Envoi d'email à: " . $member->user->email);
                    
                    try {
                        // Envoyer l'email de rappel
                        Mail::to($member->user->email)
                            ->send(new EventReminderMail($event, $member->user));
                        
                        $sentCount++;
                        $this->info("✓ Email envoyé avec succès à: " . $member->user->email);
                    } catch (\Exception $e) {
                        $this->error("✗ Erreur lors de l'envoi à {$member->user->email}: " . $e->getMessage());
                    }
                } else {
                    $this->warn("Membre sans email: User ID " . $member->user_id);
                }
            }
        }

        if ($sentCount > 0) {
            $this->info("✅ {$sentCount} emails de rappel envoyés avec succès.");
        } else {
            $this->info("ℹ️  Aucun email à envoyer.");
        }
        
        return Command::SUCCESS;
    }
}