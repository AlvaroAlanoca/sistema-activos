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
    Schema::table('servicios', function (Blueprint $table) {
        // Creamos la columna justo después del CUCE
        $table->string('tipo')->default('SICOES')->after('cuce'); 
    });
}

public function down(): void
{
    Schema::table('servicios', function (Blueprint $table) {
        $table->dropColumn('tipo');
    });
}
};
