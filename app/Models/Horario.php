<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'periodo_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'aula',
        'modalidad',
    ];

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'horario_id')->orderBy('fecha_entrega');
    }
}
