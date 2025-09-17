<?php

namespace App\Http\Controllers\Kardex\KCuenta;

use App\Exports\Kardex\Cuenta\KardexCuentaExport;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Cuenta\Cuenta;
use App\Mantenimiento\Empresa\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class KCuentaController extends Controller
{
    public function index()
    {
        $cuentas_bancarias  =   Cuenta::where('estado', 'ACTIVO')->get();
        return view('kardex.cuenta.index', compact('cuentas_bancarias'));
    }

    public function getKCuenta(Request $request)
    {
        $resultado =   $this->queryKardexCuentas($request);

        return DataTables::of($resultado->kardex)
            ->with([
                'total_ingresos'    => $resultado->total_ingresos,
                'total_egresos'     => $resultado->total_egresos,
                'saldo'             => $resultado->saldo
            ])
            ->make(true);
    }

    public function queryKardexCuentas(Request $request): object
    {
        $cuenta_bancaria_id     = $request->get('cuenta_bancaria_id');
        $fecha_inicio           = $request->get('fecha_inicio');
        $fecha_fin              = $request->get('fecha_fin');

        $kardex = collect(DB::select('CALL sp_kardex_cuentas(?, ?, ?)', [
            $cuenta_bancaria_id,
            $fecha_inicio,
            $fecha_fin
        ]))->sortByDesc('fecha_registro')->sortByDesc('id')->values();

        $total_ingresos =   0;
        $total_egresos  =   0;

        foreach ($kardex as $item) {

            $total_ingresos += $item->entrada;
            $total_egresos += $item->salida;
        }

        $resultado  =  (object)['kardex' => $kardex, 'total_ingresos' => $total_ingresos, 'total_egresos' => $total_egresos, 'saldo' => $total_ingresos - $total_egresos];

        return $resultado;
    }

    public function excel(Request $request)
    {
        ob_end_clean();
        ob_start();
        $company                =   Empresa::find(1);
        $cuenta_bancaria_id     =   $request->get('cuenta_bancaria_id');
        $cuenta_bancaria        =   Cuenta::find($cuenta_bancaria_id);

        $reporte       =   $this->queryKardexCuentas($request);

        $request->merge([
            'banco_nombre'      =>  $cuenta_bancaria->banco_nombre,
            'nro_cuenta'        =>  $cuenta_bancaria->nro_cuenta,
            'moneda'            =>  $cuenta_bancaria->moneda
        ]);


        return Excel::download(
            new KardexCuentaExport($reporte, $request, $company),
            'reporte_cuentas' . now()->format('Y-m-d') . '.xlsx'
        );
    }

     public function pdf(Request $request){

        $company                =   Empresa::find(1);
        $cuenta_bancaria_id     =   $request->get('cuenta_bancaria_id');
        $cuenta_bancaria        =   Cuenta::find($cuenta_bancaria_id);

        $reporte       =   $this->queryKardexCuentas($request);


        $request->merge([
            'banco_nombre'      =>  $cuenta_bancaria->banco_nombre,
            'nro_cuenta'        =>  $cuenta_bancaria->nro_cuenta,
            'moneda'            =>  $cuenta_bancaria->moneda
        ]);


        $pdf = Pdf::loadview('kardex.cuenta.reports.pdf', [
                'empresa'               =>  $company,
                'reporte'               =>  $reporte,
                'filters'               =>  $request

            ])->setPaper('a4', 'landscape');


        return $pdf->stream('reporte_cuentas' . Carbon::now()->format('Y_m_d_H_i_s') .'.pdf');
    }
}
