<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion_detalles', function (Blueprint $table) {
            $table->Increments('id');
            
            $table->unsignedInteger('cotizacion_id');
            $table->foreign('cotizacion_id')->references('id')->on('cotizaciones');
            
            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colores');

            $table->unsignedInteger('talla_id');
            $table->foreign('talla_id')->references('id')->on('tallas');

            $table->string('almacen_nombre',160);
            $table->string('producto_nombre',160);
            $table->string('color_nombre',160);
            $table->string('talla_nombre',160);
            
            $table->unsignedDecimal('cantidad');
            $table->unsignedDecimal('precio_unitario');
            $table->unsignedDecimal('importe');

            $table->unsignedDecimal('porcentaje_descuento', 15, 2)->nullable();
            $table->unsignedDecimal('precio_unitario_nuevo', 15, 2);
            $table->unsignedDecimal('importe_nuevo', 15, 2);
            $table->unsignedDecimal('monto_descuento', 15, 2)->nullable();

         
            $table->enum('estado',['ACTIVO','ANULADO'])->default('ACTIVO');
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
        Schema::dropIfExists('cotizacion_detalles');
    }
}
