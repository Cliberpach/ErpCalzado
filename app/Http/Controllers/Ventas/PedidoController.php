<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
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



class PedidoController extends Controller
{
    public function index(){
        return view('ventas.pedidos.index');
    }

    public function getTable(Request $request){
        $fecha_inicio   =   $request->get('fecha_inicio');
        $fecha_fin      =   $request->get('fecha_fin');

        $pedidos = Pedido::where('estado', '!=', 'ANULADO');

        if($fecha_inicio){
            $pedidos    =   $pedidos->where('fecha_registro', '>=', $fecha_inicio);
        }

        if($fecha_fin){
            $pedidos    =   $pedidos->where('fecha_registro', '<=', $fecha_fin);
        }
        
        return response()->json(['message'=>$pedidos->get()]);
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
            $monto_subtotal     =   0.0;
            $monto_embalaje     =   $request->get('monto_embalaje')??0;
            $monto_envio        =   $request->get('monto_envio')??0;
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
    
            $monto_total_pagar      =   $monto_subtotal+$monto_embalaje+$monto_envio;
            $monto_total            =   $monto_total_pagar/1.18;
            $monto_igv              =   $monto_total_pagar-$monto_total;
            $porcentaje_descuento   =   ($monto_descuento*100)/($monto_total_pagar);


            //======== REGISTRANDO PEDIDO =========
            $pedido = new Pedido();
            $pedido->cliente_id         = $request->get('cliente');

            //======== BUSCANDO NOMBRE DEL CLIENTE =====//
            $cliente    =   DB::select('select c.id,c.nombre from clientes as c
                            where c.id=?',[$request->get('cliente')]);
            
            $pedido->cliente_nombre     =   $cliente[0]->nombre;
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
            $pedido->moneda             = 1;
            $pedido->fecha_registro     = $request->get('fecha_documento');

            $pedido->sub_total              = $monto_subtotal;
            $pedido->monto_embalaje         = $monto_embalaje;
            $pedido->monto_envio            = $monto_envio;
            $pedido->total_igv              = $monto_igv;
            $pedido->total                  = $monto_total;
            $pedido->total_pagar            = $monto_total_pagar;  
            $pedido->monto_descuento        = $monto_descuento;
            $pedido->porcentaje_descuento   = $porcentaje_descuento;

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
            return redirect()->route('ventas.pedidos.index')->with('guardar', 'success');

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

        $vendedor_actual    =   DB::select('select c.id from user_persona as up
                                inner join colaboradores  as c
                                on c.persona_id=up.persona_id
                                where up.user_id = ?',[$pedido->user_id]);

        $vendedor_actual_id =   $vendedor_actual?$vendedor_actual[0]->id:null;  
        $modelos            =   Modelo::where('estado','ACTIVO')->get();
        $tallas             =   Talla::where('estado','ACTIVO')->get();
        $pedido_id          =   $id;
        
        return view('ventas.pedidos.edit',compact('empresas','clientes','vendedor_actual_id','condiciones',
                                            'modelos','tallas','pedido','pedido_detalles'));
    }

    public function update(Request $request,$id){
        DB::beginTransaction();
        try {
            $productos  =   json_decode($request->input('productos_tabla')[0]);
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
            $monto_subtotal     =   0.0;
            $monto_embalaje     =   $request->get('monto_embalaje')??0;
            $monto_envio        =   $request->get('monto_envio')??0;
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
    
            $monto_total_pagar      =   $monto_subtotal+$monto_embalaje+$monto_envio;
            $monto_total            =   $monto_total_pagar/1.18;
            $monto_igv              =   $monto_total_pagar-$monto_total;
            $porcentaje_descuento   =   ($monto_descuento*100)/($monto_total_pagar);


            //======== REGISTRANDO PEDIDO =========
            $pedido                 = Pedido::find($id);
            $pedido->cliente_id     = $request->get('cliente');

            //======== BUSCANDO NOMBRE DEL CLIENTE =====//
            $cliente    =   DB::select('select c.id,c.nombre from clientes as c
                            where c.id=?',[$request->get('cliente')]);
            
            $pedido->cliente_nombre     =   $cliente[0]->nombre;
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

            $pedido->moneda             = 1;
            $pedido->fecha_registro     = $request->get('fecha_documento');

            $pedido->sub_total              = $monto_subtotal;
            $pedido->monto_embalaje         = $monto_embalaje;
            $pedido->monto_envio            = $monto_envio;
            $pedido->total_igv              = $monto_igv;
            $pedido->total                  = $monto_total;
            $pedido->total_pagar            = $monto_total_pagar;  
            $pedido->monto_descuento        = $monto_descuento;
            $pedido->porcentaje_descuento   = $porcentaje_descuento;

            $pedido->update();
           
            //======= ELIMINANDO DETALLE ANTERIOR ========
            if(count($productos)>0){
                PedidoDetalle::where('pedido_id', $id)->delete();
            }

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
            $descripcion = "SE MODIFICÓ EL PEDIDO CON LA FECHA: ". Carbon::parse($pedido->fecha_registro)->format('d/m/y');
            $gestion = "PEDIDO";
            crearRegistro($pedido, $descripcion , $gestion);

            DB::commit();
            Session::flash('success','PEDIDO MODIFICADO.');
            return redirect()->route('ventas.pedidos.index')->with('guardar', 'success');

        } catch (\Throwable  $e) {
            DB::rollback();

            dd($e->getMessage());
        }
    }

    public function destroy($id){
        $pedido_id  =   $id;

        try {
            //==== ANULANDO PEDIDO ======
            $pedido         =   Pedido::find($id);
            $pedido->estado =   'ANULADO';
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


        $pdf = PDF::loadview('ventas.pedidos.reportes.detalle',[
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
            $tipo_doc_cliente   =   $pedido->cliente->tipo_documento;
            if($tipo_doc_cliente == 'DNI'){
                $tipoVentas = $tipoVentas->reject(function ($tipoVenta){
                    return $tipoVenta['id'] == 127;
                });
            }

        
            DB::commit();
            return view('ventas.pedidos.atender',compact('atencion_detalle','empresas','clientes',
                                                'vendedor_actual','condiciones',
                                                'modelos','tallas','pedido','tipoVentas'));
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th->getMessage());
        }
    }

    public function getDetalles($pedido_id){
        $pedido_detalles    =   DB::select('select * from pedidos_detalles as pd   
                                where pd.pedido_id=?',[$pedido_id]);

        
        return  response()->json(['type'=>'success','pedido_detalles'=>$pedido_detalles]);
    }

    public function getAtenciones($pedido_id){
        $pedido_atenciones    =   DB::select('select pa.pedido_id,cd.serie as documento_serie,
                                pa.fecha_atencion,cd.correlativo as documento_correlativo 
                                from pedidos_atenciones as pa 
                                inner join cotizacion_documento as cd   
                                where pa.pedido_id=? and pa.documento_id=cd.id',[$pedido_id]);

        
        return  response()->json(['type'=>'success','pedido_atenciones'=>$pedido_atenciones]);
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

    public function getProductosByModelo($modelo_id){
        try {
            $productos  =   DB::select('select distinct p.id as producto_id,c.id as color_id, t.id as talla_id,
            m.id as modelo_id, p.nombre as producto_nombre,c.descripcion as color_nombre, 
            t.descripcion as talla_nombre,pct.stock_logico,m.descripcion as modelo_nombre,p.codigo as producto_codigo,
            p.precio_venta_1,p.precio_venta_2,p.precio_venta_3 
            from producto_colores as pc 
            left join producto_color_tallas as pct on (pc.producto_id=pct.producto_id and pc.color_id=pct.color_id) 
            inner join productos as p on p.id=pc.producto_id 
            inner join colores as c on c.id=pc.color_id 
            inner join modelos as m on m.id=p.modelo_id
            left join tallas as t on t.id=pct.talla_id 
            where m.id=?
            order by p.nombre,c.descripcion',[$modelo_id]);    

           $productos_formateado    =   $this->formatearListado($productos);
            
            return response()->json(['type'=>'success','message'=>$productos_formateado]);

        } catch (\Throwable $e) {
            return response()->json(['type'=>'error','message'=>$e->getMessage()]);
        }
    }

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
                    $producto_color_talla['stock']              =   $talla->stock;   
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

    public function validarCantidadAtendida(Request $request){
        DB::beginTransaction();
        try {
            $data   =   $request->all();

            $cantidad_atendida_nueva        =   $request->get('cantidad_atendida_nueva');
            $cantidad_atendida_anterior     =   $request->get('cantidad_atendida_anterior');
    
            $producto_id        =   $request->get('producto_id');
            $color_id           =   $request->get('color_id');
            $talla_id           =   $request->get('talla_id');
    
            //======== OBTENER PRODUCTO ======
            $producto   =   DB::select('select pct.stock_logico from producto_color_tallas as pct
                            where pct.producto_id=? and pct.color_id=? and pct.talla_id=?',
                            [$producto_id,$color_id,$talla_id]);
            
            if(count($producto)>0){
                $stock_logico   =   $producto[0]->stock_logico;
    
                //======= SI LA NUEVA CANTIDAD ATENDIDA ES MENOR IGUAL AL STOCK LOGICO REPUESTO ======
                $stock_logico_repuesto  =   $stock_logico + $cantidad_atendida_anterior;   
                if($cantidad_atendida_nueva <= $stock_logico_repuesto){
                    //======== ACTUALIZAR STOCK_LOGICO ======
                    DB::table('producto_color_tallas')
                    ->where('producto_id', $producto_id)
                    ->where('color_id', $color_id)
                    ->where('talla_id', $talla_id)
                    ->update([
                        'stock_logico' => DB::raw('stock_logico + ' . ($cantidad_atendida_anterior - $cantidad_atendida_nueva)),
                    ]);

                    DB::commit();
                    return response()->json(['type'=>'success','data'=>$data,'message'=>'Stock lógico actualizado']);
                }else{
                    return response()->json(['type'=>'error','data'=>$data,'message'=>'Stock lógico ('.$stock_logico_repuesto .') es menor 
                    a la cantidad atendida ('.$cantidad_atendida_nueva.')']);
                }
            }else{
                return response()->json(['type'=>'error','data'=>$data,'message'=>'El producto no existe']);
            }
        } catch (\Throwable $th) {
            return response()->json(['type'=>'error','data'=>$th->getMessage(),'message'=>'Error en el servidor']);
        }
    }

    public function generarDocumentoVenta(Request $request){
        $pedido_id              =   $request->get('pedido_id');
        $documentoController    =   new DocumentoController();
        $res                    =   $documentoController->store($request);
        $jsonResponse           =   $res->getData(); 

        //====== MANEJO DE RESPUESTA =========
        $success_store_doc                =   $jsonResponse->success; 

        //===== EN CASO SE HAYA GENERADO EL DOC DE VENTA EXITOSAMENTE ======
        if($success_store_doc){
            $documento_id       =   $jsonResponse->documento_id;
            
            DB::beginTransaction();
            try {
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
                        'cantidad_atendida'     => $producto->cantidad,
                        'cantidad_pendiente'    => DB::raw('cantidad - ' . $producto->cantidad),
                    ]);
                }

                //======= GRABANDO ATENCIÓN =====
                $pedido_atencion                    =   new PedidoAtencion();
                $pedido_atencion->pedido_id         =   $pedido_id;
                $pedido_atencion->documento_id      =   $documento_id;
                $pedido_atencion->fecha_atencion    =   Carbon::now()->format('Y-m-d');
                $pedido_atencion->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'documento_id' => $documento_id,
                    'mensaje'      =>   'DOCUMENTO DE VENTA GENERADO - PEDIDO ACTUALIZADO'
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                //====== ELIMINAR DOC DE VENTA ====
                DB::table('cotizacion_documento')
                ->where('id', $documento_id)
                ->delete();

                return response()->json([
                    'success'   => false,
                    'mensaje'   => 'ERROR AL ACTUALIZAR EL PEDIDO', 
                    'excepcion' => $th->getMessage(),
                ]);
            }
        }else{
            return $res;
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
}
