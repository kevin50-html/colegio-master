<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoMateria extends Model
{
    use HasFactory;

    protected $table = 'curso_materia';

    protected $fillable = [
        'curso_id',
        'materia_id',
        'alias',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function periodos(): HasMany
    {
        return $this->hasMany(Periodo::class, 'curso_materia_id')->orderBy('orden');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'curso_materia_id');
    }
}
