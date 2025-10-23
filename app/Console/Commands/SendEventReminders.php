<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evenement;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EventReminderMail;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send email reminders for events happening in 2 days';

    public function handle()
    {
        $today = Carbon::today();
        $targetDate = $today->copy()->addDays(2);

        $this->info("Recherche des événements pour le: " . $targetDate->format('Y-m-d'));

        // Récupérer les événements dans 2 jours
        $events = Evenement::whereDate('date_event', $targetDate->toDateString())
            ->with('club')
            ->get();

        $this->info("Événements trouvés: " . $events->count());

        $sentCount = 0;
        $failedCount = 0;

        foreach ($events as $event) {
            // Vérifier si le club existe
            if (!$event->club) {
                $this->warn("Club non trouvé pour l'événement: {$event->titre}");
                continue;
            }

            $this->info("Traitement de l'événement: {$event->titre} (Club: {$event->club->nom})");

            // Récupérer tous les membres actifs du club via ClubMember
            $members = ClubMember::where('club_id', $event->club_id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            $this->info("Membres actifs trouvés: " . $members->count());

            foreach ($members as $member) {
                if (!$member->user) {
                    $this->warn("Utilisateur non trouvé pour le membre ID: {$member->id}");
                    continue;
                }

                if (!$member->user->email) {
                    $this->warn("Email manquant pour l'utilisateur: {$member->user->name}");
                    continue;
                }

                $this->info("Tentative d'envoi à: " . $member->user->email);
                
                try {
                    // Test de la configuration email
                    $this->info("Configuration MAIL_MAILER: " . config('mail.default'));
                    $this->info("Configuration MAIL_HOST: " . config('mail.mailers.smtp.host'));
                    
                    // Envoyer l'email de rappel
                    Mail::to($member->user->email)
                        ->send(new EventReminderMail($event, $member->user));
                    
                    $sentCount++;
                    $this->info("✓ Email envoyé avec succès à: " . $member->user->email);
                    
                    // Pause plus courte pour éviter les timeouts
                    usleep(100000); // 0.1 seconde
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    $this->error("✗ Erreur lors de l'envoi à {$member->user->email}: " . $e->getMessage());
                    
                    // Log détaillé de l'erreur
                    Log::error("Erreur envoi email rappel événement", [
                        'email' => $member->user->email,
                        'event' => $event->titre,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        // Rapport final
        if ($sentCount > 0) {
            $this->info("✅ {$sentCount} emails de rappel envoyés avec succès.");
        }
        if ($failedCount > 0) {
            $this->error("❌ {$failedCount} emails ont échoué. Voir les logs pour plus de détails.");
        }
        if ($sentCount === 0 && $failedCount === 0) {
            $this->info("ℹ️  Aucun email à envoyer.");
        }
        
        return Command::SUCCESS;
    }
}