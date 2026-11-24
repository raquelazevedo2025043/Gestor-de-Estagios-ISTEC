<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['classificacao', 'comentario'])]
class AvaliacaoEmpresa extends Model
{
    protected $table = 'avaliacoes_empresa';
    const UPDATED_AT = null;

    public function estagio(): BelongsTo
    {
        return $this->belongsTo(Estagio::class, 'estagio_id');
    }
}
