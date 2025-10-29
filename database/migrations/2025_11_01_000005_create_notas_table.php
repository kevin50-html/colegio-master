<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notas')) {
            Schema::create('notas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
                $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
                $table->decimal('valor', 5, 2)->nullable();
                $table->text('observaciones')->nullable();
                $table->timestamps();
                $table->unique(['actividad_id', 'estudiante_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
