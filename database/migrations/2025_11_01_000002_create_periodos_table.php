<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('periodos')) {
            Schema::create('periodos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
                $table->string('nombre');
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->unsignedTinyInteger('orden')->default(1);
                $table->text('descripcion')->nullable();
                $table->timestamps();
                $table->unique(['materia_id', 'nombre']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos');
    }
};
