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
use Barryvdh\DomPDF\Facade as PDF;

class ReciboCajaController extends Controller
{
    public function index(){
        return view('recibos_caja.index');
    }

    public function getRecibosCaja()
    {
        $datos = ReciboCaja::select('recibos_caja.id','recibos_caja.created_at as fecha_recibo', 'clientes.nombre as cliente_nombre',
                'users.usuario as usuario_nombre', 'recibos_caja.metodo_pago', 'recibos_caja.monto', 'recibos_caja.estado',
                'recibos_caja.estado_servicio','recibos_caja.saldo','recibos_caja.observacion')
            ->join('users', 'recibos_caja.user_id', '=', 'users.id')
            ->join('clientes', 'recibos_caja.cliente_id', '=', 'clientes.id')
            ->where('recibos_caja.estado', 'ACTIVO')
            ->get();

        $data = array();
        foreach ($datos as $key => $value) {
            array_push($data, array(
                'id'                =>  $value->id,
                'fecha_recibo'      =>  $value->fecha_recibo,
                'cliente_nombre'    =>  $value->cliente_nombre,
                'usuario_nombre'    =>  $value->usuario_nombre,
                'metodo_pago'       =>  $value->metodo_pago,
                'monto'             =>  $value->monto,
                'estado'            =>  $value->estado,
                'estado_servicio'   =>  $value->estado_servicio,
                'saldo'             =>  $value->saldo,
                'observacion'       =>  $value->observacion
            ));
        }
        return DataTables::of($data)->toJson();
    }

