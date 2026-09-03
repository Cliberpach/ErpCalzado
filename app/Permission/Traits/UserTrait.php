<?php

namespace App\Permission\Traits;

use App\UserPersona;

trait UserTrait
{
public function colaborador()
    {
        return $this->hasOne(UserPersona::class,'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Permission\Model\Role')->withTimestamps();
    }

    /**
     * AVISO: un rol con full-access = 'SI' concede CUALQUIER permiso, sin mirar
     * siquiera el slug recibido. Por eso este método NO sirve para permisos que
     * deban quedar fuera del alcance de los administradores.
     *
     * Si necesita un permiso que NO se herede por full-access —hoy es el caso de
     * 'almacen.producto.ver_costo'— use haveExplicitPermission() y el Gate
     * 'haveaccess.estricto', nunca este método ni el Gate 'haveaccess'.
     */
    public function havePermission($permission)
    {
        foreach($this->roles as $role)
        {
            if($role['full-access']=='SI')
            {
                return true;
            }

            foreach($role->permissions as $perm)
            {
                if($perm->slug==$permission)
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Comprueba un permiso SIN aplicar la herencia de full-access: sólo concede
     * si alguno de los roles del usuario tiene ese permiso asignado de forma
     * explícita en la tabla permission_role.
     *
     * Un rol con full-access = 'SI' nunca puede satisfacer esta comprobación:
     * RoleController@store y @update ejecutan permissions()->sync([]) sobre esos
     * roles, de modo que jamás tienen filas en permission_role.
     *
     * Para conceder uno de estos permisos, asígnelo desde la pantalla de Roles a
     * un rol con full-access = 'NO'.
     */
    public function haveExplicitPermission($permission): bool
    {
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $perm) {
                if ($perm->slug === $permission) {
                    return true;
                }
            }
        }

        return false;
    }
}
