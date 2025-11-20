<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matriculas_acudientes', function (Blueprint $table) {
            $table->foreignId('estudiante_registro_id')
                ->nullable()
                ->after('estudiante_id')
                ->constrained('estudiantes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matriculas_acudientes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('estudiante_registro_id');
        });
    }
};
