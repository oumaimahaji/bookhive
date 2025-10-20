<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Evenement;
use App\Models\User;

class EventReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $user;
    public $daysUntilEvent;

    public function __construct(Evenement $event, User $user)
    {
        $this->event = $event;
        $this->user = $user;
        $this->daysUntilEvent = 2; // Rappel 2 jours avant
    }

    public function build()
    {
        return $this->subject('Rappel : Événement dans 2 jours - ' . $this->event->titre)
                    ->view('emails.event_reminder')
                    ->with([
                        'event' => $this->event,
                        'user' => $this->user,
                        'daysUntilEvent' => $this->daysUntilEvent,
                    ]);
    }
}
