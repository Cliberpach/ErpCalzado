<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ventas\Pedido;
use App\Ventas\PedidoDetalle;
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
                    $talla['talla_nombre']          =   $producto_color_talla->talla_nombre;
                    $subtotal                       +=  $talla['cantidad']*$producto['precio_unitario_nuevo'];

                    $cantidadTotal+=$talla['cantidad'];
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
            t.descripcion as talla_nombre,pct.stock,m.descripcion as modelo_nombre,p.codigo as producto_codigo,
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
}
