<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pedido\PedidoUpdateRequest;
use Exception;
use Illuminate\Http\Request;
use App\Ventas\Pedido;
use App\Ventas\PedidoDetalle;
use App\Ventas\PedidoAtencion;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Ventas\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Almacenes\Modelo;
use App\Almacenes\Talla;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Collection;
use App\Ventas\Documento\Documento;
use  App\Http\Controllers\Ventas\DocumentoController;
use App\Ventas\Documento\Detalle;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Pedido\PedidosExport;
use App\Pos\ReciboCaja;

class PedidoController extends Controller
{
    public function index(){
        return view('pedidos.pedido.index');
    }

    public function getTable(Request $request){
        $fecha_inicio   =   $request->get('fecha_inicio');
        $fecha_fin      =   $request->get('fecha_fin');
        $pedido_estado  =   $request->get('pedido_estado');

        $pedidos = Pedido::select('pedidos.*', 
                            \DB::raw('CONCAT(pedidos.documento_venta_facturacion_serie, "-", pedidos.documento_venta_facturacion_correlativo) as documento_venta'),
                            \DB::raw('if(pedidos.cotizacion_id is null,"-",concat("CO-",pedidos.cotizacion_id)) as cotizacion_nro'))
                    ->where('pedidos.estado','!=','ANULADO');            
                    

        if($fecha_inicio){
            $pedidos    =   $pedidos->where('fecha_registro', '>=', $fecha_inicio);
        }

        if($fecha_fin){
            $pedidos    =   $pedidos->where('fecha_registro', '<=', $fecha_fin);
        }

        if($pedido_estado){
            $pedidos    =   $pedidos->where('pedidos.estado', '=', $pedido_estado);
        }
        
        return DataTables::of($pedidos->get())->toJson();
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

        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();

        
        return view('pedidos.pedido.create',compact('empresas','clientes','vendedor_actual','condiciones',
                                            'modelos','tallas','tipos_documento','departamentos','tipo_clientes'));
    }

