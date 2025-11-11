<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('materias') || !Schema::hasColumn('materias', 'curso_id')) {
            return;
        }

        if ($this->isSqliteConnection()) {
            $this->rebuildMateriasTableWithoutCursoId();

            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::table('materias', function (Blueprint $table) {
            if (!Schema::hasColumn('materias', 'curso_id')) {
                return;
            }

            try {
                $table->dropForeign(['curso_id']);
            } catch (\Throwable $exception) {
                // Ignore missing constraint definitions; the drop column below is the goal.
            }

            $table->dropColumn('curso_id');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        if (!Schema::hasTable('materias') || Schema::hasColumn('materias', 'curso_id')) {
            return;
        }

        if ($this->isSqliteConnection()) {
            Schema::table('materias', function (Blueprint $table) {
                if (Schema::hasColumn('materias', 'curso_id')) {
                    return;
                }

                $table->foreignId('curso_id')->nullable();
            });

            return;
        }

        Schema::table('materias', function (Blueprint $table) {
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->nullOnDelete();
        });
    }

    private function isSqliteConnection(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }

    private function rebuildMateriasTableWithoutCursoId(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('materias_tmp');

        Schema::create('materias_tmp', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('codigo')->nullable()->unique();
            $table->unsignedTinyInteger('intensidad_horaria')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        DB::table('materias')->select([
            'id',
            'nombre',
            'codigo',
            'intensidad_horaria',
            'descripcion',
            'created_at',
            'updated_at',
        ])->orderBy('id')->chunk(100, function ($materias) {
            $payload = $materias->map(function ($materia) {
                return (array) $materia;
            })->all();

            if (!empty($payload)) {
                DB::table('materias_tmp')->insert($payload);
            }
        });

        Schema::drop('materias');
        Schema::rename('materias_tmp', 'materias');

        Schema::enableForeignKeyConstraints();
    }
};
