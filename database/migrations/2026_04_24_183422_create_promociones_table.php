<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promociones', function (Blueprint $table) {

            $table->id();

            $table->string('nombre');

            $table->text('descripcion')
                ->nullable();

            $table->enum('tipo_promocion', [

                'descuento_fijo',

                'descuento_porcentaje',

                'precio_total',

            ])->default('precio_total');

            $table->decimal('valor', 10, 2);

            $table->integer('cantidad_minima')
                ->default(1);

            $table->date('fecha_inicio')
                ->nullable();

            $table->date('fecha_fin')
                ->nullable();

            $table->enum('estado', [

                'ACTIVO',

                'ANULADO',

            ])->default('ACTIVO');

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
        Schema::dropIfExists('promociones');
    }
}
