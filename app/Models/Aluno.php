<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['numero', 'curso_id', 'ano', 'curriculo'])]
class Aluno extends Model
{
    protected $table = 'alunos';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class, 'aluno_id');
    }

    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'aluno_id');
    }
}
