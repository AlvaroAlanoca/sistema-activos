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
    Schema::table('responsables', function (Blueprint $table) {
        $table->string('ci')->unique()->nullable()->after('nombre_apellido');
        $table->string('gerencia')->nullable()->after('ci');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
