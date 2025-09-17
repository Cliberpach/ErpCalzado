<?php

namespace App\Http\Controllers\Kardex\KStock;

use App\Almacenes\Modelo;
use App\Almacenes\Talla;
use App\Exports\Kardex\Stock\KStockExport;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class KStockController extends Controller
{
    public function index()
    {
        $modelos    =   Modelo::where('estado', 'ACTIVO')->get();
        $tallas     =   Talla::where('estado', 'ACTIVO')->get();
        return view('kardex.stock.index', compact('tallas', 'modelos'));
    }

    public function getKStock(Request $request)
    {
        $k_stock = $this->queryKStock($request);

        return DataTables::of($k_stock)
            ->make(true);
    }


    public function queryKStock(Request $request)
    {
        $modelo_id = $request->get('modelo');
        $tallas = Talla::all();

        $selects = [
            'p.id as producto_id',
            'p.nombre as producto_nombre',
            'c.id as color_id',
            'c.descripcion as color_nombre',
        ];

        foreach ($tallas as $talla) {
            $selects[] = DB::raw("
            SUM(CASE WHEN pct.talla_id = {$talla->id} THEN pct.stock_logico ELSE 0 END) as talla_{$talla->id}
        ");
        }

        $query = DB::table('producto_color_tallas as pct')
            ->join('productos as p', 'p.id', '=', 'pct.producto_id')
            ->join('modelos as m', 'm.id', '=', 'p.modelo_id')
            ->join('colores as c', 'c.id', '=', 'pct.color_id')
            ->when($modelo_id, function ($q) use ($modelo_id) {
                return $q->where('m.id', $modelo_id);
            })
            ->where('m.estado', 'ACTIVO')
            ->where('p.estado', 'ACTIVO')
            ->groupBy('p.id', 'p.nombre', 'c.id', 'c.descripcion')
            ->select($selects);

        return $query;
    }


    public function excelKardexStock(Request $request)
    {
        ob_end_clean(); // this
        ob_start(); // and this

        $empresa    =   Empresa::find(1);
        $k_stock    =   $this->queryKStock($request)->get();
        $tallas     =   Talla::where('estado', 'ACTIVO')->get();

        $modelo_nombre = 'TODOS';
        if($request->get('modelo')){
            $modelo_nombre  =   Modelo::findOrFail($request->get('modelo'))->descripcion;
        }
        $request->merge(['modelo_nombre'=>$modelo_nombre]);

        $export = new KStockExport($k_stock, $request, $empresa, $tallas);
        return Excel::download($export, 'k_stock.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
