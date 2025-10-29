<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_materia_id',
        'periodo_id',
        'dia',
        'hora_inicio',
        'hora_fin',
        'aula',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function cursoMateria(): BelongsTo
    {
        return $this->belongsTo(CursoMateria::class, 'curso_materia_id');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }
}
