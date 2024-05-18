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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ReciboCajaController extends Controller
{
    public function index(){
        return view('recibos_caja.index');
    }

    public function getRecibosCaja()
    {
        $datos = ReciboCaja::select('recibos_caja.created_at as fecha_recibo', 'clientes.nombre as cliente_nombre',
                'users.usuario as usuario_nombre', 'recibos_caja.metodo_pago', 'recibos_caja.monto', 'recibos_caja.estado',
                'recibos_caja.estado_servicio','recibos_caja.saldo')
            ->join('users', 'recibos_caja.user_id', '=', 'users.id')
            ->join('clientes', 'recibos_caja.cliente_id', '=', 'clientes.id')
            ->where('recibos_caja.estado', 'ACTIVO')
            ->get();

        $data = array();
        foreach ($datos as $key => $value) {
            array_push($data, array(
                'fecha_recibo'      =>  $value->fecha_recibo,
                'cliente_nombre'    =>  $value->cliente_nombre,
                'usuario_nombre'    =>  $value->usuario_nombre,
                'metodo_pago'       =>  $value->metodo_pago,
                'monto'             =>  $value->monto,
                'estado'            =>  $value->estado,
                'estado_servicio'   =>  $value->estado_servicio,
                'saldo'             =>  $value->saldo
            ));
        }
        return DataTables::of($data)->toJson();
    }

    public function create($pedido_id=null){
        
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

        $pedido_entidad     =   null;
        if($pedido_id){
            $pedido_entidad     =   DB::select('select p.cliente_id,p.total_pagar 
                                    from pedidos as p
                                    where p.id=?',[$pedido_id]);  
        }


        return view('recibos_caja.create',compact('vendedor_actual','empresas',
        'clientes', 'fecha_hoy', 'condiciones','tipos_documento','departamentos','tipo_clientes','pedido_entidad'));
    }

    public function store(ReciboCajaRequest $request){
        DB::beginTransaction();
        try {
            //========= GUARDAR EL RECIBO DE CAJA EN EL MOVIMIENTO DEL USUARIO =========
            

            $recibo_caja                =   new ReciboCaja();
            $recibo_caja->movimiento_id =   $request->get('movimiento_id');
            $recibo_caja->user_id       =   Auth::user()->id;
            $recibo_caja->cliente_id    =   $request->get('cliente');
            $recibo_caja->monto         =   $request->get('monto');
            $recibo_caja->saldo         =   $request->get('monto');
            $recibo_caja->metodo_pago   =   $request->get('metodo_pago');
            $recibo_caja->observacion   =   $request->get('recibo_observacion');
            $recibo_caja->save();
            


            if ($request->hasFile('recibo_imagen_1')) {
                $imagen1        = $request->file('recibo_imagen_1');
                Storage::makeDirectory('public/pagos_recibos');
                $nombreImagen1 = 'RC-'.$recibo_caja->id.'-pago-1'. '.' . $imagen1->getClientOriginalExtension();
                $imagen1Path = $imagen1->storeAs('pagos_recibos', $nombreImagen1, 'public');
                $recibo_caja->img_pago      =   'public/pagos_recibos/'.$nombreImagen1;
                $recibo_caja->update();
            }

            if ($request->hasFile('recibo_imagen_2')) {
                $imagen1        = $request->file('recibo_imagen_2');
                Storage::makeDirectory('public/pagos_recibos');
                $nombreImagen2 = 'RC-'.$recibo_caja->id.'-pago-2'. '.' . $imagen1->getClientOriginalExtension();
                $imagen2Path = $imagen1->storeAs('pagos_recibos', $nombreImagen2, 'public');
                $recibo_caja->img_pago_2      =   'public/pagos_recibos/'.$nombreImagen2;
                $recibo_caja->update();
            }

            
            DB::commit();
            Session::flash('recibo_caja_success', 'RECIBO CAJA REGISTRADO');
            return redirect()->route('recibos_caja.index');        
        } catch (\Throwable $th) {
            DB::rollback();
            Session::flash('recibo_caja_error', $th->getMessage());
            return redirect()->back();        
        }
       
    }

    public function buscarCajaApertUsuario(){

        try {
            //========== VERIFICAR SI EL USUARIO SE ENCUENTRA EN ALGUNA CAJA APERTURADA ===========
            $caja_aperturada    =   DB::select('select mc.id as movimiento_id,c.nombre from detalles_movimiento_caja as dmc
                                    inner join movimiento_caja as mc  on dmc.movimiento_id=mc.id
                                    inner join caja as c on c.id=mc.caja_id
                                    where estado_movimiento = "APERTURA" and dmc.usuario_id=?',[Auth::user()->id]);
            
            if(count($caja_aperturada) === 0){
                return response()->json(['success'=>false,
                'message'=>"USTED NO SE ENCUENTRA ASOCIADO A NINGUNA CAJA APERTURADA ACTUALMENTE"]);
            }else{
                return response()->json(['success'=>true,'message'=>'EL RECIBO SE ASIGNARÁ A LA CAJA: '.
                $caja_aperturada[0]->nombre,
                'movimiento_id'=>$caja_aperturada[0]->movimiento_id]);
            }
        } catch (\Throwable $th) {
           return response()->json(['success'=>false,'message'=>'ERROR EN EL SERVIDOR AL BUSCAR LA CAJA DEL USUARIO',
                                'exception'=>$th->getMessage()]);
        }
      
    }
}
