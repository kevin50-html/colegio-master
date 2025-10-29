<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'periodo_id',
        'titulo',
        'fecha_entrega',
        'porcentaje',
        'descripcion',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class, 'actividad_id');
    }
}
