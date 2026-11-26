<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Enviado à conta da empresa quando a coordenação aprova o registo.
 */
class EmpresaAprovada extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Registo da empresa aprovado')
            ->greeting("Olá {$notifiable->nome},")
            ->line('O registo da tua empresa foi aprovado pela coordenação.')
            ->line('Já podes entrar na plataforma com o email e a palavra-passe que indicaste no registo.')
            ->action('Entrar', route('login'))
            ->line('O monitor responsável também recebeu um email para definir a palavra-passe da conta dele.');
    }
}
