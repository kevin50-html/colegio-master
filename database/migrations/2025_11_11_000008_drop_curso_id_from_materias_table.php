<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('materias') || !Schema::hasColumn('materias', 'curso_id')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::table('materias', function (Blueprint $table) {
            $table->dropColumn('curso_id');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        if (!Schema::hasTable('materias') || Schema::hasColumn('materias', 'curso_id')) {
            return;
        }

        Schema::table('materias', function (Blueprint $table) {
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->nullOnDelete();
        });
    }
};
