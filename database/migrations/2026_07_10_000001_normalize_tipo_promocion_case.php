<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige promociones cuyo tipo_promocion quedó en MAYÚSCULA por el bug de
 * PromocionUpdateRequest (validaba/guardaba en mayúscula mientras store()
 * y todo el frontend -ErpCalzado y ecommerceMerris- comparan en minúscula
 * exacta). Sin esto, cualquier promoción editada al menos una vez muestra
 * mal el descuento/badge en la tienda. Ver PromocionUpdateRequest.php.
 */
class NormalizeTipoPromocionCase extends Migration
{
    public function up()
    {
        DB::table('promociones')->update([
            'tipo_promocion' => DB::raw('LOWER(tipo_promocion)'),
        ]);
    }

    public function down()
    {
        // No reversible de forma segura: no se puede distinguir qué filas
        // estaban originalmente en mayúscula antes de esta corrección.
    }
}
