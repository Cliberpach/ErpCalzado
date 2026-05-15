<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductoColorImagenesTable extends Migration
{
    public function up()
    {
        Schema::create('producto_color_imagenes', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('producto_id');
            $table->unsignedBigInteger('color_id');

            $table->string('img_route', 500)->comment('Ruta en storage, ej: public/productos/imagenes/archivo.jpg');
            $table->string('img_name', 255)->comment('Nombre original del archivo');

            $table->tinyInteger('orden')->default(1)->comment('Posición 1..5');

            $table->timestamps();

            $table->foreign('producto_id')
                ->references('id')
                ->on('productos')
                ->onDelete('cascade');

            $table->foreign('color_id')
                ->references('id')
                ->on('colores')
                ->onDelete('cascade');

            $table->unique(['producto_id', 'color_id', 'orden'], 'uq_pci_producto_color_orden');
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto_color_imagenes');
    }
}
