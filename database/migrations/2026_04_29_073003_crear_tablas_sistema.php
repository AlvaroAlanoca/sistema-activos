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
    // 1. Tablas independientes
    Schema::create('rubros', function ($table) { 
        $table->id('idrubros'); 
        $table->string('clasificador_presupuestario'); 
        $table->string('descripcion'); 
        });
    Schema::create('oficinas', function ($table){ 
        $table->id('idoficinas'); 
        $table->string('descripcion');
        });
    Schema::create('cargos', function ($table) {
        $table->id('idcargos'); 
        $table->string('descripcion'); 
        });

    // 2. Conectores
    Schema::create('tipo_bien', function ($table) { 
        $table->id('idtipo_bien'); 
        $table->string('descripcion'); 
        $table->foreignId('id_rubro')->references('idrubros')->on('rubros'); 
        });
    Schema::create('oficinas_cargos', function ($table) { 
        $table->id('idoficinas_cargos'); 
        $table->foreignId('id_cargos')->references('idcargos')->on('cargos'); 
        $table->foreignId('id_oficinas')->references('idoficinas')->on('oficinas'); 
        });

    // 3. Maestros
    Schema::create('bienes', function ($table) { 
        $table->id('idbienes'); 
        $table->string('estado'); 
        $table->string('codigo'); 
        $table->string('descripcion'); 
        $table->foreignId('id_tipo_bien')->references('idtipo_bien')->on('tipo_bien'); 
        });
    Schema::create('responsables', function ($table) { 
        $table->id('idresponsables'); 
        $table->string('nombre_apellido'); 
        $table->foreignId('id_oficinas_cargos')->references('idoficinas_cargos')->on('oficinas_cargos'); 
        });

    // 4. Transacciones
    Schema::create('actas', function ($table) { 
        $table->id('idacta'); 
        $table->string('tipo'); 
        $table->string('numero_acta');
        $table->foreignId('id_responsables')->references('idresponsables')->on('responsables'); 
        });
    Schema::create('acta_items', function ($table) { 
        $table->id('idacta_items'); 

        $table->string('estado'); 
        $table->foreignId('id_bienes')->references('idbienes')->on('bienes'); 
        $table->foreignId('id_acta')->references('idacta')->on('actas')->onDelete('cascade'); 
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
