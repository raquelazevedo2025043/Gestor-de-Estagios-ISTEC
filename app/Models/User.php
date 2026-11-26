<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['nome', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'roles' => 'array',
            'desativado_em' => 'datetime',
        ];
    }

    public function temRole(string $role): bool
    {
        return in_array($role, $this->roles ?? [], true);
    }

    public function estaAtivo(): bool
    {
        return $this->desativado_em === null;
    }

    /**
     * Motivo pelo qual o utilizador não pode entrar, ou null se pode.
     */
    public function motivoBloqueio(): ?string
    {
        if (! $this->estaAtivo()) {
            return 'Esta conta está desativada.';
        }

        if ($this->temRole('empresa')) {
            $empresa = $this->empresa;

            if ($empresa?->estado_validacao === 'pendente') {
                return 'A tua empresa ainda está a aguardar aprovação da coordenação.';
            }

            if ($empresa?->estado_validacao === 'rejeitada') {
                return 'O registo da empresa foi rejeitado pela coordenação: '.$empresa->justificacao_rejeicao;
            }
        }

        if ($this->temRole('monitor')) {
            $empresa = $this->monitor?->empresa;

            if ($empresa && $empresa->estado_validacao !== 'aprovada') {
                return 'A empresa ainda não foi aprovada pela coordenação.';
            }
        }

        return null;
    }

    /**
     * Quem ainda não pode entrar não recebe o email de verificação,
     * porque o link só funciona com sessão iniciada.
     */
    public function sendEmailVerificationNotification(): void
    {
        if ($this->motivoBloqueio() !== null) {
            return;
        }

        parent::sendEmailVerificationNotification();
    }

    public function aluno(): HasOne
    {
        return $this->hasOne(Aluno::class, 'user_id');
    }

    public function professor(): HasOne
    {
        return $this->hasOne(Professor::class, 'user_id');
    }

    public function empresa(): HasOne
    {
        return $this->hasOne(Empresa::class, 'user_id');
    }

    public function monitor(): HasOne
    {
        return $this->hasOne(Monitor::class, 'user_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class, 'user_id');
    }

    public function mensagensEnviadas(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'remetente_id');
    }

    public function mensagensRecebidas(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'destinatario_id');
    }
}
