<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Control de "mostrar/ocultar en la web", mismo patrón que
 * productos.mostrar_en_web (2020_12_17_044556). Antes ecommerceMerris había
 * agregado un campo propio (visible_en_tienda) para categorías porque acá no
 * existía nada equivalente — se revirtió: un solo interruptor por entidad,
 * y ErpCalzado ya es el dueño de esta decisión para productos, así que se
 * mantiene consistente acá también. Ver docs/modules/productos.md en
 * ecommerceMerris.
 */
class AddMostrarEnWebToCategoriasTable extends Migration
{
    public function up()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->boolean('mostrar_en_web')->default(false)->after('tipo');
        });
    }

    public function down()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('mostrar_en_web');
        });
    }
}
