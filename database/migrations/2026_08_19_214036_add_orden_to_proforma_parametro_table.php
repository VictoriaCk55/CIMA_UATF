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
        Schema::table('proforma_parametro', function (Blueprint $table) {
            // Agregamos la columna 'orden' de tipo entero (integer)
            // La dejamos nullable porque las proformas existentes no tienen este dato
            $table->integer('orden')->nullable()->after('metodo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proforma_parametro', function (Blueprint $table) {
            // Si revertimos la migración, eliminamos la columna
            $table->dropColumn('orden');
        });
    }
};