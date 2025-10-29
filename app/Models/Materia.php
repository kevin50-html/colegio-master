<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_id',
        'nombre',
        'codigo',
        'intensidad_horaria',
        'descripcion',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function periodos(): HasMany
    {
        return $this->hasMany(Periodo::class, 'materia_id')->orderBy('orden');
    }
}
