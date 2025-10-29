<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Horario extends Model
{
    use HasFactory;

    protected static ?string $diaColumn = null;

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

    public function getDiaAttribute($value): ?string
    {
        if ($value !== null) {
            return $value;
        }

        $column = self::diaColumn();

        if ($column !== 'dia' && array_key_exists($column, $this->attributes)) {
            return $this->attributes[$column];
        }

        return null;
    }

    public function setDiaAttribute($value): void
    {
        $this->attributes[self::diaColumn()] = $value;
    }

    public static function diaColumn(): string
    {
        if (self::$diaColumn === null) {
            $instance = new self();

            if (!Schema::hasTable($instance->getTable())) {
                self::$diaColumn = 'dia';
            } else {
                self::$diaColumn = Schema::hasColumn($instance->getTable(), 'dia') ? 'dia' : 'dia_semana';
            }
        }

        return self::$diaColumn;
    }

    public function cursoMateria(): BelongsTo
    {
        return $this->belongsTo(CursoMateria::class, 'curso_materia_id');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }
}
