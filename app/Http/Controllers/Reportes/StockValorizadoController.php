<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockValorizadoController extends Controller
{
    public function index()
    {
        return view('reportes.almacenes.producto.stockValorizado');
    }

    public function getTable()
    {
        return datatables()->query(
            DB::table('productos')
            ->join('marcas','productos.marca_id','=','marcas.id')
            ->join('almacenes','almacenes.id','=','productos.almacen_id')
            ->join('categorias','categorias.id','=','productos.categoria_id')
            ->select(
                'categorias.descripcion as categoria',
                'almacenes.descripcion as almacen',
                'marcas.marca',
                'productos.*',
                DB::raw('(productos.stock * productos.precio_venta_minimo) as stock_valorizado')
            )
            ->orderBy('productos.id','ASC')
            ->where('productos.estado', 'ACTIVO')
        )->toJson();
    }
}
