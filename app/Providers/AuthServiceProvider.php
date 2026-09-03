<?php

namespace App\Providers;

use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('haveaccess',function(User $user,$perm){
            return $user->havePermission($perm);
        });

        /**
         * Variante ESTRICTA: ignora full-access y exige que el permiso esté
         * asignado explícitamente a alguno de los roles del usuario.
         * Se usa para permisos que los administradores no deben heredar,
         * como 'almacen.producto.ver_costo'.
         */
        Gate::define('haveaccess.estricto', function (User $user, $perm) {
            return $user->haveExplicitPermission($perm);
        });
        //
    }
}
