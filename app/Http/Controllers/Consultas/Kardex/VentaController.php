<?php

namespace App\Http\Controllers\Consultas\Kardex;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        return view('consultas.kardex.venta.index');
    }

    public function getTable(Request $request)
    {

        $query  =   DB::table('cotizacion_documento_detalles as cdd')
            ->join('cotizacion_documento as cd', 'cd.id', 'cdd.documento_id')
            ->leftJoin('empresa_sedes as es', 'es.id', 'cd.sede_id')
            ->leftJoin('users as u', 'u.id', 'cd.user_id')
            ->leftJoin('envios_ventas as ev', 'ev.documento_id', 'cd.id')
            ->select(
                'cd.created_at as fecha_registro',
                DB::raw('CONCAT(cd.serie,"-",cd.correlativo) as codigo'),
                'cdd.almacen_nombre',
                'cdd.nombre_modelo',
                'cdd.nombre_producto',
                'cdd.nombre_color',
                'cdd.nombre_talla',
                DB::raw('CAST(cdd.cantidad AS UNSIGNED) as cantidad'),
                'es.nombre as sede',
                'u.usuario as registrador_nombre'
            ) ->where(function($q) {
        $q->where('ev.estado', '<>', 'RESERVADO')
          ->where(function($sub) {
              $sub->whereNull('ev.modo') 
                   ->orWhere('ev.modo', '<>', 'RESERVA')
                   ->orWhere('ev.estado', '=', 'DESPACHADO');
          });
    });;

        if ($request->get('fecha_inicio')) {
            $query = $query->whereRaw('DATE(cd.created_at) >= ?', [$request->get('fecha_inicio')]);
        }

        if ($request->get('fecha_fin')) {
            $query = $query->whereRaw('DATE(cd.created_at) <= ?', [$request->get('fecha_fin')]);
        }

        return DataTables::of($query)
            ->filterColumn('codigo', function ($_query, $keyword) {
                $_query->whereRaw("CONCAT(cd.serie, '-', cd.correlativo) like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }
}
