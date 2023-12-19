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
}
