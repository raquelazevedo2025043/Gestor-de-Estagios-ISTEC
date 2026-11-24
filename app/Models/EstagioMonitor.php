<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstagioMonitor extends Model
{
    protected $table = 'estagio_monitores';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
        ];
    }

    public function estagio(): BelongsTo
    {
        return $this->belongsTo(Estagio::class, 'estagio_id');
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class, 'monitor_id');
    }
}
