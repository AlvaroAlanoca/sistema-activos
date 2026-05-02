<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregamos "correlativo" a la tabla bienes
        Schema::table('bienes', function (Blueprint $table) {
            // Lo ponemos como integer (número) y nullable por si ya tienes bienes antiguos
            $table->integer('correlativo')->nullable(); 
        });

        // 2. Agregamos "codigo_rubro" a la tabla rubros
        Schema::table('rubros', function (Blueprint $table) {
            // Lo ponemos como string por si el código lleva letras (Ej: RUB-01)
            $table->string('codigo_rubro')->nullable();
        });
    }

    public function down(): void
    {
        // Esto sirve por si alguna vez necesitas revertir el cambio
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropColumn('correlativo');
        });

        Schema::table('rubros', function (Blueprint $table) {
            $table->dropColumn('codigo_rubro');
        });
    }
};