<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['destinatario_id', 'conteudo'])]
class Mensagem extends Model
{
    protected $table = 'mensagens';
    public $timestamps = false;

    protected $attributes = [
        'lida' => false,
    ];

    protected function casts(): array
    {
        return [
            'data_envio' => 'datetime',
            'lida' => 'boolean',
        ];
    }

    public function remetente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }
}
