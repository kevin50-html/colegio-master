<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_materia_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'orden',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function cursoMateria(): BelongsTo
    {
        return $this->belongsTo(CursoMateria::class, 'curso_materia_id');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'periodo_id')->orderBy('dia');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'periodo_id')->orderBy('fecha_entrega');
    }
}
