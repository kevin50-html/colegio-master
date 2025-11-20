<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Docente extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'documento_identidad',
        'email',
        'telefono',
        'especialidad',
        'fecha_ingreso',
        'estado',
        'observaciones',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    /**
     * Get the full name of the teacher.
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombres ?? '') . ' ' . ($this->apellidos ?? ''));
    }

    /**
     * Courses assigned to the teacher.
     */
    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'curso_docente')->withTimestamps();
    }
}
