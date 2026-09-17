<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Da de alta el permiso para anular una venta desde Consultas → Alertas.
 *
 * Contexto: la ruta 'consultas/ventas/alertas/anular-venta/{id}' era GET, sin
 * comprobación de permiso y sin CSRF, así que cualquier usuario autenticado
 * podía intentar anular un comprobante escribiendo la URL.
 *
 * Se comprueba con el Gate NORMAL 'haveaccess': un rol con full-access = 'SI'
 * lo hereda, que es lo deseado (un administrador sí debe poder anular).
 *
 * La migración sólo crea la fila. La asignación a roles se hace desde la
 * interfaz (Seguridad → Roles) y no requiere despliegue.
 *
 * Compatible con PHP 7.4 y 8.3: sin tipos de unión, sin match, sin promoción de
 * propiedades en el constructor y sin enum.
 */
class AddAnularAlertaPermission extends Migration
{
    /**
     * @return array<int, array<string, string>>
     */
    private function permisos()
    {
        return array(
            array(
                'slug'        => 'ventas.documento.anular_alerta',
                'name'        => 'Anular venta desde Alertas',
                'description' => 'Permite marcar como ANULADO un comprobante desde '
                               . 'Consultas → Alertas. No aplica a comprobantes ya '
                               . 'enviados a SUNAT.',
            ),
        );
    }

    public function up()
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        foreach ($this->permisos() as $permiso) {
            $existe = DB::table('permissions')->where('slug', $permiso['slug'])->exists();

            if ($existe) {
                continue; // idempotente: no duplica si ya se creó a mano
            }

            // Sin 'id': lo asigna el AUTO_INCREMENT, para no chocar con los ids
            // que ya existan en cada entorno.
            DB::table('permissions')->insert(array(
                'name'        => $permiso['name'],
                'slug'        => $permiso['slug'],
                'description' => $permiso['description'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ));
        }
    }

    public function down()
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        foreach ($this->permisos() as $permiso) {
            $fila = DB::table('permissions')->where('slug', $permiso['slug'])->first();

            if (!$fila) {
                continue;
            }

            // Primero las asignaciones a roles, para no dejar filas huérfanas.
            DB::table('permission_role')->where('permission_id', $fila->id)->delete();
            DB::table('permissions')->where('id', $fila->id)->delete();
        }
    }
}
