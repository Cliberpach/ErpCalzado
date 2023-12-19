<?php

namespace App\Permission\Model;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name', 'slug', 'description'
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Permission\Model\Role')->withTimestamps();
    }
}
