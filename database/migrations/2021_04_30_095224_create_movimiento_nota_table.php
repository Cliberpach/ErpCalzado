<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoNotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_nota', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('orden_produccion_detalle_id')->unsigned()->nullable();
            $table->unsignedInteger('almacen_inicio_id')->unsigned()->nullable();
            $table->unsignedInteger('almacen_final_id')->unsigned()->nullable();
            $table->unsignedDecimal('cantidad', 15,2);
            $table->unsignedInteger('nota_id')->unsigned(); 
            $table->mediumText('observacion');
            $table->unsignedInteger('usuario_id')->unsigned(); 
            // $table->unsignedInteger('lote_id')->unsigned(); 
            $table->enum('movimiento',['SALIDA','INGRESO']);
            $table->unsignedInteger('producto_id')->unsigned();
            $table->unsignedInteger('color_id')->unsigned();
            $table->unsignedInteger('talla_id')->unsigned();
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
        Schema::dropIfExists('movimiento_nota_ingreso');
    }
}
