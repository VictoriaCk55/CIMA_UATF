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
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            
            // Datos institucionales
            $table->string('institucion_nombre')->nullable();
            $table->string('laboratorio_nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            
            // Logo y firma
            $table->string('logo_path')->nullable();
            $table->string('firma_path')->nullable();
            
            // Footer
            $table->text('footer_texto')->nullable();
            $table->string('footer_direccion')->nullable();
            $table->string('footer_telefono')->nullable();
            $table->string('footer_email')->nullable();
            
            // Responsables
            $table->string('responsable_nombre')->nullable();
            $table->string('responsable_cargo')->nullable();
            $table->string('director_nombre')->nullable();
            $table->string('director_cargo')->nullable();
            
            // Notas
            $table->text('nota1')->nullable();
            $table->text('nota2')->nullable();
            $table->text('nota3')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};