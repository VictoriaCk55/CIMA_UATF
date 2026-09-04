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
        // Verificar si la columna ya existe antes de agregarla
        if (!Schema::hasColumn('proformas', 'fecha_muestreo')) {
            Schema::table('proformas', function (Blueprint $table) {
                $table->date('fecha_muestreo')->nullable()->after('fecha_recepcion');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('proformas', 'fecha_muestreo')) {
            Schema::table('proformas', function (Blueprint $table) {
                $table->dropColumn('fecha_muestreo');
            });
        }
    }
};
