<?php

namespace App\Http\Controllers\Reportes\Ventas;

use App\Exports\Reportes\Ventas\DocumentoExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DocumentoController extends Controller
{
    public function index()
    {
        return view('reportes.ventas.index');
    }

    public function getTable(Request $request)
    {
        $cliente = $request->cliente_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $consulta = DB::table('cotizacion_documento')
            ->join('clientes','clientes.id','=','cotizacion_documento.cliente_id')
            ->join('condicions','condicions.id','=','cotizacion_documento.condicion_id')
            ->leftjoin('tipos_pago','tipos_pago.id','=','cotizacion_documento.tipo_pago_id')
            ->select(
                'cotizacion_documento.id',
                'cotizacion_documento.total as monto',
                'cotizacion_documento.fecha_documento as fecha',
                'clientes.nombre as cliente',
                'condicions.descripcion as modo_pago',
                'tipos_pago.descripcion as tipo_pago',
                'cotizacion_documento.serie','cotizacion_documento.correlativo'
        );
        if($cliente)
        {
            $consulta->where('cotizacion_documento.cliente_id',$cliente);
        }

        if($fecha_ini && $fecha_ini)
        {
            $consulta->whereBetween('cotizacion_documento.fecha_documento',[$fecha_ini,$fecha_fin]);
        }


        return datatables()->query(
            $consulta
        )->toJson();
    }

    public function getExcel(Request $request)
    {
        ob_end_clean();
        ob_start();
        $cliente = $request->cliente_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        return  Excel::download(new DocumentoExport($cliente,$fecha_ini,$fecha_fin), 'VENTAS '.$fecha_ini.'-'.$fecha_fin.'.xlsx');
    }
}
