<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('curso_materia')) {
            Schema::create('curso_materia', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
                $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
                $table->string('alias')->nullable();
                $table->timestamps();
                $table->unique(['curso_id', 'materia_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curso_materia');
    }
};
