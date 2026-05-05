<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones (Lo que pasa al aplicar).
     */
    public function up(): void
    {
        Schema::table('responsables', function (Blueprint $table) {
            // 1. Eliminamos la columna vieja
            $table->dropColumn('gerencia');
            
            // 2. Agregamos la columna nueva (después del CI para mantener el orden)
            $table->string('numero_item', 50)->nullable()->after('ci');
        });
    }

    /**
     * Revierte las migraciones (Lo que pasa si hacemos rollback).
     */
    public function down(): void
    {
        Schema::table('responsables', function (Blueprint $table) {
            // Si nos arrepentimos, borramos el ítem y regresamos la gerencia
            $table->dropColumn('numero_item');
            $table->string('gerencia')->nullable()->after('ci');
        });
    }
};