<?php

namespace App\Http\Controllers\ReciboCaja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Pos\ReciboCaja;
use Yajra\DataTables\Facades\DataTables;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Ventas\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReciboCajaRequest;

class ReciboCajaController extends Controller
{
    public function index(){
        return view('recibos_caja.index');
    }

    public function getRecibosCaja()
    {
        $datos = ReciboCaja::select('recibos_caja.created_at as fecha_recibo', 'clientes.nombre as cliente_nombre',
                'users.usuario as usuario_nombre', 'recibos_caja.metodo_pago', 'recibos_caja.monto', 'recibos_caja.estado',
                'recibos_caja.estado_servicio')
            ->join('users', 'recibos_caja.user_id', '=', 'users.id')
            ->join('clientes', 'recibos_caja.cliente_id', '=', 'clientes.id')
            ->where('recibos_caja.estado', 'ACTIVO')
            ->get();

        $data = array();
        foreach ($datos as $key => $value) {
            array_push($data, array(
                'fecha_recibo'      => $value->fecha_recibo,
                'cliente_nombre'    => $value->cliente_nombre,
                'usuario_nombre'    => $value->usuario_nombre,
                'metodo_pago'       => $value->metodo_pago,
                'monto'             => $value->monto,
                'estado'            => $value->estado,
                'estado_servicio'   => $value->estado_servicio
            ));
        }
        return DataTables::of($data)->toJson();
    }

    public function create(){
        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();

        $empresas           = Empresa::where('estado', 'ACTIVO')->get();
        $clientes           = Cliente::where('estado', 'ACTIVO')->get();
        $fecha_hoy          = Carbon::now()->toDateString();
        $condiciones        = Condicion::where('estado','ACTIVO')->get();

        $vendedor_actual    =   DB::select('select c.id from user_persona as up
        inner join colaboradores  as c
        on c.persona_id=up.persona_id
        where up.user_id = ?',[Auth::id()]);
        $vendedor_actual    =   $vendedor_actual?$vendedor_actual[0]->id:null;




        return view('recibos_caja.create',compact('vendedor_actual','empresas',
        'clientes', 'fecha_hoy', 'condiciones','tipos_documento','departamentos','tipo_clientes'));
    }

    public function store(ReciboCajaRequest $request){

        DB::beginTransaction();
        try {
            dd($request->all());
            $recibo_caja    =   new ReciboCaja();
            
        } catch (\Throwable $th) {
            //throw $th;
        }
       
      
    }

    public function buscarCajaApertUsuario(){

        try {
            //========== VERIFICAR SI EL USUARIO SE ENCUENTRA EN ALGUNA CAJA APERTURADA ===========
            $caja_aperturada    =   DB::select('select mc.caja_id,c.nombre from detalles_movimiento_caja as dmc
                                    inner join movimiento_caja as mc  on dmc.movimiento_id=mc.id
                                    inner join caja as c on c.id=mc.caja_id
                                    where estado_movimiento = "APERTURA" and dmc.usuario_id=?',[Auth::user()->id]);

            if(count($caja_aperturada) === 0){
                return response()->json(['success'=>false,
                'message'=>"USTED NO SE ENCUENTRA ASOCIADO A NINGUNA CAJA APERTURADA ACTUALMENTE"]);
            }else{
                return response()->json(['success'=>true,'message'=>'EL RECIBO SE ASIGNARÁ A LA CAJA: '.
                $caja_aperturada[0]->nombre,
                'caja_id'=>$caja_aperturada->caja_id]);
            }
        } catch (\Throwable $th) {
           return response()->json(['success'=>false,'message'=>'ERROR EN EL SERVIDOR AL BUSCAR LA CAJA DEL USUARIO',
                                'exception'=>$th->getMessage()]);
        }
      


    }
}
