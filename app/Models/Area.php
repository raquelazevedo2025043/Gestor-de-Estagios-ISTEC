<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// tipo de trabalho do estágio (ex.: front-end, QA, redes)
#[Fillable(['nome'])]
class Area extends Model
{
    protected $table = 'areas';
    public $timestamps = false;

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class, 'area_id');
    }
}
