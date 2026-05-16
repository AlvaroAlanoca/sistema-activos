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
    Schema::create('servicio_contrato', function (Blueprint $table) {
        $table->id('idservicio_contrato');
        
        // Relación con la tabla users (el estándar de Laravel es 'id')
        $table->foreignId('id_user')->constrained('users');
        
        // Relación con la tabla servicios (apuntamos a 'idservicios')
        $table->foreignId('id_servicio')->references('idservicios')->on('servicios');
        
        $table->date('fecha_inicio');
        $table->date('fecha_fin');
        $table->string('estado')->default('pendiente'); // 'pendiente' o 'cumplido'
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_contrato');
    }
};
