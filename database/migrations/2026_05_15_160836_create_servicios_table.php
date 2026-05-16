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
    Schema::create('servicios', function (Blueprint $table) {
        // Usamos id() pero le pasamos el nombre que quieres
        $table->id('idservicios'); 
        $table->string('cuce')->unique(); // El código estatal suele ser único
        $table->text('descripcion');
        $table->string('empresa');
        $table->timestamps(); // Esto crea created_at y updated_at automáticamente
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