    public function store(Request $request){
      
        DB::beginTransaction();
        try {
            $productos  = json_decode($request->input('productos_tabla')[0]);
            $data       =   $request->all();

            $rules = [
                'empresa' => 'required',
                'cliente' => 'required',
                'condicion_id' => 'required',
                'fecha_documento' => 'required',
                'fecha_atencion' => 'required',
            ];
    
            $message = [
                'empresa.required' => 'El campo Empresa es obligatorio',
                'cliente.required' => 'El campo Cliente es obligatorio',
                'condicion_id.required' => 'El campo condicion es obligatorio',
                'moneda' => 'El campo Moneda es obligatorio',
                'fecha_documento.required' => 'El campo Fecha de Documento es obligatorio',
            ];

            Validator::make($data, $rules, $message)->validate();

            //======= MANEJANDO MONTOS ========
            $monto_embalaje     =   $request->has('monto_embalaje')?$request->get('monto_embalaje'):0;
            $monto_envio        =   $request->has('monto_envio')?$request->get('monto_envio'):0;
            $monto_subtotal     =   0.0;
            $monto_total        =   0.0;
            $monto_igv          =   0.0;
            $monto_total_pagar  =   0.0;
            $monto_descuento    =   $request->get('monto_descuento')??0;

            foreach ($productos as $producto) {
                $precio = 0;
                if( floatval($producto->porcentaje_descuento) == 0){
                    $precio =   $producto->precio_venta;
                }else{
                    $precio =   $producto->precio_venta_nuevo;
                }

                foreach ($producto->tallas as $talla){
                    $monto_subtotal +=  $talla->cantidad * $precio;
                }
            }
    
            $monto_total_pagar      =   $monto_subtotal + $monto_embalaje + $monto_envio;
            $monto_total            =   $monto_total_pagar/1.18;
            $monto_igv              =   $monto_total_pagar-$monto_total;
            $porcentaje_descuento   =   ($monto_descuento*100)/($monto_total_pagar);


            //======== REGISTRANDO PEDIDO =========
            $pedido = new Pedido();
            $pedido->cliente_id         = $request->get('cliente');

            //======== BUSCANDO NOMBRE DEL CLIENTE =====//
            $cliente    =   DB::select('select c.id,c.nombre,c.telefono_movil from clientes as c
                            where c.id=?',[$request->get('cliente')]);
            
            $pedido->cliente_nombre     =   $cliente[0]->nombre;
            $pedido->cliente_telefono   =   $cliente[0]->telefono_movil;
            //==========================================//

            $pedido->empresa_id         = $request->get('empresa');
            //======== BUSCANDO NOMBRE DE LA EMPRESA =====//
            $empresa    =   DB::select('select e.id,e.razon_social from empresas as e
                            where e.id=?',[$request->get('empresa')]);
            
            $pedido->empresa_nombre     =   $empresa[0]->razon_social;
            //==========================================//


            $pedido->condicion_id       = $request->get('condicion_id');
            $pedido->user_id            = Auth::user()->id;

            //======== OBTENIENDO EL NOMBRE COMPLETO DEL USUARIO ===========
            $pedido->user_nombre        = DB::select('select CONCAT(p.nombres, " ", p.apellido_paterno, " ", p.apellido_materno) AS user_nombre 
                                            from users as u
                                            inner join user_persona as up 
                                            on u.id=up.user_id
                                            inner join personas as p
                                            on p.id=up.persona_id
                                            where u.id=?',[Auth::user()->id])[0]->user_nombre;
            //=============================================================
            $pedido->moneda                 = 1;
            $pedido->fecha_registro         = $request->get('fecha_documento');
            $pedido->fecha_propuesta        = $request->get('fecha_propuesta');

            $pedido->monto_embalaje         =   $monto_embalaje;
            $pedido->monto_envio            =   $monto_envio;
            $pedido->sub_total              =   $monto_subtotal;
            $pedido->total_igv              =   $monto_igv;
            $pedido->total                  =   $monto_total;
            $pedido->total_pagar            =   $monto_total_pagar;  
            $pedido->monto_descuento        =   $monto_descuento;
            $pedido->porcentaje_descuento   =   $porcentaje_descuento;

            //======= CONTANDO PEDIDOS ======
            $cantidad_pedidos   =   Pedido::count();
            $pedido->pedido_nro =   $cantidad_pedidos+1;

            $pedido->save();


            //========== GRABAR DETALLE DEL PEDIDO ========
            foreach ($productos as $producto) {
                foreach ($producto->tallas as  $talla) {
                     //===== CALCULANDO MONTOS PARA EL DETALLE =====
                    $importe        =   floatval($talla->cantidad) * floatval($producto->precio_venta);
                    $precio_venta   =   $producto->porcentaje_descuento == 0?$producto->precio_venta:$producto->precio_venta_nuevo;
        
                    PedidoDetalle::create([
                        'pedido_id'                 => $pedido->id,
                        'producto_id'               => $producto->producto_id,
                        'color_id'                  => $producto->color_id,
                        'talla_id'                  => $talla->talla_id,
                        'producto_codigo'           => $producto->producto_codigo,
                        'producto_nombre'           => $producto->producto_nombre,
                        'color_nombre'              => $producto->color_nombre,
                        'talla_nombre'              => $talla->talla_nombre,
                        'modelo_nombre'             => $producto->modelo_nombre,
                        'cantidad'                  => $talla->cantidad,
                        'cantidad_atendida'         => 0,
                        'cantidad_pendiente'        => $talla->cantidad,
                        'precio_unitario'           => $producto->precio_venta,
                        'importe'                   => $importe,
                        'porcentaje_descuento'      =>  floatval($producto->porcentaje_descuento),
                        'precio_unitario_nuevo'     =>  floatval($precio_venta),
                        'importe_nuevo'             =>  floatval($precio_venta) * floatval($talla->cantidad),  
                        'monto_descuento'           =>  floatval($importe)*floatval($producto->porcentaje_descuento)/100,
                    ]);
                }
            }

            //====== REGISTRO DE ACTIVIDAD ========
            $descripcion = "SE AGREGÓ EL PEDIDO CON LA FECHA: ". Carbon::parse($pedido->fecha_registro)->format('d/m/y');
            $gestion = "PEDIDO";
            crearRegistro($pedido, $descripcion , $gestion);

            DB::commit();
            Session::flash('success','Pedido creado.');
            return redirect()->route('pedidos.pedido.index')->with('guardar', 'success');

        } catch (\Throwable  $e) {
            DB::rollback();

            dd($e->getMessage());
        }
    }

    public function edit($id){

        $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
        $clientes           =   Cliente::where('estado', 'ACTIVO')->get();  
        $condiciones        =   Condicion::where('estado','ACTIVO')->get();

        //======= OBTENIENDO DATOS DEL PEDIDO ========
        $pedido             =   Pedido::find($id);
        $pedido_detalles    =   PedidoDetalle::where('pedido_id',$id)->get();

        //========= VALIDAR QUE EL PEDIDO NO ESTÉ FACTURADO =======
        if($pedido->facturado === 'SI'){
            Session::flash('pedido_error','NO SE PUEDEN EDITAR LOS PEDIDOS FACTURADOS');
            return back();
        }

        //======= VALIDAR QUE EL PEDIDO NO ESTÉ ANULADO NI FINALIZADO ======
        if($pedido->estado === 'ANULADO' || $pedido->estado === "FINALIZADO"){
            Session::flash('pedido_error','NO SE PUEDEN EDITAR LOS PEDIDOS ANULADOS O FINALIZADOS');
            return back();
        }

        //========= LOS PEDIDOS NO FACTURADOS PUEDEN EDITARSE ========
        //========= PUEDEN AGREGARSE NUEVOS PRODUCTOS AL PEDIDO =========
        //========= DE LOS PRODUCTOS YA EXISTENTES, PUEDEN EDITARSE LAS CANTIDADES PENDIENTE =======
        //========= DE LOS PRODUCTOS YA EXISTENTES, NO SE PUEDEN TOCAR LAS CANTIDADES ATENDIDAS =====
        //========= LAS CANTIDADES ATENDIDAS PUEDEN MODIFICARSE MEDIANTE NOTAS DE DEVOLUCIÓN/CRÉDITO O CAMBIOS DE TALLA SOBRE EL DOCUMENTO VENTA DE ATENCIÓN =======
        

        $vendedor_actual    =   DB::select('select c.id from user_persona as up
                                inner join colaboradores  as c
                                on c.persona_id=up.persona_id
                                where up.user_id = ?',[$pedido->user_id]);

        $vendedor_actual_id =   $vendedor_actual?$vendedor_actual[0]->id:null;  
        $modelos            =   Modelo::where('estado','ACTIVO')->get();
        $tallas             =   Talla::where('estado','ACTIVO')->get();
        $pedido_id          =   $id;

        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();

        
        
        return view('pedidos.pedido.edit',compact('empresas','clientes','vendedor_actual_id','condiciones',
                                            'modelos','tallas','pedido','pedido_detalles',
                                        'tipos_documento','departamentos','tipo_clientes'));
    }

    public function update(PedidoUpdateRequest $request,$id){
        
        DB::beginTransaction();
        try {
            
            $productos  =   json_decode($request->get('lstProductos'));
            $data       =   $request->all();
            $lstProductos   =   [];

            //======== REFORMATEANDO LST PRODUCTOS =======
            foreach ($productos as $producto) {
                foreach ($producto->tallas as $talla){
                    $producto   =   (object)['producto_id'=>$producto->producto_id,
                                            'color_id'=>$producto->color_id,
                                            'talla_id'=>$talla->talla_id,
                                            'producto_nombre'=>$producto->producto_nombre,
                                            'color_nombre'=>$producto->color_nombre,
                                            'talla_nombre'=>$talla->talla_nombre,
                                            'cantidad'=>$talla->cantidad];
                    $lstProductos[] =   $producto;
                }
            }


            //======== VALIDAR LISTADO DE PRODUCTOS ========
            $requestValidarCantidadAtendida = Request::create('/dummy-url', 'GET', [
                'lstProductos' => json_encode($lstProductos)
            ]);

            $resValidarCantidadAtendida =   $this->validarCantidadAtendida($requestValidarCantidadAtendida);
            $data                       =   $resValidarCantidadAtendida->getContent();
            $data                       =   json_decode($data, true); 

            //======= EN CASO DE VALIDACIÓN COMPLETADA ======
            if($data['success']){
                $lstErroresValidacion   =   $data['lstErroresValidacion'];
                //========== EN CASO HAYAN ERRORES DE VALIDACIÓN, RETORNAR SUCCESS FALSE =======
                if(count($lstErroresValidacion) > 0){
                    return response()->json($data);
                }
            }

            //========== EN CASO DE ERROR AL VALIDAR =======
            if(!$data['success']){
                return response()->json($data);
            }

            //======= MANEJANDO MONTOS ========
            $monto_embalaje     =   $request->has('monto_embalaje')?$request->get('monto_embalaje'):0;
            $monto_envio        =   $request->has('monto_envio')?$request->get('monto_envio'):0;
            $monto_subtotal     =   0.0;
            $monto_total        =   0.0;
            $monto_igv          =   0.0;
            $monto_total_pagar  =   0.0;
            $monto_descuento    =   $request->get('monto_descuento')??0;

            //====== OBTENIENDO SUBTOTAL DE LOS PRODUCTOS ======
            foreach ($productos as $producto) {
                $precio = 0;
                if( floatval($producto->porcentaje_descuento) == 0){
                    $precio =   $producto->precio_venta;
                }else{
                    $precio =   $producto->precio_venta_nuevo;
                }

                foreach ($producto->tallas as $talla){
                    $monto_subtotal +=  $talla->cantidad * $precio;
                }
            }

            //====== CALCULAMOS LOS MONTOS ======
            $monto_total_pagar      =   $monto_subtotal + $monto_embalaje + $monto_envio;
            $monto_total            =   $monto_total_pagar/1.18;
            $monto_igv              =   $monto_total_pagar-$monto_total;
            $porcentaje_descuento   =   ($monto_descuento*100)/($monto_total_pagar);
           
            //======== REGISTRANDO PEDIDO =========
            $pedido                 = Pedido::find($id);
            $pedido->cliente_id     = $request->get('cliente');

            //======== BUSCANDO NOMBRE DEL CLIENTE =====//
            $cliente    =   DB::select('select c.id,c.nombre,c.telefono_movil from clientes as c
                            where c.id=?',[$request->get('cliente')]);
            
            $pedido->cliente_nombre     =   $cliente[0]->nombre;
            $pedido->cliente_telefono   =   $cliente[0]->telefono_movil;

            //==========================================//

            $pedido->empresa_id         = $request->get('empresa');
            //======== BUSCANDO NOMBRE DE LA EMPRESA =====//
            $empresa    =   DB::select('select e.id,e.razon_social from empresas as e
                            where e.id=?',[$request->get('empresa')]);
            
            $pedido->empresa_nombre     =   $empresa[0]->razon_social;
            //==========================================//


            $pedido->condicion_id       =   $request->get('condicion_id');
            //$pedido->user_id            =   Auth::user()->id;

            //======== OBTENIENDO EL NOMBRE COMPLETO DEL USUARIO ===========
            // $pedido->user_nombre        = DB::select('select CONCAT(p.nombres, " ", p.apellido_paterno, " ", p.apellido_materno) AS user_nombre 
            //                                 from users as u
            //                                 inner join user_persona as up 
            //                                 on u.id=up.user_id
            //                                 inner join personas as p
            //                                 on p.id=up.persona_id
            //                                 where u.id=?',[Auth::user()->id])[0]->user_nombre;

            $pedido->moneda                 =   1;
            //$pedido->fecha_registro       =   $request->get('fecha_documento');

            $pedido->fecha_propuesta        =   $request->get('fecha_propuesta');
            $pedido->sub_total              =   $monto_subtotal;
            $pedido->total_igv              =   $monto_igv;
            $pedido->total                  =   $monto_total;
            $pedido->total_pagar            =   $monto_total_pagar;  
            $pedido->monto_descuento        =   $monto_descuento;
            $pedido->porcentaje_descuento   =   $porcentaje_descuento;
            $pedido->monto_embalaje         =   $monto_embalaje;
            $pedido->monto_envio            =   $monto_envio;
            $pedido->update();

           
            //======= ELIMINANDO DETALLE ANTERIOR, SIEMPRE Y CUANDO NO SE HAYA ATENDIDO AÚN ========
            if(count($productos)>0){
                PedidoDetalle::where('pedido_id', $id)
                ->where('cantidad_atendida',0)
                ->delete();
            }

            //========== GRABAR DETALLE DEL PEDIDO ========
            foreach ($productos as $producto) {
                foreach ($producto->tallas as  $talla) {

                    //===== CALCULANDO MONTOS PARA EL DETALLE =====
                    $importe        =   floatval($talla->cantidad) * floatval($producto->precio_venta);
                    $precio_venta   =   $producto->porcentaje_descuento == 0?$producto->precio_venta:$producto->precio_venta_nuevo;
                    
                    //======= BUSCANDO SI EXISTE EL PRODUCTO EN EL DETALLE DEL PEDIDO =====
                    $producto_existe                        =   DB::select('select pd.producto_id,pd.color_id,pd.talla_id,pd.cantidad_atendida
                                                                from pedidos_detalles as pd
                                                                where pd.pedido_id = ? and
                                                                pd.producto_id = ? and 
                                                                pd.color_id = ? and 
                                                                pd.talla_id = ?',
                                                                [$id,
                                                                $producto->producto_id,$producto->color_id,$talla->talla_id]);

                                                                                 
                    //========== EN CASO EL PRODUCTO YA EXISTA EN EL DETALLE =======
                    if(count($producto_existe) === 1){
                        
                        //====== PREGUNTANDO SI TIENE CANTIDAD ATENDIDA ======
                        if($producto_existe[0]->cantidad_atendida > 0){

                            //======= LA NUEVA CANTIDAD DEBE SER MAYOR O IGUAL A LA CANTIDAD ATENDIDA =======
                            if( $talla->cantidad >= $producto_existe[0]->cantidad_atendida ){
                                
                                //============ ACTUALIZAR PRODUCTO EN LA BD ========
                                DB::table('pedidos_detalles')
                                ->where('pedido_id', $id)  
                                ->where('producto_id', $producto->producto_id)  
                                ->where('color_id', $producto->color_id)  
                                ->where('talla_id', $talla->talla_id)  
                                ->update([
                                    'cantidad'                  => $talla->cantidad,
                                    'cantidad_pendiente'        => $talla->cantidad - $producto_existe[0]->cantidad_atendida,
                                    'precio_unitario'           => $producto->precio_venta,
                                    'importe'                   => $importe,
                                    'porcentaje_descuento'      => floatval($producto->porcentaje_descuento),
                                    'precio_unitario_nuevo'     => floatval($precio_venta),
                                    'importe_nuevo'             => floatval($precio_venta) * floatval($talla->cantidad),
                                    'monto_descuento'           => floatval($importe) * floatval($producto->porcentaje_descuento) / 100,
                                ]);
                            }else{
                                throw new Exception($producto->producto_nombre.'-'.$producto->color_nombre.'-'.$talla->talla_nombre.
                                ', LA CANTIDAD NUEVA ('.$talla->cantidad.') DEBE SER MAYOR O IGUAL A LA CANTIDAD ATENDIDA'.'('.$producto_existe[0]->cantidad_atendida.')');
                            }
                        }
                    }

                    //========== EN CASO EL PRODUCTO SEA NUEVO EN EL DETALLE ========
                    if(count($producto_existe) === 0){
                        $pedido_detalle                         =   new PedidoDetalle();
                        $pedido_detalle->pedido_id              =   $pedido->id;
                        $pedido_detalle->producto_id            =   $producto->producto_id;
                        $pedido_detalle->color_id               =   $producto->color_id;
                        $pedido_detalle->talla_id               =   $talla->talla_id;
                        $pedido_detalle->producto_codigo        =   $producto->producto_codigo;
                        $pedido_detalle->producto_nombre        =   $producto->producto_nombre;
                        $pedido_detalle->color_nombre           =   $producto->color_nombre;
                        $pedido_detalle->talla_nombre           =   $talla->talla_nombre;
                        $pedido_detalle->modelo_nombre          =   $producto->modelo_nombre;
                        $pedido_detalle->cantidad               =   $talla->cantidad;
                        $pedido_detalle->cantidad_atendida      =   0;
                        $pedido_detalle->cantidad_pendiente     =   $talla->cantidad;
                        $pedido_detalle->precio_unitario        =   $producto->precio_venta;
                        $pedido_detalle->importe                =   $importe;
                        $pedido_detalle->porcentaje_descuento   =   floatval($producto->porcentaje_descuento);
                        $pedido_detalle->precio_unitario_nuevo  =   floatval($precio_venta);
                        $pedido_detalle->importe_nuevo          =   floatval($precio_venta) * floatval($talla->cantidad);
                        $pedido_detalle->monto_descuento        =   floatval($importe) * floatval($producto->porcentaje_descuento) / 100;
                        $pedido_detalle->save();
                    }
                }
            }

            //====== REGISTRO DE ACTIVIDAD ========
            $descripcion = "SE MODIFICÓ EL PEDIDO CON LA FECHA: ". Carbon::parse($pedido->fecha_registro)->format('d/m/y');
            $gestion = "PEDIDO";
            crearRegistro($pedido, $descripcion , $gestion);

            DB::commit();
            Session::flash('success','PEDIDO N°'.$id.'MODIFICADO CON ÉXITO');

            return response()->json(['success'=>true,'message'=>'PEDIDO N°'.$id.'MODIFICADO CON ÉXITO']);
        } catch (\Throwable  $th) {
            DB::rollback();

            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        $pedido_id  =   $id;

        try {
            //==== ANULANDO PEDIDO ======
            $pedido         =   Pedido::find($id);
            $pedido->estado =   'FINALIZADO';
            $pedido->update();

            return response()->json(['type'=>'success','pedido_id'=>$id]);
        } catch (\Throwable $th) {
            return response()->json(['type'=>'error','message'=>$th->getMessage()]);
        }
    }

    public function report($id)
    {
        $pedido             = Pedido::findOrFail($id);
        $tallas             = Talla::all();
        $igv                = '';
        $tipo_moneda        = '';
        $detalles           = PedidoDetalle::where('pedido_id',$id)->get();
        $empresa            = Empresa::where('id',$pedido->empresa_id)->get()[0];
        
        $paper_size         = array(0,0,360,360);

        $detalles           = $this->formatearArrayDetalle($detalles);

        $vendedor_nombre    = $pedido->user_nombre;


        $pdf = PDF::loadview('pedidos.pedido.reportes.detalle',[
            'pedido'            => $pedido,
            'detalles'          => $detalles,
            'empresa'           => $empresa,
            'tallas'            => $tallas,
            'vendedor_nombre'   => $vendedor_nombre
            ])->setPaper('a4')->setWarnings(false);
        return $pdf->stream('CO-'.$pedido->pedido_nro.'.pdf');

    }

    public function atender(Request $request){
        DB::beginTransaction();
        
        try {
            //======== OBTENIENDO ID DEL PEDIDO =========
            $pedido_id      =   $request->get('pedido_id');
            //========= OBTENIENDO EL DETALLE DEL PEDIDO =====
            $pedido         =   Pedido::find($pedido_id);
            $pedido_detalle =   PedidoDetalle::where('pedido_id',$pedido_id)->get();
            
            $atencion_detalle = [];            
            foreach ($pedido_detalle as  $pedido_item) {
                //======== OBTENIENDO EL STOCK DEL PRODUCTO =======
                $producto_stock =   DB::select('select pct.stock_logico from producto_color_tallas as pct
                                    where pct.producto_id=? and pct.color_id=? and pct.talla_id=?',
                                    [$pedido_item->producto_id,$pedido_item->color_id,$pedido_item->talla_id]);
                
                //======= EN CASO EL PRODUCTO COLOR TENGA ESA TALLA EN LA BD ========
                if(count($producto_stock) > 0){
                    $stock_logico                       =   $producto_stock[0]->stock_logico;
                    $cantidad_pendiente                 =   $pedido_item->cantidad_pendiente;
                    $cantidad_atendida                  =   $pedido_item->cantidad_atendida;
                    $cantidad_solicitada                =   $pedido_item->cantidad;
                    $stock_logico_actualizado           =   0;
                    $cantidad_atender                   =   0;


                    //===== SEPARANDO STOCK LOGICO ======
                    if($cantidad_pendiente > 0 && $stock_logico > 0){
                        $cantidad_atender   =   ($stock_logico >= $cantidad_pendiente)?$cantidad_pendiente:$stock_logico;

                        DB::table('producto_color_tallas')
                        ->where('producto_id', $pedido_item->producto_id) 
                        ->where('color_id', $pedido_item->color_id) 
                        ->where('talla_id', $pedido_item->talla_id) 
                        ->update([
                            'stock_logico' => DB::raw("stock_logico - $cantidad_atender") 
                        ]);

                        //====== OBTENIENDO NUEVO STOCK_LOGICO ======
                        $producto_stock_actualizado =   DB::select('select pct.stock_logico from producto_color_tallas as pct
                        where pct.producto_id=? and pct.color_id=? and pct.talla_id=?',
                        [$pedido_item->producto_id,$pedido_item->color_id,$pedido_item->talla_id]);

                        $stock_logico_actualizado   =   $producto_stock_actualizado[0]->stock_logico; 
                        
                    }

                    if($cantidad_pendiente == 0 || $stock_logico == 0){
                        $stock_logico_actualizado   =   $stock_logico;
                        $cantidad_atender           =   0;
                    }
               
                    //====== EXISTE EL PRODUCTO COLOR TALLA ======
                    $existe                     =   true;
                }

                //========= EN CASO EL PRODUCTO COLOR NO TENGA ESA TALLA EN BD ==========
                if(count($producto_stock) == 0){
                    $stock_logico                       =   0;
                    $cantidad_solicitada                =   $pedido_item->cantidad;
                    $cantidad_pendiente                 =   $pedido_item->cantidad_pendiente;
                    $cantidad_atendida                  =   $pedido_item->cantidad_atendida;
                    $stock_logico_actualizado           =   0;
                    $cantidad_atender                   =   0;

                    //====== EXISTE EL PRODUCTO COLOR TALLA ======
                    $existe                     =   false;
                }

                $atencion_item = (object)[
                    'modelo_nombre'         => $pedido_item->modelo_nombre,
                    'producto_id'           => $pedido_item->producto_id,
                    'producto_codigo'       => $pedido_item->producto_codigo,
                    'producto_nombre'       => $pedido_item->producto_nombre,
                    'color_id'              => $pedido_item->color_id,
                    'color_nombre'          => $pedido_item->color_nombre,
                    'talla_id'              => $pedido_item->talla_id,
                    'talla_nombre'          => $pedido_item->talla_nombre,
                    'precio_unitario'       => $pedido_item->precio_unitario,
                    'porcentaje_descuento'  => $pedido_item->porcentaje_descuento,
                    'precio_unitario_nuevo' => $pedido_item->precio_unitario_nuevo,
                    'stock_logico_actualizado' => $stock_logico_actualizado,
                    'stock_logico'          => $stock_logico,
                    'cantidad_solicitada'   => $cantidad_solicitada,
                    'cantidad_atendida'     => $cantidad_atendida,
                    'cantidad_pendiente'    => $cantidad_pendiente,
                    'cantidad'              => $cantidad_atender,
                    'existe'                => $existe,
                ];

                $atencion_detalle[]     =   $atencion_item;
            }


            $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
            $clientes           =   Cliente::where('estado', 'ACTIVO')->get();  
            $condiciones        =   Condicion::where('estado','ACTIVO')->get();
            $vendedor_actual    =   DB::select('select c.id from user_persona as up
                                    inner join colaboradores  as c
                                    on c.persona_id=up.persona_id
                                    where up.user_id = ?',[Auth::id()]);
            $vendedor_actual    =   $vendedor_actual?$vendedor_actual[0]->id:null;     
            $modelos            =   Modelo::where('estado','ACTIVO')->get();
            $tallas             =   Talla::where('estado','ACTIVO')->get();
            $tipos_ventas       =   tipos_venta();
            $tipoVentas         =   collect();

            foreach($tipos_ventas as $tipo){
                if(ifComprobanteSeleccionado($tipo->id) && ($tipo->tipo == 'VENTA' || $tipo->tipo == 'AMBOS')){
                    $tipoVentas->push([
                        "id"=>$tipo->id,
                        "nombre"=>$tipo->nombre,
                    ]);
                }
            }

            //======= DE ACUERDO AL TIPO CLIENTE ; LIMITAR LOS TIPOS DE VENTAS =====
            //===== SI ES CLIENTE CON DNI, QUITAMOS LA FACTURA ======
            $tipo_doc_cliente   =   $pedido->cliente->tipo_documento;
            if($tipo_doc_cliente === 'DNI'){
                $tipoVentas = $tipoVentas->reject(function ($tipoVenta){
                    return $tipoVenta['id'] == 127;
                });
            }

            //====== SI EL CLIENTE TIENE RUC, QUITAMOS LA BOLETA =======
            if($tipo_doc_cliente === 'RUC'){
                $tipoVentas = $tipoVentas->reject(function ($tipoVenta){
                    return $tipoVenta['id'] == 128;
                });
            }

            //====== SI EL CLIENTE NO TIENE DNI NI RUC ========
            //======= PERMITIR SOLO NOTAS DE VENTA =======
            if($tipo_doc_cliente !== 'RUC' && $tipo_doc_cliente !== 'DNI'){
                $tipoVentas = $tipoVentas->reject(function ($tipoVenta){
                    return $tipoVenta['id'] == 127;
                });
                $tipoVentas = $tipoVentas->reject(function ($tipoVenta){
                    return $tipoVenta['id'] == 128;
                });
            }

            //======= SI EL PEDIDO YA FUE FACTURADO, PERMITIR SOLO ATENDER CON NOTAS DE VENTA ======
            if($pedido->facturado === 'SI'){
                $tipoVentas = $tipoVentas->reject(function ($tipoVenta){
                    return $tipoVenta['id'] == 127;
                });
                $tipoVentas = $tipoVentas->reject(function ($tipoVenta){
                    return $tipoVenta['id'] == 128;
                });


                $doc_venta  =   DB::select('select * from cotizacion_documento as cd
                                where cd.pedido_id = ? and cd.tipo_doc_venta_pedido = "FACTURACION"',
                                [$pedido->id]);

                if(count($doc_venta) !== 1){
                    throw new Exception('NO SE ENCUENTRA EL DOC DE VENTA CON EL QUE SE FACTURÓ EL PEDIDO');
                }

                Session::flash('pedido_facturado_atender',
                'EL PEDIDO SOLO PODRÁ ATENDERSE CON NOTAS DE VENTA,PORQUE YA FUE FACTURADO CON EL DOC: '
                .$doc_venta[0]->serie.'-'.$doc_venta[0]->correlativo);
            }

            $departamentos = departamentos();
        
            DB::commit();
            return view('pedidos.pedido.atender',compact('atencion_detalle','empresas','clientes','pedido_detalle',
                                                'vendedor_actual','condiciones',
                                                'modelos','tallas','pedido','tipoVentas','departamentos'));
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th->getMessage());
        }
    }

   

    public function getAtenciones($pedido_id){
        $pedido_atenciones    =   DB::select('select cd.serie as documento_serie,cd.correlativo as documento_correlativo, 
                                cd.created_at as fecha_atencion ,CONCAT(p.nombres, " ", p.apellido_paterno, " ", p.apellido_materno) AS documento_usuario,
                                cd.monto_envio as documento_monto_envio, cd.monto_embalaje as documento_monto_embalaje,
                                cd.total_pagar as documento_total_pagar,cd.pedido_id,cd.id as documento_id
                                from  cotizacion_documento as cd  
                                inner join user_persona as up on cd.user_id = up.user_id
                                inner join personas as p  on p.id = up.persona_id
                                where cd.pedido_id=? and cd.tipo_doc_venta_pedido = "ATENCION" ',[$pedido_id]);

        
        return  response()->json(['type'=>'success','pedido_atenciones'=>$pedido_atenciones]);
    }

    public function getAtencionDetalles($pedido_id,$documento_id){

        $atencion_detalles    =   DB::select('select cdd.nombre_producto as producto_nombre,
                                cdd.nombre_color as color_nombre, cdd.nombre_talla as talla_nombre,
                                cdd.cantidad
                                from cotizacion_documento as cd
                                inner join cotizacion_documento_detalles  as cdd on cdd.documento_id = cd.id   
                                where cd.pedido_id=? and cd.id = ?',[$pedido_id,$documento_id]);

        
        return  response()->json(['type'=>'success','atencion_detalles'=>$atencion_detalles]);
    }

    public function formatearArrayDetalle($detalles){
        $detalleFormateado=[];
        $productosProcesados = [];
        foreach ($detalles as $detalle) {
            $cod   =   $detalle->producto_id.'-'.$detalle->color_id;
            if (!in_array($cod, $productosProcesados)) {
                $producto=[];
                //======== obteniendo todas las detalle talla de ese producto_color =================
                $producto_color_tallas = $detalles->filter(function ($detalleFiltro) use ($detalle) {
                    return $detalleFiltro->producto_id == $detalle->producto_id && $detalleFiltro->color_id == $detalle->color_id;
                });
                
                $producto['producto_codigo']        =   $detalle->producto_codigo;
                $producto['producto_id']            =   $detalle->producto_id;
                $producto['color_id']               =   $detalle->color_id;
                $producto['producto_nombre']        =   $detalle->producto_nombre;
                $producto['color_nombre']           =   $detalle->color_nombre;
                $producto['modelo_nombre']          =   $detalle->modelo_nombre;
                $producto['precio_unitario']        =   $detalle->precio_unitario;
                $producto['porcentaje_descuento']   =   $detalle->porcentaje_descuento;
                $producto['precio_unitario_nuevo']  =   $detalle->precio_unitario_nuevo;

                $tallas             =   [];
                $subtotal           =   0.0;
                $subtotal_with_desc =   0.0;
                $cantidadTotal=0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla=[];
                    $talla['talla_id']              =   $producto_color_talla->talla_id;

                    
                    $talla['cantidad']              =   $producto_color_talla->cantidad;
                    $subtotal                       +=  $talla['cantidad']*$producto['precio_unitario_nuevo'];
                    $cantidadTotal                  +=  $talla['cantidad'];
                   

                    $talla['talla_nombre']          =   $producto_color_talla->talla_nombre;
                    
                   array_push($tallas,$talla);
                }
                
                $producto['tallas']                 =   $tallas;
                $producto['subtotal']               =   $subtotal;
                $producto['cantidad_total']         =   $cantidadTotal;
                array_push($detalleFormateado,$producto);
                $productosProcesados[] = $detalle->producto_id.'-'.$detalle->color_id;
            }
        }
        return $detalleFormateado;
    }

    public function getColoresTallas($producto_id){
       
        try {
            $precios_venta  =   DB::select('SELECT 
                                p.id AS producto_id,
                                p.nombre AS producto_nombre,
                                p.precio_venta_1,
                                p.precio_venta_2,
                                p.precio_venta_3
                                FROM 
                                    productos AS p 
                                WHERE 
                                    p.id = ? AND p.estado = "ACTIVO" ',[$producto_id]);  

           
            $colores =  DB::select('SELECT 
                                    p.id AS producto_id,
                                    p.nombre AS producto_nombre,
                                    c.id AS color_id,
                                    c.descripcion AS color_nombre,
                                    p.codigo as producto_codigo
                                FROM 
                                    producto_colores AS pc 
                                    inner join productos as p on p.id = pc.producto_id
                                    inner join colores as c on c.id = pc.color_id
                                WHERE 
                                    pc.producto_id = ? 
                                    AND p.estado = "ACTIVO" and c.estado = "ACTIVO" ',[$producto_id]);

            $stocks =   DB::select('select  pct.producto_id,pct.color_id,pct.talla_id,
                        pct.stock,pct.stock_logico, t.descripcion as talla_nombre
                        from producto_color_tallas as pct
                        inner join productos as p on p.id = pct.producto_id
                        inner join colores as c on c.id = pct.color_id 
                        inner join tallas as t on t.id = pct.talla_id
                        where p.estado = "ACTIVO" and c.estado = "ACTIVO" and t.estado = "ACTIVO"
                        and p.id = ?',[$producto_id]);

            $tallas =   Talla::where('estado','ACTIVO')->orderBy('id')->get();   

            $producto_color_tallas  =   null;
            if(count($colores) > 0){
                $producto_color_tallas  =   $this->formatearColoresTallas($colores,$stocks,$precios_venta,$tallas);
            }

          
            
            return response()->json(['success' => true,'producto_color_tallas'=>$producto_color_tallas]);
        } catch (\Throwable $th) {
    
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function formatearColoresTallas($colores, $stocks, $precios_venta, $tallas)
    {
        
        $producto = [];

        // Verifica si $colores no está vacío
        if (count($colores) > 0) {
            $producto['id']     = $colores[0]->producto_id;
            $producto['nombre'] = $colores[0]->producto_nombre;
            $producto['codigo'] = $colores[0]->producto_codigo;
        } else {
            // Maneja el caso cuando $colores está vacío
            $producto['id']     = null;
            $producto['nombre'] = null;
            $producto['codigo'] = null;

        }

        // Verifica si $precios_venta no está vacío
        if (count($precios_venta) > 0) {
            $producto['precio_venta_1'] = $precios_venta[0]->precio_venta_1;
            $producto['precio_venta_2'] = $precios_venta[0]->precio_venta_2;
            $producto['precio_venta_3'] = $precios_venta[0]->precio_venta_3;
        } else {
            // Maneja el caso cuando $precios_venta está vacío
            $producto['precio_venta_1'] = null;
            $producto['precio_venta_2'] = null;
            $producto['precio_venta_3'] = null;
        }

        $lstColores = [];

        //======== RECORRIENDO COLORES =======
        foreach ($colores as $color) {
            $item_color = [];
            $item_color['id']       =   $color->color_id;
            $item_color['nombre']   =   $color->color_nombre;

            //======== OBTENIENDO TALLAS DEL COLOR =======
            $lstTallas = [];

            foreach ($tallas as $talla) {
                $item_talla = [];
                $item_talla['id'] = $talla->id;
                $item_talla['nombre'] = $talla->descripcion;

                // Filtrar stocks para color y talla actuales
                $stock_filtrado = array_filter($stocks, function ($stock) use ($producto, $color, $talla) {
                    return $stock->producto_id == $producto['id'] &&
                        $stock->color_id == $color->color_id &&
                        $stock->talla_id == $talla->id;
                });

                // Asignar stock y stock lógico si existe, o establecer en 0
                if (!empty($stock_filtrado)) {
                    $first_stock = reset($stock_filtrado); // Obtiene el primer elemento del array filtrado
                    $item_talla['stock'] = $first_stock->stock;
                    $item_talla['stock_logico'] = $first_stock->stock_logico;
                } else {
                    $item_talla['stock'] = 0;
                    $item_talla['stock_logico'] = 0;
                }

                $lstTallas[] = $item_talla;
            }

            $item_color['tallas'] = $lstTallas;
            $lstColores[] = $item_color;
        }

        $producto['colores'] = $lstColores;

        return $producto;
    }


    public function getProductosByModelo($modelo_id){
        try {
            $productos  =   DB::select('select p.id,p.nombre 
                            from productos as p
                            where p.modelo_id = ? and p.estado = "ACTIVO"',[$modelo_id]);

            return response()->json(['success' => true,'productos'=>$productos]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    // public function getProductosByModelo($modelo_id){
    //     try {
    //         $productos  =   DB::select('select distinct p.id as producto_id,c.id as color_id, t.id as talla_id,
    //         m.id as modelo_id, p.nombre as producto_nombre,c.descripcion as color_nombre, 
    //         t.descripcion as talla_nombre,pct.stock_logico,m.descripcion as modelo_nombre,p.codigo as producto_codigo,
    //         p.precio_venta_1,p.precio_venta_2,p.precio_venta_3 
    //         from producto_colores as pc 
    //         left join producto_color_tallas as pct on (pc.producto_id=pct.producto_id and pc.color_id=pct.color_id) 
    //         inner join productos as p on p.id=pc.producto_id 
    //         inner join colores as c on c.id=pc.color_id 
    //         inner join modelos as m on m.id=p.modelo_id
    //         left join tallas as t on t.id=pct.talla_id 
    //         where m.id=?
    //         order by p.nombre,c.descripcion',[$modelo_id]);    

    //        $productos_formateado    =   $this->formatearListado($productos);
            
    //         return response()->json(['type'=>'success','message'=>$productos_formateado]);

    //     } catch (\Throwable $e) {
    //         return response()->json(['type'=>'error','message'=>$e->getMessage()]);
    //     }
    // }

    public function formatearListado($productos){
        $productos_formateado       =   [];
        $producto_color_procesados  =   [];
        $llave                      =   '';

        $productos_procesados       =   [];
        $llave_2                    =   '';


        //====== FORMATEANDO =====
        foreach ($productos as $producto) {
            $llave      =   $producto->producto_id.'-'.$producto->color_id;
            $llave_2    =   $producto->producto_id;
            if (!in_array($llave, $producto_color_procesados)) {
                $producto_color =   [];
                $producto_color['producto_id']          =   $producto->producto_id;
                $producto_color['color_id']             =   $producto->color_id;
                $producto_color['producto_nombre']      =   $producto->producto_nombre;
                $producto_color['producto_codigo']      =   $producto->producto_codigo;
                $producto_color['color_nombre']         =   $producto->color_nombre;
                $producto_color['modelo_nombre']        =   $producto->modelo_nombre;
                $producto_color['porcentaje_descuento'] =   0;
                $producto_color['precio_venta_1']       =   $producto->precio_venta_1;
                $producto_color['precio_venta_2']       =   $producto->precio_venta_2;
                $producto_color['precio_venta_3']       =   $producto->precio_venta_3;


                //==== OBTENIENDO LAS TALLAS ====
                $tallas = array_filter($productos, function($p) use ($producto) {
                    return $p->producto_id == $producto->producto_id && $p->color_id == $producto->color_id;
                });

                $producto_color_tallas = [];
                foreach ($tallas as $talla) {
                    //====== CONSTRUYENDO TALLA =====
                    $producto_color_talla                       =   [];
                    $producto_color_talla['talla_id']           =   $talla->talla_id;
                    $producto_color_talla['talla_nombre']       =   $talla->talla_nombre;
                    $producto_color_talla['stock_logico']       =   $talla->stock_logico;   
                    $producto_color_talla['precio_unitario']    =   0;   

                    
                    //====== GUARDANDO TALLA DEL PRODUCTO COLOR ====
                    $producto_color_tallas[]   =   $producto_color_talla;
                }
                $producto_color['tallas']   =   $producto_color_tallas;
                if(!in_array($llave_2, $productos_procesados)){
                    $producto_color['print_precios']   =   true;
                }else{
                    $producto_color['print_precios']   =   false;
                }
                
                $productos_formateado[]         =   $producto_color;
                $producto_color_procesados[]    =   $llave;
            }
            $productos_procesados[]    =   $llave_2;
        }

        return $productos_formateado;
    }

    public function validarCantidadAtender(Request $request){
        DB::beginTransaction();
        try {
            $data   =   $request->all();
          
            $cantidad_atender_nueva         =   $request->get('cantidad_atender_nueva');
            $cantidad_atender_anterior      =   $request->get('cantidad_atender_anterior');
    
            $producto_id        =   $request->get('producto_id');
            $color_id           =   $request->get('color_id');
            $talla_id           =   $request->get('talla_id');
    
            //======== OBTENER PRODUCTO ======
            $producto   =   DB::select('select pct.stock_logico from producto_color_tallas as pct
                            where pct.producto_id=? and pct.color_id=? and pct.talla_id=?',
                            [$producto_id,$color_id,$talla_id]);
            
            if(count($producto)>0){
                $stock_logico   =   $producto[0]->stock_logico;
    
                //======= SI LA NUEVA CANTIDAD ATENDER ES MENOR IGUAL AL STOCK LOGICO REPUESTO ======
                $stock_logico_repuesto  =   $stock_logico + $cantidad_atender_anterior;   
                if($cantidad_atender_nueva <= $stock_logico_repuesto){
                    //======== ACTUALIZAR STOCK_LOGICO ======
                    DB::table('producto_color_tallas')
                    ->where('producto_id', $producto_id)
                    ->where('color_id', $color_id)
                    ->where('talla_id', $talla_id)
                    ->update([
                        'stock_logico' => DB::raw('stock_logico + ' . ($cantidad_atender_anterior - $cantidad_atender_nueva)),
                    ]);

                    DB::commit();
                    return response()->json(['type'=>'success','data'=>$data,'message'=>'Stock lógico actualizado']);
                }else{
                    return response()->json(['type'=>'error','data'=>$data,'message'=>'Stock lógico ('.$stock_logico_repuesto .') es menor 
                    a la cantidad que se quiere atender ('.$cantidad_atender_nueva.')']);
                }
            }else{
                return response()->json(['type'=>'error','data'=>$data,'message'=>'El producto no existe']);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['type'=>'error','data'=>$th->getMessage(),'message'=>'Error en el servidor']);
        }
    }

public function generarDocumentoVenta(Request $request){
       
    DB::beginTransaction();

    try {

        $pedido_id              =   $request->get('pedido_id');
        
        $pedido                 =   Pedido::find($pedido_id);

        if($pedido->facturado === 'SI'){
            //====== SI EL PEDIDO YA FUE FACTURADO, EVITAMOS QUE SE CONTABILIZE LA ATENCIÓN EN LA CAJA ========
            $additionalData = [
                'facturado'     =>  'SI',
            ];

            $request->merge($additionalData);
        }
       
        $documentoController    =   new DocumentoController();
        $res                    =   $documentoController->store($request);
        $jsonResponse           =   $res->getData(); 

        //====== MANEJO DE RESPUESTA =========
        $success_store_doc                =   $jsonResponse->success; 

        //===== EN CASO SE HAYA GENERADO EL DOC DE VENTA EXITOSAMENTE ======
        if($success_store_doc){
            $documento_id       =   $jsonResponse->documento_id;
            
            
            //======= ACTUALIZANDO CANTIDAD ATENDIDA EN PEDIDO DETALLES ======
            $productosJSON  = $request->get('productos_tabla');
            $productos      = json_decode($productosJSON);

            foreach ($productos as $producto) {
                DB::table('pedidos_detalles')
                    ->where('pedido_id', $pedido_id)
                    ->where('producto_id', $producto->producto_id)
                    ->where('color_id', $producto->color_id)
                    ->where('talla_id', $producto->talla_id)
                    ->update([
                        'cantidad_pendiente'    => DB::raw('cantidad_pendiente  - ' . $producto->cantidad),
                        'cantidad_atendida'     => DB::raw('cantidad_atendida   + ' . $producto->cantidad)
                    ]);
            }

            //======= GRABANDO ATENCIÓN =====
            DB::table('cotizacion_documento')
            ->where('id', $documento_id)
            ->update([
                'pedido_id'             =>  $pedido_id,
                'tipo_doc_venta_pedido' =>  "ATENCION",
                'updated_at' => Carbon::now()
            ]);

    
            /*$pedido_atencion                    =   new PedidoAtencion();
            $pedido_atencion->pedido_id         =   $pedido_id;
            $pedido_atencion->documento_id      =   $documento_id;
            $pedido_atencion->fecha_atencion    =   Carbon::now()->format('Y-m-d');
            $pedido_atencion->save();*/



            //======= CAMBIANDO ESTADO DEL PEDIDO =====
            //===== CANTIDAD DE ITEMS QUE TIENE EL PEDIDO ======
            $cant_items_pedido                  =   PedidoDetalle::where('pedido_id',$pedido_id)->count('*');
            $cant_items_pendientes_pedido       =   PedidoDetalle::where('pedido_id',$pedido_id)
                                                        ->where('cantidad_pendiente','>',0)
                                                        ->count('*');
            $cant_items_atendidos_pedido        =   PedidoDetalle::where('pedido_id',$pedido_id)
                                                        ->where('cantidad_atendida','>',0)
                                                        ->count('*');

            $pedido_actualizar                  =   Pedido::find($pedido_id);

            if($cant_items_pendientes_pedido === 0){
                $pedido_actualizar->estado      =   "FINALIZADO";
            }
                
            if($cant_items_pendientes_pedido > 0 && $cant_items_atendidos_pedido > 0){
                $pedido_actualizar->estado      =   "ATENDIENDO";
            }

            if($cant_items_atendidos_pedido === 0){
                $pedido_actualizar->estado      =   "PENDIENTE";
            }

            //======== VERIFICANDO SI EL PEDIDO ESTÁ FACTURADO ======
            if($pedido_actualizar->facturado === "SI"){

                //===== SI EL SALDO FACTURADO ES MAYOR O IGUAL AL MONTO DE LA ATENCIÓN =====
                if($pedido_actualizar->saldo_facturado >= $request->get('monto_total_pagar')){
                    //====== NO GENERAR RECIBOS DE CAJA =======
                    //====== DISMINUIR SALDO FACTURADO ========
                    $pedido_actualizar->saldo_facturado -=  $request->get('monto_total_pagar'); 
                }else{
                    //===== SI EL SALDO FACTURADO ES MENOR AL MONTO DE LA ATENCIÓN ======
                
                    //====== OBTENER EXCEDENTE =======
                       $excedente  =   $request->get('monto_total_pagar') -    $pedido_actualizar->saldo_facturado;

                    //======= OBTENIENDO MOVIMIENTO ID DEL USUARIO ======
                    $movimiento = DB::select("select dmc.movimiento_id from detalles_movimiento_caja as dmc
                                        where dmc.usuario_id = ? and dmc.fecha_salida is null",
                                        [$pedido_actualizar->user_id]);

                    if(count($movimiento) !== 1){
                        throw new Exception("ERROR AL OBTENER EL MOVIMIENTO DE CAJA DEL USUARIO DEL PEDIDO");
                    }

                    $documento_venta    = Documento::find($documento_id);

                    if(!$documento_venta){
                        throw new Exception("ERROR AL OBTENER EL DOCUMENTO DE VENTA DE LA ATENCIÓN PARA GENERAR EL RECIBO DE CAJA EXCEDENTE");
                    }

                        
                    //====== GENERAR RECIBO DE CAJA DEL EXCEDENTE =====
                    $recibo_caja                    =   new ReciboCaja();
                    $recibo_caja->movimiento_id     =   $movimiento[0]->movimiento_id;
                    $recibo_caja->user_id           =   $pedido_actualizar->user_id;
                    $recibo_caja->cliente_id        =   $pedido_actualizar->cliente_id;
                    $recibo_caja->monto             =   $excedente;
                    $recibo_caja->saldo             =   0;
                    $recibo_caja->metodo_pago       =   "EFECTIVO";
                    $recibo_caja->estado_servicio   =   "CANJEADO";
                     $recibo_caja->observacion   =   "CREADO A PARTIR DEL EXCEDENTE DE "."S/.".$excedente .
                                                        " PRESENTE EN LA ATENCIÓN ".$documento_venta->serie.'-'.$documento_venta->correlativo.
                                                        " DEL PEDIDO PE-".$pedido_actualizar->pedido_nro;            
                    $recibo_caja->save();
                        
                    //====== DISMINUIR SALDO FACTURADO ========
                    $pedido_actualizar->saldo_facturado =  0; 
                }
            }
                
            $pedido_actualizar->save();
               

            DB::commit();
            return response()->json([
                'success'       => true,
                'documento_id'  => $documento_id,
                'mensaje'       =>   'DOCUMENTO DE VENTA GENERADO - PEDIDO ACTUALIZADO'
            ]);
           
        }else{
            DB::rollBack();
            return response()->json([
                'success'       =>      false,
                'mensaje'       =>      $jsonResponse->mensaje,
                'excepcion'     =>      $jsonResponse->excepcion
            ]);            
        }  
    } catch (\Throwable $th) {
        //======== REVERTIR ACCIONES REALIZADAS EN PEDIDO CONTROLLER ======
        DB::rollBack();

        //===== DEVOLVER STOCKS =======
        $detalles_documento_venta    =   Detalle::where('documento_id',$documento_id)->get();

        foreach ($detalles_documento_venta as $item) {
            DB::table('producto_color_tallas')
                ->where('producto_id', $item->producto_id)
                ->where('color_id', $item->color_id)
                ->where('talla_id', $item->talla_id)
                ->update([
                    'stock'    => DB::raw('stock  + ' . $item->cantidad),
                ]);
        }
       
        //====== ELIMINAR DOC DE VENTA ====
        DB::table('cotizacion_documento')
            ->where('id', $documento_id)
            ->delete();

            return response()->json([
                'success'   => false,
                'mensaje'   => 'ERROR EN EL SERVIDOR, SI EL ERROR PERSISTE COMUNICARSE CON EL ADMINISTRADOR DEL SISTEMA', 
                'excepcion' => $th->getMessage(),
            ]);
    }
 }

    public function validarTipoVenta($comprobante_id){
        try {
            $estado = DB::table('empresa_numeracion_facturaciones')
            ->where('tipo_comprobante', $comprobante_id)
            ->where('estado', 'ACTIVO')
            ->exists();

            $message = "";
            if($estado){
                $message    =   "TIPO DE COMPROBANTE ACTIVO EN LA EMPRESA";
            }else{
                $message    =   "TIPO DE COMPROBANTE NO ESTÁ ACTIVO EN LA EMPRESA";
            }

            return response()->json(['type'=>'success','estado'=>$estado,'message'=>$message]);
        } catch (\Throwable $th) {
            return response()->json(['type'=>'error','message'=>'ERROR EN EL SERVIDOR','exception'=>$th->getMessage()]);
        }
    }

    public function devolverStockLogico(Request $request){
        $productos  =  json_decode($request->get('carrito'));

        foreach ($productos as $producto) {
            foreach ($producto->tallas as $talla) {
                if($talla->existe && $talla->cantidad_atender > 0){

                    DB::update('UPDATE producto_color_tallas 
                    SET stock_logico = stock_logico + ? 
                    WHERE producto_id = ? and color_id=? and talla_id=?', 
                    [$talla->cantidad_atender, $producto->producto_id,$producto->color_id,$talla->talla_id]);
    
                }
            }
        }
    }
    
    public function getPedidoDetalles($pedido_id){

        try {
            $pedido_detalles    =   PedidoDetalle::where('pedido_id',$pedido_id)->get();
            
            return response()->json(['type'=>'success','pedido_detalles'=>$pedido_detalles]);
        } catch (\Throwable $th) {
            return response()->json(['type'=>'error','exception'=>$th->getMessage(),'message'=>'ERROR EN EL SERVIDOR']);
        }

    }


    public function getExcel($fecha_inicio=null,$fecha_fin=null,$estado=null){
        $fecha_inicio   =   $fecha_inicio=="null"?null:$fecha_inicio;
        $fecha_fin      =   $fecha_fin=="null"?null:$fecha_fin;
        $estado         =   $estado=="null"?null:$estado;


        return  Excel::download(new PedidosExport($fecha_inicio,$fecha_fin,$estado), 'REPORTE-PEDIDOS'.'.xlsx');
    }

    public function facturar(Request $request){

        DB::beginTransaction();
        try {
            //====== RECIBIENDO PEDIDO ID =====
            $pedido_id      =   $request->get('pedido_id');
            $pedido         =   Pedido::find($pedido_id);
            
            if(!$pedido){
                throw new \Exception('NO SE ENCONTRÓ EL PEDIDO EN LA BASE DE DATOS');
            }

            //===== OBTENIENDO EL TIPO DOC DEL CLIENTE =======
            $cliente    =   DB::select('select c.tipo_documento from clientes as c
                            where c.id = ?',[$pedido->cliente_id]);
            
            if(count($cliente) === 0 || count($cliente) > 1){
                throw new \Exception('NO SE ENCONTRÓ EL CLIENTE EN LA BASE DE DATOS');
            }

            $cliente_tipo_documento =   $cliente[0]->tipo_documento;
            $tipo_venta             =   null;
            if($cliente_tipo_documento === "RUC"){
                $tipo_venta = 127;
            }
            if($cliente_tipo_documento === "DNI" ){
                $tipo_venta = 128;
            }

            if($cliente_tipo_documento !== "RUC" && $cliente_tipo_documento !== "DNI"){
                throw new Exception("SE REQUIERE DNI O RUC PARA FACTURAR UN PEDIDO");
            }
            
            //======= OBTENIENDO DETALLE DEL PEDIDO ===========
            $detalle_pedido =   DB::select('select * from pedidos_detalles as pd
                                where pd.pedido_id = ?',[$pedido_id]);

            $productos  =   [];
            foreach($detalle_pedido as $item){
                $producto = (object)[
                    'producto_id'           =>  $item->producto_id,
                    'color_id'              =>  $item->color_id,
                    'talla_id'              =>  $item->talla_id,
                    'cantidad'              =>  $item->cantidad,
                    'precio_unitario'       =>  $item->precio_unitario,
                    'porcentaje_descuento'  =>  $item->porcentaje_descuento,
                    'precio_unitario_nuevo' =>  $item->precio_unitario_nuevo
                ];

                $productos[]    =   $producto;
            }

            $productos = json_encode($productos);
           
            //======= AGREGANDO DATOS AL REQUEST =====
            $additionalData = [
                'fecha_documento_campo'     =>  $pedido->fecha_registro,
                'empresa'                   =>  $pedido->empresa_id,
                'fecha_atencion_campo'      =>  $pedido->fecha_registro,
                'tipo_venta'                =>  $tipo_venta,
                'condicion_id'              =>  $pedido->condicion_id,
                'fecha_vencimiento_campo'   =>  $pedido->fecha_registro,
                'cliente_id'                =>  $pedido->cliente_id,
                'igv'                       =>  "18",
                "igv_check"                 =>  "on",
                "efectivo"                  =>  "0",
                "importe"                   =>  "0",
                "empresa_id"                =>  $pedido->empresa_id,
                "monto_sub_total"           =>  $pedido->sub_total,
                "monto_embalaje"            =>  $pedido->monto_embalaje,
                "monto_envio"               =>  $pedido->monto_envio,
                "monto_total_igv"           =>  $pedido->total_igv,
                "monto_descuento"           =>  $pedido->monto_descuento,
                "monto_total"               =>  $pedido->total,
                "monto_total_pagar"         =>  $pedido->total_pagar,
                "data_envio"                =>  null,
                "facturar"                  =>  true,
                "productos_tabla"           =>  $productos
            ];

            $request->merge($additionalData);
            
            //======== GENERANDO DOC VENTA ======
            $documentoController    =   new DocumentoController();
            $res                    =   $documentoController->store($request);
            $jsonResponse           =   $res->getData(); 
            

            //====== MANEJO DE RESPUESTA =========
            $success_store_doc      =   $jsonResponse->success; 
        
            if($success_store_doc){
                
                $doc_venta  =   DB::select('select cd.serie,cd.correlativo,cd.total_pagar 
                                from cotizacion_documento as cd
                                where cd.id = ?',[$jsonResponse->documento_id])[0];

                //======== ACTUALIZANDO PEDIDO =======
                $pedido->facturado = 'SI';
                $pedido->documento_venta_facturacion_id             =   $jsonResponse->documento_id;
                $pedido->documento_venta_facturacion_serie          =   $doc_venta->serie;
                $pedido->documento_venta_facturacion_correlativo    =   $doc_venta->correlativo;
                $pedido->monto_facturado                =   $doc_venta->total_pagar;
                $pedido->saldo_facturado                =   $doc_venta->total_pagar;
                $pedido->save();

                //====== ACTUALIZANDO DOC VENTA ======
                DB::table('cotizacion_documento')
                ->where('id', $jsonResponse->documento_id)
                ->update([
                    'pedido_id'                 =>  $pedido_id,
                    'tipo_doc_venta_pedido'     => "FACTURACION",
                    'updated_at' => Carbon::now() 
                ]);

                DB::commit();

                return response()->json(
                [   'success'=>true,
                    'message'=>'SE HA GENERADO EL DOCUMENTO DE VENTA '.$doc_venta->serie.'-'.$doc_venta->correlativo,
                    'documento_id'  => $jsonResponse->documento_id]);
            }else{
                
                DB::rollBack();
                return response()->json(['success'=>false,'message'=>$jsonResponse->mensaje]);

            }

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getCliente($pedido_id){
        
        try {
            $pedido     =   Pedido::find($pedido_id);

            $cliente    =   DB::select('select c.* from clientes as c
                            where c.id = ?',[$pedido->cliente_id]);
            
            
            if(count($cliente) !== 1){
                throw new \Exception('CLIENTE NO ENCONTRADO');
            }
            
            return response()->json(['success' => true,'cliente'=>$cliente[0]]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false,'message'=>$th->getMessage()]);
        }
    }

    public function validarCantidadAtendida(Request $request){
        try {
            
            $lstProductos           =   json_decode($request->get('lstProductos'));
            $lstProductosValidados  =   [];
            $lstErroresValidacion   =   [];

            foreach ($lstProductos as $producto) {

                //========= OBTENIENDO CANTIDAD NUEVA ======
                $cantidad_nueva =   $producto->cantidad;
                //======== OBTENIENDO LA CANTIDAD ATENDIDA DEL PRODUCTO EN TIEMPO REAL =========
                $producto_en_detalle    =   DB::select('select pd.cantidad_atendida,pd.cantidad_pendiente,
                                            pd.producto_id,pd.color_id,pd.talla_id
                                            from pedidos_detalles as pd
                                            where pd.pedido_id = ? and 
                                            pd.producto_id = ? and
                                            pd.color_id = ? and
                                            pd.talla_id = ?',
                                            [$request->get('pedido_id'),
                                            $producto->producto_id,$producto->color_id,$producto->talla_id]);
                
                //======== EN CASO EL PRODUCTO EXISTA EN EL DETALLE PREVIAMENTE ======
                if(count($producto_en_detalle) === 1){

                    //======= VALIDAR CANTIDAD NUEVA CON LA CANTIDAD ATENDIDA =======
                    //======= LA CANTIDAD NUEVA DEBE SER MAYOR O IGUAL A LA CANTIDAD ATENDIDA ======
                    if($cantidad_nueva < $producto_en_detalle[0]->cantidad_atendida){

                        $mensaje    =   $producto->producto_nombre."-".$producto->color_nombre."-".$producto->talla_nombre.
                                        ", CANT NUEVA(".$cantidad_nueva.") DEBE SER MAYOR O IGUAL A CANT ATEND(".$producto_en_detalle[0]->cantidad_atendida.").";
                        $producto->validacion           =   false;
                        //$producto->mensaje_validacion   =   $mensaje;  
                        $lstErroresValidacion[]         =   (object)['producto_id'=>$producto->producto_id,
                                                                    'color_id'=>$producto->color_id,
                                                                    'talla_id'=>$producto->talla_id,
                                                                    'mensaje'=>$mensaje];

                    }else{

                        $producto->validacion           =   true;
                        $producto->mensaje_validacion   =   '';

                    }

                    $lstProductosValidados[]    =   $producto;
                }

                //========= EN CASO EL PRODUCTO SEA NUEVO =======
                if(count($producto_en_detalle) === 0){

                    if($producto->cantidad > 0){
                        $producto->validacion           =   true;
                        $lstProductosValidados[]    =   $producto;
                    }
                    
                }
                
            }

            return response()->json(['success'=>true,
            'lstProductosValidados'=>$lstProductosValidados,
            'lstErroresValidacion'=>$lstErroresValidacion]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
