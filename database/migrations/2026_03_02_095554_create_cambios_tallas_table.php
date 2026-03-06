<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCambiosTallasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cambios_tallas', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('documento_id');

            $table->foreign('documento_id')->references('id')->on('cotizacion_documento');
            $table->unsignedBigInteger('detalle_id');

            // Producto reemplazado
            $table->unsignedInteger('producto_reemplazado_id');
            $table->foreign('producto_reemplazado_id')->references('id')->on('productos');

            $table->unsignedInteger('color_reemplazado_id');
            $table->foreign('color_reemplazado_id')->references('id')->on('colores');

            $table->unsignedInteger('talla_reemplazado_id');
            $table->foreign('talla_reemplazado_id')->references('id')->on('tallas');

            $table->string('producto_reemplazado_nombre', 255);
            $table->string('color_reemplazado_nombre', 255);
            $table->string('talla_reemplazado_nombre', 255);
            $table->unsignedInteger('cantidad_detalle');

            // Producto reemplazante
            $table->unsignedInteger('producto_reemplazante_id');
            $table->foreign('producto_reemplazante_id')->references('id')->on('productos');

            $table->unsignedInteger('color_reemplazante_id');
            $table->foreign('color_reemplazante_id')->references('id')->on('colores');

            $table->unsignedInteger('talla_reemplazante_id');
            $table->foreign('talla_reemplazante_id')->references('id')->on('tallas');

            $table->string('producto_reemplazante_nombre', 255);
            $table->string('color_reemplazante_nombre', 255);
            $table->string('talla_reemplazante_nombre', 255);
            $table->unsignedInteger('cantidad_cambiada');
            $table->unsignedInteger('cantidad_sin_cambio');

            // Usuario
            $table->unsignedInteger('user_id');
            $table->string('user_nombre', 255);

            // Estado
            $table->string('estado', 50)->default('ACTIVO');

            // Sede y almacén
            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');

            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->string('almacen_nombre', 160)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cambios_tallas');
    }
}
