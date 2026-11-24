<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nif', 'nome', 'morada', 'telefone'])]
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

    // Monitor responsável pela empresa
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Monitor::class, 'responsavel_id');
    }

    // Membro da coordenação que aprovou ou rejeitou
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
}
