<?php

namespace App\Notifications;

use App\Models\Fascicolo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class FascicoloCompletato extends Notification
{
    use Queueable;

    public function __construct(public Fascicolo $fascicolo) {}

    public function via(object $notifiable): array
    {
        $canali = ['database']; // sempre campanellina interna

        // Email se abilitata in settings
        if (\App\Models\Setting::get('notifica_email', '1') === '1') {
            $canali[] = 'mail';
        }

        return $canali;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Fascicolo completato — {$this->fascicolo->cliente->nome}")
            ->greeting("Ciao {$notifiable->name},")
            ->line("Il cliente {$this->fascicolo->cliente->nome} ha completato il caricamento documenti.")
            ->line("Tipo pratica: {$this->fascicolo->tipo_pratica_label}")
            ->action('Apri Fascicolo', route('fascicoli.show', $this->fascicolo))
            ->line('Verifica i documenti caricati e aggiorna lo stato.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'fascicolo_id'   => $this->fascicolo->id,
            'cliente_nome'   => $this->fascicolo->cliente->nome . ' ' . $this->fascicolo->cliente->cognome,
            'tipo_pratica'   => $this->fascicolo->tipo_pratica_label,
            'completato_il'  => $this->fascicolo->completato_il,
            'link'           => route('fascicoli.show', $this->fascicolo),
            'messaggio'      => "Il cliente ha completato il fascicolo {$this->fascicolo->tipo_pratica_label}",
        ];
    }
}