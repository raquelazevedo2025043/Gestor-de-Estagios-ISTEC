<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estagio extends Model
{
    protected $table = 'estagios';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'data_atribuicao_professor' => 'datetime',
            'data_inicio' => 'date',
            'data_fim' => 'date',
        ];
    }

    public function candidatura(): BelongsTo
    {
        return $this->belongsTo(Candidatura::class, 'candidatura_id');
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'professor_id');
    }

    // Membro da coordenação que atribuiu o orientador
    public function atribuidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_atribuido_por');
    }

    // Monitor atual
    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class, 'monitor_id');
    }

    public function historicoMonitores(): HasMany
    {
        return $this->hasMany(EstagioMonitor::class, 'estagio_id');
    }

    public function observacoes(): HasMany
    {
        return $this->hasMany(Observacao::class, 'estagio_id');
    }

    public function avaliacaoEmpresa(): HasOne
    {
        return $this->hasOne(AvaliacaoEmpresa::class, 'estagio_id');
    }

    public function avaliacoesFinais(): HasMany
    {
        return $this->hasMany(AvaliacaoFinal::class, 'estagio_id');
    }
}
