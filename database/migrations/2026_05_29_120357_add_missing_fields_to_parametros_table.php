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
        Schema::table('parametros', function (Blueprint $table) {
            // Verificamos si la columna NO existe antes de crearla
            if (!Schema::hasColumn('parametros', 'codigo_poe')) {
                $table->string('codigo_poe')->nullable()->after('metodo');
            }

            if (!Schema::hasColumn('parametros', 'limite_cuantificacion')) {
                $table->string('limite_cuantificacion')->nullable()->after('codigo_poe');
            }

            if (!Schema::hasColumn('parametros', 'unidad')) {
                $table->string('unidad')->nullable()->after('limite_cuantificacion');
            }

            if (!Schema::hasColumn('parametros', 'matriz')) {
                $table->string('matriz')->nullable()->after('unidad');
            }

            if (!Schema::hasColumn('parametros', 'tecnica')) {
                $table->string('tecnica')->nullable()->after('matriz');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn(['codigo_poe', 'limite_cuantificacion', 'unidad', 'matriz', 'tecnica']);
        });
    }
};