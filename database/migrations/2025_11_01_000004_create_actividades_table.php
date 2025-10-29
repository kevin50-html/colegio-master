<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('actividades')) {
            Schema::create('actividades', function (Blueprint $table) {
                $table->id();
                $table->foreignId('horario_id')->constrained('horarios')->cascadeOnDelete();
                $table->string('titulo');
                $table->date('fecha_entrega')->nullable();
                $table->unsignedTinyInteger('porcentaje')->nullable();
                $table->text('descripcion')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
