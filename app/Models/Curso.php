<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
