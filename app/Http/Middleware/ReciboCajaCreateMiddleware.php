<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Closure;

class ReciboCajaCreateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $caja_aperturada    =   DB::select('select mc.caja_id,c.nombre from detalles_movimiento_caja as dmc
                                    inner join movimiento_caja as mc  on dmc.movimiento_id=mc.id
                                    inner join caja as c on c.id=mc.caja_id
                                    where estado_movimiento = "APERTURA" and dmc.usuario_id=?',[Auth::user()->id]);

        if (count($caja_aperturada) === 0) {
            Session::flash('error_validacion_caja', 'USTED NO SE ENCUENTRA REGISTRADO EN NINGUNA CAJA APERTURADA ACTUALMENTE');
            return redirect()->route('recibos_caja.index');
        }        

        return $next($request);
    }
}
