<?php

namespace App\Http\Controllers\Reportes\Compras;

use App\Exports\Reportes\Compras\DocumentoExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DocumentoController extends Controller
{
    public function index()
    {
        return view('reportes.compras.index');
    }

    public function getTable(Request $request)
    {
        $proveedor = $request->proveedor_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $consulta =  DB::table('compra_documentos')
        ->join('proveedores','proveedores.id','=','compra_documentos.proveedor_id')
        ->select(
            'compra_documentos.id',
            'compra_documentos.total as monto',
            'compra_documentos.fecha_emision as fecha',
            'proveedores.descripcion as proveedor',
            'compra_documentos.modo_compra as modo_pago',
            'compra_documentos.numero_doc as documento',
        )
        ->where('compra_documentos.estado','!=','ANULADO');

        if($proveedor)
        {
            $consulta = $consulta->where('compra_documentos.proveedor_id',$proveedor);
        }

        if($fecha_ini && $fecha_fin)
        {
            $consulta = $consulta->whereBetween('compra_documentos.fecha_emision',[$fecha_ini,$fecha_fin]);
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
        return  Excel::download(new DocumentoExport($proveedor,$fecha_ini,$fecha_fin), 'COMPRAS '.$fecha_ini.'-'.$fecha_fin.'.xlsx');
    }
}
