<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['monitor_id', 'area_id', 'titulo', 'descricao', 'requisitos', 'duracao_semanas', 'localizacao', 'regime', 'vagas'])]
class Oferta extends Model
{
    protected $table = 'ofertas';

    protected $attributes = [
        'vagas' => 1,
        'estado' => 'rascunho',
        'regime' => 'presencial',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class, 'monitor_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    // Sem nenhum curso = para qualquer curso
    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'curso_oferta', 'oferta_id', 'curso_id');
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class, 'oferta_id');
    }
}
