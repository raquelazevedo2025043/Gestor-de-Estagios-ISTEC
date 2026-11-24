<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['cargo'])]
class Monitor extends Model
{
    protected $table = 'monitores';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $attributes = [
        'ativo' => true,
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class, 'monitor_id');
    }

    // Estágios que acompanha atualmente
    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'monitor_id');
    }

    // Todos os estágios que já acompanhou
    public function historicoEstagios(): HasMany
    {
        return $this->hasMany(EstagioMonitor::class, 'monitor_id');
    }
}
