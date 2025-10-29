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
        'horario_id',
        'titulo',
        'fecha_entrega',
        'porcentaje',
        'descripcion',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class, 'actividad_id');
    }
}
