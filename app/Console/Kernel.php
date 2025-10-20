<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SendEventReminders::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Envoyer les rappels tous les jours à 9h00
        $schedule->command('events:send-reminders')
                 ->dailyAt('09:00')
                 ->timezone('Africa/Tunis'); // Ajustez le fuseau horaire selon votre besoin

        // Vous pouvez aussi l'exécuter plus fréquemment pour les tests
        // $schedule->command('events:send-reminders')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}