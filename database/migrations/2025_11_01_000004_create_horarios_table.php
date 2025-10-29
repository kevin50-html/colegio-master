<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('horarios')) {
            Schema::create('horarios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curso_materia_id')->constrained('curso_materia')->cascadeOnDelete();
                $table->foreignId('periodo_id')->nullable()->constrained('periodos')->nullOnDelete();
                $table->enum('dia', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->string('aula')->nullable();
                $table->timestamps();
                $table->unique(['curso_materia_id', 'dia', 'hora_inicio', 'hora_fin']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
