<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reportes_ambientales', function (Blueprint $table) {
            // Validación para evitar error de columna duplicada
            if (!Schema::hasColumn('reportes_ambientales', 'tipo_medicion')) {
                $table->string('tipo_medicion', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reportes_ambientales', function (Blueprint $table) {
            $table->dropColumn('tipo_medicion');
        });
    }
};