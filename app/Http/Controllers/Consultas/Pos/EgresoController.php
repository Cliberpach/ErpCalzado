<?php

namespace App\Http\Controllers\Consultas\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EgresoController extends Controller
{
    public function index()
    {
        return view('consultas.pos.egreso');
    }

    public function getTable(Request $request)
    {

        $caja = $request->caja_id;
        $cuenta = $request->cuenta_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $consulta =  DB::table('egreso')
            //->join('tabladetalles', 'egreso.tipodocumento_id', '=', 'tabladetalles.id')
            ->join('tabladetalles', 'egreso.cuenta_id', '=', 'tabladetalles.id')
            ->select(
                'egreso.id',
                'egreso.descripcion',
                'egreso.monto',
                'egreso.estado',
                'egreso.documento',
                'egreso.usuario',
                'egreso.created_at',
                'tabladetalles.descripcion as cuenta',
                DB::raw('(select descripcion from tabladetalles where id = egreso.tipodocumento_id) as tipoDocumento')
            )->where('egreso.estado', 'ACTIVO');

        if ($cuenta) {
            $consulta = $consulta->where('tabladetalles.id', $cuenta);
        }
        if ($fecha_ini && $fecha_fin) {
            $consulta = $consulta->whereBetween(DB::raw('DATE_FORMAT(egreso.created_at, "%Y-%m-%d")'), [$fecha_ini, $fecha_fin]);
        }
        return datatables()->query(
            $consulta
        )->toJson();
    }
}
