<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'intensidad_horaria',
        'descripcion',
    ];

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'curso_materia')->withPivot(['id', 'alias'])->withTimestamps()->orderBy('cursos.nombre');
    }

    public function cursoMaterias(): HasMany
    {
        return $this->hasMany(CursoMateria::class, 'materia_id');
    }
}
