<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Agregar atributos a la tabla 'bienes'
        Schema::table('bienes', function (Blueprint $table) {
            // Usamos decimal con 10 dígitos en total y 2 decimales para la moneda (Bs.)
            $table->decimal('depreciacion_acumulada', 10, 2)->default(0)->nullable()->after('costo');
            $table->decimal('valor_neto', 10, 2)->default(0)->nullable()->after('depreciacion_acumulada');
        });

        // 2. Quitar atributo a la tabla 'tipo_bien'
        Schema::table('tipo_bien', function (Blueprint $table) {
            $table->dropColumn('vida_util');
        });

        // 3. Agregar atributo a la tabla 'rubros'
        Schema::table('rubros', function (Blueprint $table) {
            $table->integer('vida_util')->nullable()->after('descripcion')->comment('Vida útil en años');
        });
    }

    public function down()
    {
        // Esto es por si necesitas revertir los cambios en el futuro
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropColumn(['depreciacion_acumulada', 'valor_neto']);
        });

        Schema::table('tipo_bien', function (Blueprint $table) {
            $table->integer('vida_util')->nullable();
        });

        Schema::table('rubros', function (Blueprint $table) {
            $table->dropColumn('vida_util');
        });
    }
};