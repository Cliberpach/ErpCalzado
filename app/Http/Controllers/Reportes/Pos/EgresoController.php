<?php

namespace App\Http\Controllers\Reportes\Pos;

use App\Exports\Reportes\Pos\EgresoExport;
use App\Http\Controllers\Controller;
use App\Pos\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EgresoController extends Controller
{
    public function index()
    {
        $cajas = Caja::where('estado', 'ACTIVO')->get();
        return view('reportes.pos.egreso', compact('cajas'));
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
            'egreso.usuario',
            'egreso.documento',
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

        //nuevo atributo de tipo date llamado fecha en movimiento_caja
        // update moviemiento_caja fecha = DATE_FORMAT(fecha_apertura, "%Y-%m-d")
    }

    public function getExcel(Request $request)
    {
        ob_end_clean();
        ob_start();
        $cuenta = $request->cuenta_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        return  Excel::download(new EgresoExport($cuenta, $fecha_ini, $fecha_fin), 'EGRESO ' . $fecha_ini . '-' . $fecha_fin . '.xlsx');
    }
}
