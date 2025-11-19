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
                $table->unique(['curso_materia_id', 'dia', 'hora_inicio', 'hora_fin'], 'horarios_curso_dia_intervalo_unique');
            });

            return;
        }

        if (!Schema::hasColumn('horarios', 'curso_materia_id')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->foreignId('curso_materia_id')->nullable()->after('id');
            });

            Schema::table('horarios', function (Blueprint $table) {
                $table->foreign('curso_materia_id')->references('id')->on('curso_materia')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('horarios', 'periodo_id')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->dropForeign(['periodo_id']);
            });

            Schema::table('horarios', function (Blueprint $table) {
                $table->foreignId('periodo_id')->nullable()->change();
            });

            Schema::table('horarios', function (Blueprint $table) {
                $table->foreign('periodo_id')->references('id')->on('periodos')->nullOnDelete();
            });
        } else {
            Schema::table('horarios', function (Blueprint $table) {
                $table->foreignId('periodo_id')->nullable()->after('curso_materia_id')->constrained('periodos')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('horarios', 'dia') && Schema::hasColumn('horarios', 'dia_semana')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->renameColumn('dia_semana', 'dia');
            });
        } elseif (!Schema::hasColumn('horarios', 'dia')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->enum('dia', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'])->after('periodo_id');
            });
        }

        if (!Schema::hasColumn('horarios', 'hora_inicio')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->time('hora_inicio')->after('dia');
            });
        }

        if (!Schema::hasColumn('horarios', 'hora_fin')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->time('hora_fin')->after('hora_inicio');
            });
        }

        if (!Schema::hasColumn('horarios', 'aula')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->string('aula')->nullable()->after('hora_fin');
            });
        }

    }

    public function down(): void
    {
        if (!Schema::hasTable('horarios')) {
            return;
        }

        if (Schema::hasColumn('horarios', 'curso_materia_id')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->dropForeign(['curso_materia_id']);
                $table->dropColumn('curso_materia_id');
            });
        }

        if (Schema::hasColumn('horarios', 'dia') && !Schema::hasColumn('horarios', 'dia_semana')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->renameColumn('dia', 'dia_semana');
            });
        }

        if (Schema::hasColumn('horarios', 'periodo_id')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->dropForeign(['periodo_id']);
                $table->foreignId('periodo_id')->nullable(false)->change();
                $table->foreign('periodo_id')->references('id')->on('periodos')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('horarios', 'aula')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->dropColumn('aula');
            });
        }
    }
};
