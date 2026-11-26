<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * As empresas só recebem o email de verificação quando conseguem entrar,
 * ou seja, no primeiro login depois de a coordenação as aprovar.
 */
class EnviarVerificacaoDeEmail
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
    }
}
