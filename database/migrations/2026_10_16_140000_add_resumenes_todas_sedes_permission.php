<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Da de alta el permiso para trabajar con los resúmenes de todas las sedes.
 *
 * Contexto: la pantalla de Resúmenes sólo deja resumir la sede del usuario,
 * porque tanto el listado como el modal toman `Auth::user()->sede_id` y no hay
 * forma de elegir otra. Con dos sedes activas (PRINCIPAL y SEDE CHICLAYO) y
 * una sola persona administrando, las boletas pendientes de la sede ajena no
 * se podían declarar desde ningún usuario.
 *
 * Quien tenga este permiso ve un combo de sedes en el modal y en el listado.
 * Quien no lo tenga se queda exactamente como hasta ahora: su propia sede,
 * fija, y un 403 si la petición trae un sede_id ajeno.
 *
 * Se comprueba con el Gate normal 'haveaccess': un rol con full-access = 'SI'
 * lo hereda, que es lo deseado.
 *
 * La migración sólo crea la fila. La asignación a roles se hace desde la
 * interfaz (Seguridad → Roles) y no requiere despliegue.
 *
 * Compatible con PHP 7.4 y 8.3: sin tipos de unión, sin match, sin promoción de
 * propiedades en el constructor y sin enum.
 */
class AddResumenesTodasSedesPermission extends Migration
{
    /**
     * @return array<int, array<string, string>>
     */
    private function permisos()
    {
        return array(
            array(
                'slug'        => 'venta.resumenes.todas_sedes',
                'name'        => 'Resúmenes de todas las sedes',
                'description' => 'Permite elegir la sede al crear un resumen de boletas '
                               . 'y ver en el listado los resúmenes de todas las sedes. '
                               . 'Sin él, el usuario sólo opera con la suya.',
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
