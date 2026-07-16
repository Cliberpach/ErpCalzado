<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasWebDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('reservas_web_detalle', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reserva_web_id')->constrained('reservas_web')->cascadeOnDelete();

            $table->unsignedInteger('producto_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('talla_id');
            $table->unsignedInteger('cantidad');

            // Snapshot del precio al momento de la reserva — puede cambiar
            // entre reserva y confirmación, no se recalcula.
            $table->decimal('precio_venta_1', 10, 2);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas_web_detalle');
    }
}
