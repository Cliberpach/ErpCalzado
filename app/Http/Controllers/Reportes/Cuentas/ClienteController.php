<?php

namespace App\Http\Controllers\Reportes\Cuentas;

use App\Exports\Reportes\Cuentas\ClienteExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ClienteController extends Controller
{
    public function index()
    {
        return view('reportes.cuentas.cliente');
    }

    public function getTable(Request $request)
    {
        $cliente = $request->cliente_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $consulta = DB::table('cuenta_cliente')
        ->join('cotizacion_documento','cotizacion_documento.id','=','cuenta_cliente.cotizacion_documento_id')
        ->join('condicions','condicions.id','=','cotizacion_documento.condicion_id')
        ->join('clientes','clientes.id','=','cotizacion_documento.cliente_id')
        ->select(
            'cuenta_cliente.id',
            'cuenta_cliente.saldo',
            'cotizacion_documento.total as monto',
            'cotizacion_documento.fecha_documento as fecha',
            'clientes.nombre as cliente',
            'condicions.descripcion as modo_pago',
            'cotizacion_documento.serie',
            'cotizacion_documento.correlativo',
            'cotizacion_documento.moneda',
            DB::raw('(cotizacion_documento.total - cuenta_cliente.saldo) as acta'),
            DB::raw('ifnull((select fecha
            from detalle_cuenta_cliente dcc
            where dcc.cuenta_cliente_id = cuenta_cliente.id
           order by id desc
           limit 1),"-") as fecha_ultima'),
            DB::raw('ifnull((select descripcion
            from tipos_pago tp
            where tp.id = cotizacion_documento.tipo_pago_id
           order by id desc
           limit 1),"-") as tipo_pago'),
        );

        if($cliente)
        {
            $consulta = $consulta->where('cotizacion_documento.cliente_id',$cliente);
        }

        if($fecha_ini && $fecha_fin)
        {
            $consulta = $consulta->whereBetween('cuenta_cliente.fecha_doc',[$fecha_ini,$fecha_fin]);
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
        return  Excel::download(new ClienteExport($cliente,$fecha_ini,$fecha_fin), 'CUENTAS CLIENTES '.$fecha_ini.'-'.$fecha_fin.'.xlsx');
    }
}