    public function create($pedido_id=null){
        
        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();

        $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
        $clientes           =   Cliente::where('estado', 'ACTIVO')->get();
        $fecha_hoy          =   Carbon::now()->toDateString();
        $condiciones        =   Condicion::where('estado','ACTIVO')->get();

        $vendedor_actual    =   DB::select('select c.id from user_persona as up
                                inner join colaboradores  as c
                                on c.persona_id=up.persona_id
                                where up.user_id = ?',[Auth::id()]);
        $vendedor_actual    =   $vendedor_actual?$vendedor_actual[0]->id:null;

        $pedido_entidad     =   null;
        if($pedido_id){
            $pedido_entidad     =   DB::select('select p.cliente_id,p.total_pagar,p.pedido_nro 
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
            $recibo_caja->observacion   =   trim($request->get('recibo_observacion'));            
            $recibo_caja->save();
            


            if ($request->hasFile('recibo_imagen_1')) {
                $imagen1        = $request->file('recibo_imagen_1');
                Storage::makeDirectory('public/pagos_recibos');
                $nombreImagen1 = 'RC-'.$recibo_caja->id.'-pago-1'. '.' . $imagen1->getClientOriginalExtension();
                $imagen1Path = $imagen1->storeAs('pagos_recibos', $nombreImagen1, 'public');
                $recibo_caja->img_pago      =   'pagos_recibos/'.$nombreImagen1;
                $recibo_caja->update();
            }

            if ($request->hasFile('recibo_imagen_2')) {
                $imagen1        = $request->file('recibo_imagen_2');
                Storage::makeDirectory('public/pagos_recibos');
                $nombreImagen2 = 'RC-'.$recibo_caja->id.'-pago-2'. '.' . $imagen1->getClientOriginalExtension();
                $imagen2Path = $imagen1->storeAs('pagos_recibos', $nombreImagen2, 'public');
                $recibo_caja->img_pago_2      =   'pagos_recibos/'.$nombreImagen2;
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

    public function edit($recibo_caja_id){
        //========== VERIFICANDO SI EL USUARIO ES EL MISMO QUE CREÓ EL RECIBO =========
        $usuario_editar     =   Auth::user()->id;
        $recibo_caja        =   DB::select('select * from recibos_caja as rc
                                    where rc.id=?',[$recibo_caja_id])[0];

        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();
                            
        $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
        $clientes           =   Cliente::where('estado', 'ACTIVO')->get();
        $fecha_hoy          =   Carbon::parse($recibo_caja->created_at)->format('Y-m-d');
        $condiciones        =   Condicion::where('estado','ACTIVO')->get();

        $vendedor_actual    =   DB::select('select c.id from user_persona as up
                                inner join colaboradores  as c
                                on c.persona_id=up.persona_id
                                where up.user_id = ?',[Auth::id()]);
        $vendedor_actual    =   $vendedor_actual?$vendedor_actual[0]->id:null;

        //======= VALIDAR QUE SOLO EL CREADOR DEL RECIBO,PUEDA EDITAR EL RECIBO ==========
        if($usuario_editar !== $recibo_caja->user_id){
            Session::flash('recibo_caja_error', 'SOLO EL USUARIO QUE CREÓ EL RECIBO PUEDE EDITARLO');
            return redirect()->back();  
        }

        //======== VALIDAR QUE EL RECIBO SOLO SE EDITE SI ESTÁ EN ESTADO DE SERVICIO LIBRE =========
        if($recibo_caja->estado_servicio !== "LIBRE"){
            Session::flash('recibo_caja_error', 'SOLO PUEDEN EDITARSE RECIBOS ANTES DE SU USO');
            return redirect()->back();  
        }

       //======== VALIDAR QUE LA EDICIÓN SE REALIZE SOLO SI EL MOVIMIENTO DEL USUARIO ESTÁ APERTURADO =======
       $movimiento   =   DB::select('select mc.estado_movimiento from movimiento_caja as mc
                        where mc.id=?',[$recibo_caja->movimiento_id]);
       
        if($movimiento[0]->estado_movimiento === "CIERRE"){
            Session::flash('recibo_caja_error', 'EL MOVIMIENTO DE CAJA DEL USUARIO HA CERRADO');
            return redirect()->back();  
        }

        return view('recibos_caja.edit',compact('recibo_caja','vendedor_actual','empresas',
        'clientes', 'fecha_hoy', 'condiciones','tipos_documento','departamentos','tipo_clientes'));
    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        try {
            //========= BUSCAR EL RECIBO DE CAJA PARA EDITAR =========
            
            $recibo_caja                =   ReciboCaja::find($id);
            $recibo_caja->cliente_id    =   $request->get('cliente');
            $recibo_caja->monto         =   $request->get('monto');
            $recibo_caja->saldo         =   $request->get('monto');
            $recibo_caja->metodo_pago   =   $request->get('metodo_pago');
            $recibo_caja->observacion   =   $request->get('recibo_observacion');
            $recibo_caja->update();
            

            if ($request->hasFile('recibo_imagen_1')) {
                $imagen1        = $request->file('recibo_imagen_1');
                Storage::makeDirectory('public/pagos_recibos');
                $nombreImagen1 = 'RC-'.$recibo_caja->id.'-pago-1'. '.' . $imagen1->getClientOriginalExtension();
                $imagen1Path = $imagen1->storeAs('pagos_recibos', $nombreImagen1, 'public');
                $recibo_caja->img_pago      =   'pagos_recibos/'.$nombreImagen1;
                $recibo_caja->update();
            }else{
                $imagenAnterior = $recibo_caja->img_pago;

                if ($imagenAnterior) {
                    Storage::delete('public/'.$imagenAnterior);
                    
                    $recibo_caja->img_pago = null;
                    $recibo_caja->update();
                }
            }

            if ($request->hasFile('recibo_imagen_2')) {
                $imagen1        = $request->file('recibo_imagen_2');
                Storage::makeDirectory('public/pagos_recibos');
                $nombreImagen2 = 'RC-'.$recibo_caja->id.'-pago-2'. '.' . $imagen1->getClientOriginalExtension();
                $imagen2Path = $imagen1->storeAs('pagos_recibos', $nombreImagen2, 'public');
                $recibo_caja->img_pago_2      =   'pagos_recibos/'.$nombreImagen2;
                $recibo_caja->update();
            }else{
                if ($imagenAnterior) {
                    Storage::delete('public/'.$imagenAnterior);
                    
                    $recibo_caja->img_pago_2 = null;
                    $recibo_caja->update();
                }
            }

            
            DB::commit();
            Session::flash('recibo_caja_success', 'RECIBO CAJA ACTUALIZADO');
            return redirect()->route('recibos_caja.index');        
        } catch (\Throwable $th) {
            DB::rollback();
            Session::flash('recibo_caja_error', $th->getMessage());
            return redirect()->back();        
        }
    }

    public function destroy(Request $request,$recibo_caja_id){
        $recibo_caja        =   DB::select('select * from recibos_caja as rc
                                    where rc.id=?',[$recibo_caja_id])[0];

        $usuario_destroy    =   Auth::user()->id;

        //======= VALIDAR QUE SOLO EL CREADOR DEL RECIBO,PUEDA EDITAR EL RECIBO ==========
        if($usuario_destroy !== $recibo_caja->user_id){
            Session::flash('recibo_caja_error', 'SOLO EL USUARIO QUE CREÓ EL RECIBO PUEDE ELIMINARLO');
            return redirect()->back();  
        }

        //======== VALIDAR QUE EL RECIBO SOLO SE EDITE SI ESTÁ EN ESTADO DE SERVICIO LIBRE =========
        if($recibo_caja->estado_servicio !== "LIBRE"){
            Session::flash('recibo_caja_error', 'SOLO PUEDEN ELIMINARSE RECIBOS ANTES DE SU USO');
            return redirect()->back();  
        }

        //======== VALIDAR QUE LA EDICIÓN SE REALIZE SOLO SI EL MOVIMIENTO DEL USUARIO ESTÁ APERTURADO =======
        $movimiento   =   DB::select('select mc.estado_movimiento from movimiento_caja as mc
                        where mc.id=?',[$recibo_caja->movimiento_id]);
       
        if($movimiento[0]->estado_movimiento === "CIERRE"){
            Session::flash('recibo_caja_error', 'EL MOVIMIENTO DE CAJA DEL USUARIO HA CERRADO');
            return redirect()->back();  
        }

        DB::table('recibos_caja')
        ->where('id', $recibo_caja_id)
        ->update([
            'updated_at'    =>  Carbon::now(),
            'estado'        =>  'ANULADO'
        ]);

        Session::flash('recibo_caja_success', 'RECIBO CAJA ELIMINADO');
        return redirect()->route('recibos_caja.index');        
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


    public function pdf($size, $recibo_caja_id)
    {
        $recibo_caja     = DB::select('select u.usuario as usuario_nombre,c.nombre as cliente_nombre,
                                rc.monto,rc.metodo_pago,rc.estado_servicio,rc.created_at,rc.id,
                                c.tipo_documento as cliente_tipo_doc,c.documento as cliente_documento,
                                c.direccion as cliente_direccion,rc.observacion,rc.saldo
                                from recibos_caja as rc
                                inner join users as u   on u.id=rc.user_id
                                inner join clientes as c on c.id=rc.cliente_id
                                where rc.id=?',[$recibo_caja_id])[0];

        $empresa    = Empresa::first();
        if ($size == 80) {
            $pdf = PDF::loadView('recibos_caja.Imprimir.ticket', compact('recibo_caja','empresa'));
            $pdf->setpaper([0, 0, 226.772, 651.95]);
        }
        else{
            $pdf = PDF::loadView('recibos_caja.Imprimir.normal', compact('recibo_caja','empresa'));
        }
        return $pdf->stream('recibo.pdf');
    }


    public function detalles($recibo_caja_id){
        try {
            $detalles       =   DB::select('select cd.serie as documento_serie,cd.correlativo as documento_correlativo,
                                rcd.saldo_antes,rcd.monto_usado,rcd.saldo_despues,rcd.created_at as fecha_uso,u.usuario
                                from recibos_caja_detalle as rcd
                                inner join cotizacion_documento as cd on cd.id=rcd.documento_id
                                inner join users as u on u.id=cd.user_id
                                where rcd.recibo_id=?
                                order by rcd.id desc',[$recibo_caja_id]);

            return response()->json(['success'=>true,'detalles'=>$detalles]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>'ERROR EN EL SERVIDOR AL OBTENER DETALLES DEL RECIBO',
            'exception'=>$th->getMessage()]);
        }
    }
}
