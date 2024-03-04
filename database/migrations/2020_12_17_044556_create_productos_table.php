<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->Increments('id');

            $table->unsignedInteger('categoria_id');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
            
            $table->unsignedInteger('marca_id');
            $table->foreign('marca_id')->references('id')->on('marcas')->onDelete('cascade');

            $table->unsignedInteger('modelo_id');
            $table->foreign('modelo_id')->references('id')->on('modelos')->onDelete('cascade');

            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes')->onDelete('cascade');
            
            $table->string('codigo', 50)->nullable(); //opcional
            $table->string('nombre');
            $table->mediumText('descripcion')->nullable();
            $table->string('medida');
            $table->string('codigo_barra')->nullable();
            // $table->string('moneda');
            //$table->unsignedDecimal('stock', 15, 2)->default(0);
            $table->unsignedDecimal('stock_minimo', 15, 2)->default(1);
            $table->unsignedDecimal('precio_compra', 15, 2)->nullable();
            //$table->unsignedDecimal('precio_venta_minimo', 15, 2)->nullable();
            //$table->unsignedDecimal('precio_venta_maximo', 15, 2)->nullable();
            //$table->unsignedDecimal('peso_producto', 15, 2)->default(0);
            $table->unsignedDecimal('precio_venta_1', 15, 2)->nullable();
            $table->unsignedDecimal('precio_venta_2', 15, 2)->nullable();
            $table->unsignedDecimal('precio_venta_3', 15, 2)->nullable();

            $table->boolean('igv')->default(TRUE);
            $table->string('facturacion')->default('SI'); //campo agregado


            //$table->unsignedDecimal('porcentaje_normal', 15, 2)->default(0);
            //$table->unsignedDecimal('porcentaje_distribuidor', 15, 2)->default(0);
            $table->unsignedDecimal('costo', 15, 2)->default(0);
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
        Schema::dropIfExists('productos');
    }
}
