<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Da de alta el permiso para gestionar las copias de seguridad.
 *
 * Contexto: las rutas de 'mantenimiento/copias_seguridad' sólo llevaban
 * middleware('auth') y el controlador no comprobaba nada, así que cualquier
 * usuario autenticado podía listar, generar, descargar y borrar volcados
 * completos de la base de datos.
 *
 * Se comprueba con el Gate NORMAL 'haveaccess': un rol con full-access = 'SI'
 * lo hereda, que es lo deseado (un administrador sí debe poder gestionarlas).
 *
 * La migración sólo crea la fila. La asignación a roles se hace desde la
 * interfaz (Seguridad → Roles) y no requiere despliegue.
 *
 * Compatible con PHP 7.4 y 8.3: sin tipos de unión, sin match, sin promoción de
 * propiedades en el constructor y sin enum.
 */
class AddCopiasSeguridadPermission extends Migration
{
    /**
     * @return array<int, array<string, string>>
     */
    private function permisos()
    {
        return array(
            array(
                'slug'        => 'mantenimiento.copias_seguridad.gestionar',
                'name'        => 'Gestionar copias de seguridad',
                'description' => 'Permite listar, generar, descargar y eliminar copias de '
                               . 'seguridad. La descarga entrega un volcado completo de la '
                               . 'base de datos.',
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
