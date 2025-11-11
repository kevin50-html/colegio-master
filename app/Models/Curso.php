<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $fillable = ['nombre'];

    /**
     * Teachers assigned to the course.
     */
    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(Docente::class, 'curso_docente')->withTimestamps();
    }

    /**
     * Students enrolled in the course.
     */
    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiante::class, 'curso_id');
    }

    /**
     * Enrollment requests associated with the course.
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(MatriculaAcudiente::class, 'curso_id');
    }

    /**
     * Subjects linked to the course through the pivot entity.
     */
    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class, 'curso_materia')->withPivot(['id', 'alias'])->withTimestamps()->orderBy('materias.nombre');
    }

    /**
     * Pivot records for the course.
     */
    public function cursoMaterias(): HasMany
    {
        return $this->hasMany(CursoMateria::class, 'curso_id');
    }
}
