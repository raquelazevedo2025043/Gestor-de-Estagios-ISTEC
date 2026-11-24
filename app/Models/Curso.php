<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome'])]
class Curso extends Model
{
    protected $table = 'cursos';
    public $timestamps = false;

    public function alunos(): HasMany
    {
        return $this->hasMany(Aluno::class, 'curso_id');
    }

    public function ofertas(): BelongsToMany
    {
        return $this->belongsToMany(Oferta::class, 'curso_oferta', 'curso_id', 'oferta_id');
    }
}
