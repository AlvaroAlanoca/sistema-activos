<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

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