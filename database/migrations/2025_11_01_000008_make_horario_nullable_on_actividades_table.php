<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('actividades') || !Schema::hasColumn('actividades', 'horario_id')) {
            return;
        }

        $connection = Schema::getConnection()->getDriverName();

        if ($connection === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            Schema::create('actividades_temp', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('horario_id')->nullable();
                $table->string('titulo');
                $table->date('fecha_entrega')->nullable();
                $table->unsignedTinyInteger('porcentaje')->nullable();
                $table->text('descripcion')->nullable();
                $table->unsignedBigInteger('periodo_id')->nullable();
                $table->timestamps();
            });

            $existingColumns = Schema::getColumnListing('actividades');
            $columnsToCopy = array_values(array_intersect($existingColumns, [
                'id',
                'horario_id',
                'titulo',
                'fecha_entrega',
                'porcentaje',
                'descripcion',
                'periodo_id',
                'created_at',
                'updated_at',
            ]));

            $rows = DB::table('actividades')->get($columnsToCopy);

            foreach ($rows as $row) {
                DB::table('actividades_temp')->insert((array) $row);
            }

            Schema::drop('actividades');
            Schema::rename('actividades_temp', 'actividades');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            Schema::table('actividades', function (Blueprint $table) {
                $table->dropForeign(['horario_id']);
            });

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

        if ($connection === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            Schema::create('actividades_temp', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('horario_id');
                $table->string('titulo');
                $table->date('fecha_entrega')->nullable();
                $table->unsignedTinyInteger('porcentaje')->nullable();
                $table->text('descripcion')->nullable();
                $table->unsignedBigInteger('periodo_id')->nullable();
                $table->timestamps();
            });

            $existingColumns = Schema::getColumnListing('actividades');
            $columnsToCopy = array_values(array_intersect($existingColumns, [
                'id',
                'horario_id',
                'titulo',
                'fecha_entrega',
                'porcentaje',
                'descripcion',
                'periodo_id',
                'created_at',
                'updated_at',
            ]));

            $rows = DB::table('actividades')->get($columnsToCopy);

            foreach ($rows as $row) {
                DB::table('actividades_temp')->insert((array) $row);
            }

            Schema::drop('actividades');
            Schema::rename('actividades_temp', 'actividades');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            Schema::table('actividades', function (Blueprint $table) {
                $table->dropForeign(['horario_id']);
            });

            Schema::table('actividades', function (Blueprint $table) {
                $table->unsignedBigInteger('horario_id')->nullable(false)->change();
            });

            Schema::table('actividades', function (Blueprint $table) {
                $table->foreign('horario_id')->references('id')->on('horarios')->cascadeOnDelete();
            });
        }
    }
};
