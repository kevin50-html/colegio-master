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
                $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
                $table->string('dia_semana');
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->string('aula')->nullable();
                $table->string('modalidad')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
