<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las modificaciones en la estructura de la base de datos.
     */
    public function up(): void
    {
        // 1. Alteración de la tabla bienes
        Schema::table('bienes', function (Blueprint $table) {
            $table->decimal('costo', 10, 2)
                  ->nullable()
                  ->after('descripcion')
                  ->comment('Costo de adquisición del activo');
        });

        // 2. Alteración de la tabla tipo_bien (Ajustar nombre si es 'tipo_bienes')
        Schema::table('tipo_bien', function (Blueprint $table) {
            $table->unsignedInteger('vida_util')
                  ->nullable()
                  ->after('descripcion')
                  ->comment('Vida útil estimada expresada en años');
        });
    }

    /**
     * Revierte las modificaciones en caso de un rollback.
     */
    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropColumn('costo');
        });

        Schema::table('tipo_bien', function (Blueprint $table) {
            $table->dropColumn('vida_util');
        });
    }
};