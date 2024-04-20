<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ventas\Pedido;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Ventas\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Almacenes\Modelo;
use App\Almacenes\Talla;

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

    public function create(){
        $empresas           = Empresa::where('estado', 'ACTIVO')->get();
        $clientes           = Cliente::where('estado', 'ACTIVO')->get();  
        $condiciones        = Condicion::where('estado','ACTIVO')->get();
        $vendedor_actual    =   DB::select('select c.id from user_persona as up
        inner join colaboradores  as c
        on c.persona_id=up.persona_id
        where up.user_id = ?',[Auth::id()]);
        $vendedor_actual    =   $vendedor_actual?$vendedor_actual[0]->id:null;     
        $modelos            = Modelo::where('estado','ACTIVO')->get();
        $tallas             = Talla::where('estado','ACTIVO')->get();
        
        return view('ventas.pedidos.create',compact('empresas','clientes','vendedor_actual','condiciones',
                                            'modelos','tallas'));
    }
}
