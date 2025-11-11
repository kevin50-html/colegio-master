<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('materias') || !$this->columnExists('materias', 'curso_id')) {
            return;
        }

        if ($this->isSqliteConnection()) {
            $this->rebuildMateriasTableWithoutCursoId();

            return;
        }

        Schema::table('materias', function (Blueprint $table) {
            if (!$this->columnExists('materias', 'curso_id')) {
                return;
            }

            try {
                if (method_exists($table, 'dropConstrainedForeignId')) {
                    $table->dropConstrainedForeignId('curso_id');

                    return;
                }

                $table->dropForeign(['curso_id']);
            } catch (\Throwable $exception) {
                // The constraint might not exist anymore. Continue with the drop column attempt below.
            }

            $table->dropColumn('curso_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('materias') || $this->columnExists('materias', 'curso_id')) {
            return;
        }

        if ($this->isSqliteConnection()) {
            $this->rebuildMateriasTableWithCursoId();

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

    private function columnExists(string $table, string $column): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        if ($this->isSqliteConnection()) {
            return collect(DB::select("PRAGMA table_info('{$table}')"))
                ->pluck('name')
                ->contains($column);
        }

        return Schema::hasColumn($table, $column);
    }

    private function rebuildMateriasTableWithoutCursoId(): void
    {
        DB::connection()->transaction(function () {
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement('CREATE TABLE materias_tmp (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                nombre VARCHAR(255) NOT NULL UNIQUE,
                codigo VARCHAR(255) NULL UNIQUE,
                intensidad_horaria INTEGER NULL,
                descripcion TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )');

            DB::statement('INSERT INTO materias_tmp (id, nombre, codigo, intensidad_horaria, descripcion, created_at, updated_at)
                SELECT id, nombre, codigo, intensidad_horaria, descripcion, created_at, updated_at FROM materias');

            DB::statement('DROP TABLE materias');
            DB::statement('ALTER TABLE materias_tmp RENAME TO materias');

            DB::statement('PRAGMA foreign_keys = ON');
        });
    }

    private function rebuildMateriasTableWithCursoId(): void
    {
        DB::connection()->transaction(function () {
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement('CREATE TABLE materias_tmp (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                nombre VARCHAR(255) NOT NULL UNIQUE,
                codigo VARCHAR(255) NULL UNIQUE,
                intensidad_horaria INTEGER NULL,
                descripcion TEXT NULL,
                curso_id INTEGER NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                CONSTRAINT materias_curso_id_foreign FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE SET NULL
            )');

            DB::statement('INSERT INTO materias_tmp (id, nombre, codigo, intensidad_horaria, descripcion, curso_id, created_at, updated_at)
                SELECT id, nombre, codigo, intensidad_horaria, descripcion, curso_id, created_at, updated_at FROM materias');

            DB::statement('DROP TABLE materias');
            DB::statement('ALTER TABLE materias_tmp RENAME TO materias');

            DB::statement('PRAGMA foreign_keys = ON');
        });
    }
};
