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
            $pedido->user_nombre        = Auth::user()->usuario;
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
