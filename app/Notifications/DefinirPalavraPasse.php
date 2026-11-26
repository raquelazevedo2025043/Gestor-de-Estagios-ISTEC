<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Enviado ao monitor responsável quando a coordenação aprova a empresa,
 * para definir a palavra-passe da sua conta.
 */
class DefinirPalavraPasse extends Notification
{
    public function __construct(
        public string $token,
        public string $empresa,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $minutos = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Define a tua palavra-passe')
            ->greeting("Olá {$notifiable->nome},")
            ->line("A empresa {$this->empresa} foi aprovada pela coordenação e és o monitor responsável.")
            ->line('Para entrares na plataforma, define a tua palavra-passe.')
            ->action('Definir palavra-passe', $url)
            ->line("Este link expira em {$minutos} minutos. Depois disso, usa a opção \"Recuperar palavra-passe\" na página de login.");
    }
}
