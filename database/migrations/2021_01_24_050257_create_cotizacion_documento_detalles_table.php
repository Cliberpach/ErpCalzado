<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionDocumentoDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion_documento_detalles', function (Blueprint $table) {
            $table->Increments('id');

            $table->unsignedInteger('documento_id');
            $table->foreign('documento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');

            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedInteger('producto_id');
            $table->unsignedInteger('color_id');
            $table->unsignedInteger('talla_id');
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->foreign('color_id')->references('id')->on('colores');
            $table->foreign('talla_id')->references('id')->on('tallas');

            $table->string('codigo_producto')->nullable();
            $table->string('unidad')->default('NIU'); //NIU-BIENES
            $table->string('almacen_nombre');
            $table->string('nombre_producto');
            $table->string('nombre_color');
            $table->string('nombre_talla');
            $table->string('nombre_modelo');

            $table->unsignedDecimal('cantidad', 15, 6);
            $table->unsignedInteger('cantidad_sin_cambio')->nullable();
            $table->unsignedInteger('cantidad_cambiada')->nullable();

            $table->unsignedDecimal('precio_unitario', 15, 6);
            $table->unsignedDecimal('importe', 15, 6);

            $table->unsignedDecimal('porcentaje_descuento', 15, 6)->nullable();
            $table->unsignedDecimal('precio_unitario_nuevo', 15, 6);
            $table->unsignedDecimal('importe_nuevo', 15, 6);
            $table->unsignedDecimal('monto_descuento', 15, 6)->nullable();

            $table->unsignedInteger('anticipo_id')->nullable();
            $table->string('anticipo_serie', 100)->nullable();
            $table->enum('tipo', ['PRODUCTO', 'SERVICIO'])->default('PRODUCTO');
            $table->string('estado_cambio_talla', 100)->nullable();
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->enum('eliminado', ['0', '1'])->default('0');
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
        Schema::dropIfExists('cotizacion_documento_detalles');
    }
}
