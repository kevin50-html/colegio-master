<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatriculaAcudiente extends Model
{
    use HasFactory;

    protected $table = 'matriculas_acudientes';

    protected $fillable = [
        'user_id',
        'estudiante_id',
        'estudiante_registro_id',
        'curso_id',
        'nombres',
        'apellidos',
        'documento_identidad',
        'email',
        'telefono',
        'documentos',
        'estado',
    ];

    protected $casts = [
        'documentos' => 'array',
    ];

    public function acudiente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    public function estudianteRegistro(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_registro_id');
    }
}
