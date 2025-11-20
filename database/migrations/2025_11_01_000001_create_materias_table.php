<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('materias')) {
            Schema::create('materias', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->string('codigo')->nullable()->unique();
                $table->unsignedTinyInteger('intensidad_horaria')->nullable();
                $table->text('descripcion')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
