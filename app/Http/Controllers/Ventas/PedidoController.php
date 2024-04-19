<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ventas\Pedido;


class PedidoController extends Controller
{
    public function index(){
        return view('ventas.pedidos.index');
    }

    public function getTable(Request $request){
        $fecha_inicio   =   $request->get('fecha_inicio');
        $fecha_fin      =   $request->get('fecha_fin');

        $pedidos = Pedido::where('estado', '!=', 'ANULADO')
                    ->whereBetween('fecha_registro', [$fecha_inicio, $fecha_fin])
                    ->get();


        return response()->json(['message'=>$pedidos]);

    }
}
