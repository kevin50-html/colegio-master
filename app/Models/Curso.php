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
     * Subjects assigned to the course.
     */
    public function materias(): HasMany
    {
        return $this->hasMany(Materia::class, 'curso_id')->orderBy('nombre');
    }
}
