<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Da de alta el permiso 'almacen.producto.ver_costo'.
 *
 * Este permiso NO se comprueba con el Gate 'haveaccess' sino con
 * 'haveaccess.estricto' (ver AuthServiceProvider y UserTrait::haveExplicitPermission).
 * La diferencia es deliberada: 'haveaccess' concede cualquier permiso a los roles
 * con full-access = 'SI', y el cliente pidió que el costo NO lo vean todos los
 * administradores.
 *
 * La migración sólo crea la fila del permiso. La asignación a roles y usuarios se
 * hace desde la interfaz (Seguridad → Roles / Usuarios) y no requiere despliegue:
 *
 *   1. Crear un rol dedicado, p. ej. COSTOS, con full-access = 'NO'.
 *   2. Marcarle el permiso "Ver costo del producto".
 *   3. Asignar ese rol a los usuarios que deban ver el costo.
 *
 * Nota: no se asigna a ningún rol aquí a propósito. Un rol con full-access = 'SI'
 * no puede recibirlo: RoleController@store/@update ejecuta permissions()->sync([])
 * sobre esos roles.
 */
class AddVerCostoProductoPermission extends Migration
{
    private const SLUG = 'almacen.producto.ver_costo';

    public function up()
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $existe = DB::table('permissions')->where('slug', self::SLUG)->exists();

        if ($existe) {
            return; // idempotente: no duplica si ya se creó a mano
        }

        DB::table('permissions')->insert([
            'name'        => 'Ver costo del producto',
            'slug'        => self::SLUG,
            'description' => 'El usuario puede ver el costo del producto. NO se hereda por full-access: '
                           . 'debe asignarse explícitamente a un rol con full-access = NO.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down()
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $permiso = DB::table('permissions')->where('slug', self::SLUG)->first();

        if (!$permiso) {
            return;
        }

        // Primero las asignaciones a roles, para no dejar filas huérfanas.
        DB::table('permission_role')->where('permission_id', $permiso->id)->delete();
        DB::table('permissions')->where('id', $permiso->id)->delete();
    }
}
