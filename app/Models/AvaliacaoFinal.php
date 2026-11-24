<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nota', 'comentario'])]
class AvaliacaoFinal extends Model
{
    protected $table = 'avaliacoes_finais';
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'nota' => 'decimal:2',
        ];
    }

    public function estagio(): BelongsTo
    {
        return $this->belongsTo(Estagio::class, 'estagio_id');
    }

    public function avaliador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avaliador_id');
    }
}
