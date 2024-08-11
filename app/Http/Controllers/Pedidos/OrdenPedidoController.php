<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class OrdenPedidoController extends Controller
{
    public function index(){
        return view('pedidos.ordenes.index');
    }

    public function getTable(Request $request){
        
        $ordenes_pedido =   DB::select('select CONCAT("OP","-",op.id) as op_id_name,
                            op.id, op.user_id, op.user_nombre,op.fecha_propuesta_atencion,op.observacion,
                            op.created_at as fecha_registro,op.estado 
                            from ordenes_pedido as op 
                            where op.estado != "ANULADO"');

        
        return DataTables::of($ordenes_pedido)->toJson();
    }

}
