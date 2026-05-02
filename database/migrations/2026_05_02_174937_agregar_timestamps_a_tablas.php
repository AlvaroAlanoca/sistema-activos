<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregamos las columnas a la tabla bienes
        Schema::table('bienes', function (Blueprint $table) {
            $table->timestamps(); // Esto crea created_at y updated_at
        });

        // Agregamos a la tabla responsables
        Schema::table('responsables', function (Blueprint $table) {
            $table->timestamps();
        });

        // Agregamos a la tabla actas
        Schema::table('actas', function (Blueprint $table) {
            $table->timestamps();
        });

        // Agregamos a la tabla acta_items
        Schema::table('acta_items', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('cargos', function (Blueprint $table) {
        $table->timestamps();
        });
        Schema::table('oficinas', function (Blueprint $table) {
        $table->timestamps();
        });
        Schema::table('oficinas_cargos', function (Blueprint $table) {
        $table->timestamps();
        });
        Schema::table('rubros', function (Blueprint $table) {
        $table->timestamps();
        });                        
        Schema::table('tipo_bien', function (Blueprint $table) {
        $table->timestamps();
        });    
    }

    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('responsables', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('actas', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('acta_items', function (Blueprint $table) {
            $table->dropTimestamps();
        });
            Schema::table('cargos', function (Blueprint $table) {
            $table->dropTimestamps();
        });
                Schema::table('oficinas', function (Blueprint $table) {
            $table->dropTimestamps();
        });
                Schema::table('oficinas_cargos', function (Blueprint $table) {
            $table->dropTimestamps();
        });
                Schema::table('rubros', function (Blueprint $table) {
            $table->dropTimestamps();
        });    
                Schema::table('tipo_bien', function (Blueprint $table) {
            $table->dropTimestamps();
        });            
    
    }
};