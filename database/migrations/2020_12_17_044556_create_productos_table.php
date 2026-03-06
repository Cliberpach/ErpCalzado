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

            $table->string('codigo', 50)->nullable(); //opcional
            $table->string('nombre');
            $table->mediumText('descripcion')->nullable();
            $table->string('medida');
            $table->string('codigo_barra')->nullable();
            $table->unsignedDecimal('stock_minimo', 15, 2)->default(1);
            $table->unsignedDecimal('precio_compra', 15, 2)->nullable();
            $table->unsignedDecimal('precio_venta_1', 15, 2)->nullable();
            $table->unsignedDecimal('precio_venta_2', 15, 2)->nullable();
            $table->unsignedDecimal('precio_venta_3', 15, 2)->nullable();

            $table->boolean('igv')->default(TRUE);
            $table->string('facturacion')->default('SI'); //campo agregado


            $table->unsignedDecimal('costo', 15, 2)->default(0);
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->enum('tipo', ['FICTICIO', 'PRODUCTO'])->default('PRODUCTO');

            $table->longText('img1_ruta')->nullable();
            $table->longText('img1_nombre')->nullable();
            $table->longText('img2_ruta')->nullable();
            $table->longText('img2_nombre')->nullable();
            $table->longText('img3_ruta')->nullable();
            $table->longText('img3_nombre')->nullable();
            $table->longText('img4_ruta')->nullable();
            $table->longText('img4_nombre')->nullable();
            $table->longText('img5_ruta')->nullable();
            $table->longText('img5_nombre')->nullable();

            $table->boolean('mostrar_en_web')->default(false);
            
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
