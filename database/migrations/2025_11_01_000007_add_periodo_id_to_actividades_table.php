<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('actividades') && !Schema::hasColumn('actividades', 'periodo_id')) {
            $connection = Schema::getConnection()->getDriverName();

            Schema::table('actividades', function (Blueprint $table) use ($connection) {
                $table->unsignedBigInteger('periodo_id')->nullable();

                if ($connection !== 'sqlite') {
                    $table->foreign('periodo_id')->references('id')->on('periodos')->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('actividades') && Schema::hasColumn('actividades', 'periodo_id')) {
            $connection = Schema::getConnection()->getDriverName();

            Schema::table('actividades', function (Blueprint $table) use ($connection) {
                if ($connection !== 'sqlite') {
                    $table->dropForeign(['periodo_id']);
                }

                $table->dropColumn('periodo_id');
            });
        }
    }
};