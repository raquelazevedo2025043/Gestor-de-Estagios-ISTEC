<?php

namespace App\Models;

use App\Notifications\DefinirPalavraPasse;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Password;

#[Fillable(['nif', 'nome', 'morada', 'telefone', 'area_atividade'])]
class Empresa extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $attributes = [
        'estado_validacao' => 'pendente',
    ];

    protected function casts(): array
    {
        return [
            'data_validacao' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // monitor responsável pela empresa
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Monitor::class, 'responsavel_id');
    }

    // membro da coordenação que aprovou ou rejeitou
    public function validador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validado_por');
    }

    public function monitores(): HasMany
    {
        return $this->hasMany(Monitor::class, 'empresa_id');
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class, 'empresa_id');
    }

    // aprova a empresa e envia ao responsável o email para definir a palavra-passe
    public function aprovar(User $coordenador): void
    {
        $this->forceFill([
            'estado_validacao' => 'aprovada',
            'justificacao_rejeicao' => null,
            'validado_por' => $coordenador->id,
            'data_validacao' => now(),
        ])->save();

        $responsavel = $this->responsavel?->user;

        if ($responsavel) {
            $token = Password::broker()->createToken($responsavel);
            $responsavel->notify(new DefinirPalavraPasse($token, $this->nome));
        }
    }

    // a justificação é obrigatória (RF13)
    public function rejeitar(User $coordenador, string $justificacao): void
    {
        $this->forceFill([
            'estado_validacao' => 'rejeitada',
            'justificacao_rejeicao' => $justificacao,
            'validado_por' => $coordenador->id,
            'data_validacao' => now(),
        ])->save();
    }
}
