<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nome', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
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
