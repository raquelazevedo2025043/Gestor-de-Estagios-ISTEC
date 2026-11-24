<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['oferta_id'])]
class Candidatura extends Model
{
    protected $table = 'candidaturas';
    public $timestamps = false;

    protected $attributes = [
        'estado' => 'submetida',
    ];

    protected function casts(): array
    {
        return [
            'data_candidatura' => 'date',
            'data_aceitacao_empresa' => 'datetime',
            'prazo_resposta' => 'datetime', // calculado pela base de dados: não escrever
            'data_resposta_aluno' => 'datetime',
        ];
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class, 'oferta_id');
    }

    // Criado pelo trigger quando a candidatura fica aceite
    public function estagio(): HasOne
    {
        return $this->hasOne(Estagio::class, 'candidatura_id');
    }
}
