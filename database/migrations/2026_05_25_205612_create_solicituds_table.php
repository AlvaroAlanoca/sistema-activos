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
    Schema::create('solicitudes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bien_id')->constrained('bienes', 'idbienes'); // Enlazado a tu llave primaria
        $table->foreignId('responsable_id')->constrained('responsables', 'idresponsables');
        $table->string('estado')->default('PENDIENTE'); // PENDIENTE, APROBADA, RECHAZADA
        $table->text('motivo');
        $table->text('respuesta_admin')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
