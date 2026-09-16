<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Da de alta los permisos para emitir guías de remisión y retenciones a SUNAT.
 *
 * Contexto: las rutas 'consultas/ventas/alertas/sunat_guias/{id}' y
 * '.../sunat_retenciones/{id}' eran GET sin ninguna comprobación de permiso, de modo que
 * cualquier usuario autenticado podía disparar una emisión externa escribiendo la URL.
 * Este cambio las pasa a POST y exige uno de estos permisos.
 *
 * Se comprueban con el Gate NORMAL 'haveaccess': un rol con full-access = 'SI' los hereda,
 * que es lo deseado aquí (un administrador sí debe poder emitir). El Gate estricto
 * 'haveaccess.estricto' se reserva para lo que los administradores NO deben ver.
 *
 * La migración sólo crea las filas. La asignación a roles se hace desde la interfaz
 * (Seguridad → Roles) y no requiere despliegue.
 *
 * Compatible con PHP 7.4 y 8.3: sin tipos de unión, sin match, sin promoción de
 * propiedades en el constructor y sin enum.
 */
class AddEnviarSunatPermissions extends Migration
{
    /**
     * @return array<int, array<string, string>>
     */
    private function permisos()
    {
        return array(
            array(
                'slug'        => 'ventas.guia.enviar_sunat',
                'name'        => 'Enviar guía de remisión a SUNAT',
                'description' => 'Permite emitir la guía de remisión electrónica. '
                               . 'Acción con efecto externo irreversible.',
            ),
            array(
                'slug'        => 'ventas.retencion.enviar_sunat',
                'name'        => 'Enviar retención a SUNAT',
                'description' => 'Permite emitir el comprobante de retención electrónico. '
                               . 'Acción con efecto externo irreversible.',
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
