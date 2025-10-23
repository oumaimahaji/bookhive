<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
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

    public function __construct(Evenement $event, User $user, $daysUntilEvent = 2)
    {
        $this->event = $event;
        $this->user = $user;
        $this->daysUntilEvent = $daysUntilEvent;
    }

    public function build()
    {
        $subject = $this->daysUntilEvent == 0 
            ? "Rappel : Événement aujourd'hui - {$this->event->titre}"
            : "Rappel : Événement dans {$this->daysUntilEvent} jour(s) - {$this->event->titre}";

        return $this->subject($subject)
                    ->view('emails.event_reminder')
                    ->with([
                        'event' => $this->event,
                        'user' => $this->user,
                        'daysUntilEvent' => $this->daysUntilEvent,
                    ]);
    }
}