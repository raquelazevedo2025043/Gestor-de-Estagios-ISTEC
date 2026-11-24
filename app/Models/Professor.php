<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['departamento'])]
class Professor extends Model
{
    protected $table = 'professores';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'professor_id');
    }
}
