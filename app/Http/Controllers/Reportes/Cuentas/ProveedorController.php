<?php

namespace App\Http\Controllers\Reportes\Cuentas;

use App\Exports\Reportes\Cuentas\ProveedorExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProveedorController extends Controller
{
    public function index()
    {
        return view('reportes.cuentas.proveedor');
    }

    public function getTable(Request $request)
    {
        $proveedor = $request->proveedor_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $consulta = DB::table('cuenta_proveedor')
        ->join('compra_documentos','compra_documentos.id','=','cuenta_proveedor.compra_documento_id')
        ->join('proveedores','proveedores.id','=','compra_documentos.proveedor_id')
        ->select(
            'cuenta_proveedor.id',
            'cuenta_proveedor.saldo',
            'compra_documentos.total as monto',
            'compra_documentos.fecha_emision as fecha',
            'proveedores.descripcion as proveedor',
            'compra_documentos.modo_compra as modo_pago',
            'compra_documentos.numero_doc as documento',
            'compra_documentos.moneda',
            DB::raw('(compra_documentos.total - cuenta_proveedor.saldo) as acta'),
            DB::raw('ifnull((select fecha
            from detalle_cuenta_proveedor dcp
            where dcp.cuenta_proveedor_id = cuenta_proveedor.id
           order by id desc
           limit 1),"-") as fecha_ultima'),
        );

        if($proveedor)
        {
            $consulta = $consulta->where('compra_documentos.proveedor_id',$proveedor);
        }

        if($fecha_ini && $fecha_fin)
        {
            $consulta = $consulta->whereBetween('cuenta_proveedor.fecha_doc',[$fecha_ini,$fecha_fin]);
        }

        return datatables()->query(
            $consulta
        )->toJson();
    }

    public function getExcel(Request $request)
    {
        ob_end_clean();
        ob_start();
        $proveedor = $request->proveedor_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        return  Excel::download(new ProveedorExport($proveedor,$fecha_ini,$fecha_fin), 'CUENTAS PROVEEDOR '.$fecha_ini.'-'.$fecha_fin.'.xlsx');
    }
}
