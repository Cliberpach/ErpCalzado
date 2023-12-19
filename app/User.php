<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Permission\Traits\UserTrait;
class User extends Authenticatable
{
    use Notifiable, UserTrait;

    protected $fillable = [
        'usuario', 'email', 'password','contra','nombre_imagen','ruta_imagen'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->hasOne(UserPersona::class,'user_id');
    }
}
