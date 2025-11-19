<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('actividades') || !Schema::hasColumn('actividades', 'horario_id')) {
            return;
        }

        $connection = Schema::getConnection()->getDriverName();

        Schema::table('actividades', function (Blueprint $table) use ($connection) {
            if ($connection !== 'sqlite') {
                $table->dropForeign(['horario_id']);
            }
        });

        if ($connection === 'sqlite') {
            Schema::table('actividades', function (Blueprint $table) {
                $table->dropColumn('horario_id');
            });

            Schema::table('actividades', function (Blueprint $table) {
                $table->foreignId('horario_id')->nullable()->constrained('horarios')->nullOnDelete();
            });
        } else {
            Schema::table('actividades', function (Blueprint $table) {
                $table->unsignedBigInteger('horario_id')->nullable()->change();
            });

            Schema::table('actividades', function (Blueprint $table) {
                $table->foreign('horario_id')->references('id')->on('horarios')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('actividades') || !Schema::hasColumn('actividades', 'horario_id')) {
            return;
        }

        $connection = Schema::getConnection()->getDriverName();

        Schema::table('actividades', function (Blueprint $table) use ($connection) {
            if ($connection !== 'sqlite') {
                $table->dropForeign(['horario_id']);
            }
        });

        if ($connection === 'sqlite') {
            Schema::table('actividades', function (Blueprint $table) {
                $table->dropColumn('horario_id');
            });

            Schema::table('actividades', function (Blueprint $table) {
                $table->foreignId('horario_id')->constrained('horarios')->cascadeOnDelete();
            });
        } else {
            Schema::table('actividades', function (Blueprint $table) {
                $table->unsignedBigInteger('horario_id')->nullable(false)->change();
            });

            Schema::table('actividades', function (Blueprint $table) {
                $table->foreign('horario_id')->references('id')->on('horarios')->cascadeOnDelete();
            });
        }
    }
};
