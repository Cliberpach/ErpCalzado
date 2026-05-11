<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocionesProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promociones_productos', function (Blueprint $table) {

            $table->id()->comment('Activar reactivar, no duplicar');

            $table->unsignedBigInteger('promocion_id');
            $table->unsignedInteger('producto_id');

            $table->boolean('estado')->default(1);

            $table->timestamps();

            $table->foreign('promocion_id')
                ->references('id')
                ->on('promociones');

            $table->foreign('producto_id')
                ->references('id')
                ->on('productos');

            $table->unique(['promocion_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promociones_productos');
    }
}
