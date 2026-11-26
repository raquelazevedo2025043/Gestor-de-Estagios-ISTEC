<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Enviado à conta da empresa quando a coordenação rejeita o registo,
 * com o motivo indicado (RF13).
 */
class EmpresaRejeitada extends Notification
{
    public function __construct(
        public string $justificacao,
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
        return (new MailMessage)
            ->subject('Registo da empresa rejeitado')
            ->greeting("Olá {$notifiable->nome},")
            ->line('O registo da tua empresa foi rejeitado pela coordenação pelo seguinte motivo:')
            ->line($this->justificacao)
            ->line('Se tiveres dúvidas, contacta a coordenação de estágios do ISTEC.');
    }
}
