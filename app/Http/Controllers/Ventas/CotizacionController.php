<?php
namespace App\Http\Controllers\Ventas;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use App\Almacenes\Color;
use App\Almacenes\Kardex;
use App\Almacenes\LoteProducto;
use App\Almacenes\Marca;
use App\Almacenes\Producto;
use App\Almacenes\Modelo;
use App\Almacenes\Talla;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Ventas\Cotizacion\CotizacionADocVentaRequest;
use App\Http\Requests\Ventas\Cotizacion\CotizacionStoreRequest;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Sedes\Sede;
use App\Ventas\Cliente;
use App\Ventas\Cotizacion;
use App\Ventas\CotizacionDetalle;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Ventas\Pedido;
use App\Ventas\PedidoDetalle;

use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
use App\Pos\DetalleMovimientoVentaCaja;
use App\User;
use App\Ventas\EnvioVenta;
use Exception;

class CotizacionController extends Controller
{
    public function index()
    { 

        return view('ventas.cotizaciones.index');
    }

    public function getCotizaciones(Request $request)
    {
       
        $query = DB::table('cotizaciones as co')
                ->select(
                    DB::raw('CONCAT("CO-",co.id) as simbolo'),
                    'co.id',
                    'co.almacen_nombre',
                    'cl.nombre as cliente',
                    'co.created_at',
                    'co.registrador_nombre',
                    'co.total_pagar',
                    'co.estado',
                    'co.created_at',
                    DB::raw('IF(cd.cotizacion_venta IS NULL, "-", CONCAT(cd.serie,"-",cd.correlativo)) as documento'),
                    DB::raw('IF(p.id IS NULL, "-", CONCAT("PE-", p.id)) as pedido_id'),
                )
                ->join('clientes as cl', 'cl.id', '=', 'co.cliente_id')
                ->leftJoin('cotizacion_documento as cd', 'cd.cotizacion_venta', '=', 'co.id')
                ->leftJoin('pedidos as p', 'p.cotizacion_id', '=', 'co.id')
                ->where('co.estado','<>','ANULADO');

        $roles = DB::table('role_user as rl')
                ->join('roles as r', 'r.id', '=', 'rl.role_id')
                ->where('rl.user_id', Auth::user()->id)
                ->pluck('r.name')
                ->toArray(); 

        //======== ADMIN PUEDE VER TODAS LAS COTIZACIONES DE SU SEDE =====
        if (in_array('ADMIN', $roles)) {
            $query->where('co.sede_id', Auth::user()->sede_id);
        } else {
            
            //====== USUARIOS PUEDEN VER SOLO SUS PROPIAS COTIZACIONES ======
            $query->where('co.sede_id', Auth::user()->sede_id)
            ->where('co.registrador_id', Auth::user()->id);
            
        }
           
        return DataTables::of($query->get())->make(true); 
    }
    

    public function create()
    {
        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();

        $clientes           =   Cliente::where('estado', 'ACTIVO')->get();
        $condiciones        =   Condicion::where('estado','ACTIVO')->get();
        $modelos            =   Modelo::where('estado','ACTIVO')->get();
        $categorias         =   Categoria::where('estado','ACTIVO')->get();
        $marcas             =   Marca::where('estado','ACTIVO')->get();
        $tallas             =   Talla::where('estado','ACTIVO')->get();
       
        $registrador        =   DB::select('select 
                                u.* 
                                from users as u where u.id = ?',
                                [Auth::user()->id])[0];

        $sede_id            =   Auth::user()->sede_id;

        $almacenes          =   Almacen::where('estado','ACTIVO')->where('tipo_almacen','PRINCIPAL')->get();
        $porcentaje_igv     =   Empresa::find(1)->igv;

        return view('ventas.cotizaciones.create', 
        compact(
            'tallas',
            'modelos',
            'clientes', 
            'condiciones',
            'tipos_documento',
            'departamentos',
            'tipo_clientes',
            'categorias',
            'marcas',
            'registrador',
            'almacenes',
            'sede_id',
            'porcentaje_igv')
        );
    }


/*
array:10 [
  "_token"              => "XCcigubfOivrL6d1gCUmLO5X52q2rCU2nt6Xk2vN"
  "registrador"         => "ADMINISTRADOR"
  "fecha_registro"      => "2025-02-05"
  "almacen"             => "1"
  "condicion_id"        => "1"
  "cliente"             => "1"
  "lstCotizacion"       => "[{"producto_id":"1","color_id":"1","producto_nombre":"PRODUCTO TEST","color_nombre":"BLANCO","precio_venta":"1.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"10"}],"subtotal":10},{"producto_id":"1","color_id":"2","producto_nombre":"PRODUCTO TEST","color_nombre":"AZUL","precio_venta":"1.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"20"}],"subtotal":20},{"producto_id":"1","color_id":"3","producto_nombre":"PRODUCTO TEST","color_nombre":"CELESTE","precio_venta":"1.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"30"}],"subtotal":30},{"producto_id":"1","color_id":"4","producto_nombre":"PRODUCTO TEST","color_nombre":"PLOMO","precio_venta":"1.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"40"}],"subtotal":40}]"
  "sede_id"             => "1"
  "registrador_id"      => "1"
  "porcentaje_igv"      => "18.00"
  "montos_cotizacion"   => 
                            "{"subtotal":"100.00","embalaje":"11.00","envio":"12.00",
                            "total":"104.24","igv":"18.76",
                            "totalPagar":"123.00","monto_descuento":"0.00"}"
]
*/ 
    public function store(CotizacionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            
            $lstCotizacion      =   json_decode($request->get('lstCotizacion'));
            $montos_cotizacion  =   json_decode($request->get('montos_cotizacion'));

            $almacen            =   Almacen::find($request->get('almacen'));
            $registrador        =   User::find($request->get('registrador_id'));
        
            //======= CALCULANDO MONTOS ========
            $monto_subtotal     =   0.0;
            $monto_embalaje     =   $montos_cotizacion->embalaje??0;
            $monto_envio        =   $montos_cotizacion->envio??0;
            $monto_total        =   0.0;
            $monto_igv          =   0.0;
            $monto_total_pagar  =   0.0;
            $monto_descuento    =   $montos_cotizacion->monto_descuento??0;

            foreach ($lstCotizacion as $producto) {
                if( floatval($producto->porcentaje_descuento) == 0){
                    $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta);
                }else{
                    $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta_nuevo);
                }
            }

            $monto_total_pagar      =   $monto_subtotal+$monto_embalaje+$monto_envio;
            $monto_total            =   $monto_total_pagar/1.18;
            $monto_igv              =   $monto_total_pagar-$monto_total;
            $porcentaje_descuento   =   ($monto_descuento*100)/($monto_total_pagar);


            //======== REGISTRANDO MAESTRO COTIZACIÓN ======
            $cotizacion                     =   new Cotizacion();
            $cotizacion->empresa_id         =   1;
            $cotizacion->cliente_id         =   $request->get('cliente');
            $cotizacion->condicion_id       =   $request->get('condicion_id');
            $cotizacion->registrador_id     =   $request->get('registrador_id');
            $cotizacion->registrador_nombre =   $registrador->usuario;
            $cotizacion->fecha_documento    =   Carbon::now()->format('Y-m-d');
            $cotizacion->fecha_atencion     =   Carbon::now()->format('Y-m-d');
            $cotizacion->sede_id            =   $request->get('sede_id');
            $cotizacion->almacen_id         =   $almacen->id;
            $cotizacion->almacen_nombre     =   $almacen->descripcion;

            $cotizacion->sub_total              =   $monto_subtotal;
            $cotizacion->monto_embalaje         =   $monto_embalaje;
            $cotizacion->monto_envio            =   $monto_envio;
            $cotizacion->total_igv              =   $monto_igv;
            $cotizacion->total                  =   $monto_total;
            $cotizacion->total_pagar            =   $monto_total_pagar;  
            $cotizacion->monto_descuento        =   $monto_descuento;
            $cotizacion->porcentaje_descuento   =   $porcentaje_descuento;

            $cotizacion->moneda             =   4;
            $cotizacion->igv                =   $request->get('porcentaje_igv');
            $cotizacion->igv_check          =   "1";
            $cotizacion->save();


            //======= REGISTRO DETALLE DE LA COTIZACIÓN =====
            foreach ($lstCotizacion as $item) {

                $existe_producto    =   Producto::find($item->producto_id);
                $existe_color       =   Color::find($item->color_id);
                $existe_talla       =   Talla::find($item->talla_id);

                if(!$existe_producto){
                    throw new Exception("EL PRODUCTO NO EXISTE EN LA BD!!!");
                }

                if(!$existe_color){
                    throw new Exception("EL COLOR NO EXISTE EN LA BD!!!");
                }

                if(!$existe_talla){
                    throw new Exception("LA TALLA NO EXISTE EN LA BD!!!");
                }

              
                //==== CALCULANDO MONTOS PARA EL DETALLE ====
                $importe        =   floatval($item->cantidad) * floatval($item->precio_venta);
                $precio_venta   =   $item->porcentaje_descuento == 0?$item->precio_venta:$item->precio_venta_nuevo;


                $detalle                            =   new CotizacionDetalle();
                $detalle->cotizacion_id             =   $cotizacion->id;
                $detalle->almacen_id                =   $almacen->id;
                $detalle->producto_id               =   $item->producto_id;
                $detalle->color_id                  =   $item->color_id;
                $detalle->talla_id                  =   $item->talla_id;
                $detalle->almacen_nombre            =   $almacen->descripcion;
                $detalle->producto_nombre           =   $existe_producto->nombre;
                $detalle->color_nombre              =   $existe_color->descripcion;
                $detalle->talla_nombre              =   $existe_talla->descripcion;
                $detalle->cantidad                  =   $item->cantidad;
                $detalle->precio_unitario           =   $item->precio_venta;
                $detalle->importe                   =   $importe;
                $detalle->precio_unitario_nuevo     =   floatval($precio_venta);
                $detalle->porcentaje_descuento      =   floatval($item->porcentaje_descuento);
                $detalle->monto_descuento           =   floatval($importe) * floatval($item->porcentaje_descuento) / 100;
                $detalle->importe_nuevo             =   floatval($precio_venta) * floatval($item->cantidad);
                $detalle->save();

            }

            //Registro de actividad
            $descripcion = "SE AGREGÓ LA COTIZACION CON LA FECHA: ". Carbon::parse($cotizacion->fecha_documento)->format('d/m/y');
            $gestion = "COTIZACION";
            crearRegistro($cotizacion, $descripcion , $gestion);

            Session::flash('message_success','COTIZACIÓN REGISTRADA CON ÉXITO');
            DB::commit();
            return response()->json(['success'=>true,'message'=>"COTIZACIÓN REGISTRADA CON ÉXITO"]);
        
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
        
    }

    public function edit($id)
    {
        //=========== SI LA COTIZACIÓN TIENE UN DOC DE VENTA, YA NO PUEDE MODIFICARSE =========
        $exists_doc_venta   =   DB::table('cotizacion_documento')->where('cotizacion_venta', $id)->exists();
        if($exists_doc_venta){
            Session::flash('error','NO PUEDE MODIFICAR UNA COTIZACIÓN QUE TIENE UN DOCUMENTO DE VENTA GENERADO');
            return redirect()->back();
        }


        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();

        $cotizacion         =   Cotizacion::findOrFail($id);
        $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
        $clientes           =   Cliente::where('estado', 'ACTIVO')->get();
        $condiciones        =   Condicion::where('estado','ACTIVO')->get();
        $detalles           =   CotizacionDetalle::where('cotizacion_id',$id)->where('estado', 'ACTIVO')
                                ->with('producto', 'color', 'talla')->get();

        $modelos            =   Modelo::where('estado','ACTIVO')->get();
        $categorias         =   Categoria::where('estado','ACTIVO')->get();
        $marcas             =   Marca::where('estado','ACTIVO')->get();        $tallas         =   Talla::where('estado','ACTIVO')->get();
        $porcentaje_igv =   Empresa::find(1)->igv;

        $registrador    =   User::find($cotizacion->registrador_id);
        $almacenes      =   Almacen::where('estado','ACTIVO')->where('tipo_almacen','PRINCIPAL')->get();
        $sede_id        =   Auth::user()->sede_id;


        return view('ventas.cotizaciones.edit', [
            'cotizacion'        =>  $cotizacion,
            'empresas'          =>  $empresas,
            'clientes'          =>  $clientes,
            'condiciones'       =>  $condiciones,
            'detalles'          =>  $detalles,
            'modelos'           =>  $modelos,
            'categorias'        =>  $categorias,
            'marcas'            =>  $marcas,
            'tallas'            =>  $tallas,
            'tipos_documento'   =>  $tipos_documento,
            'departamentos'     =>  $departamentos,
            'tipo_clientes'     =>  $tipo_clientes,
            'porcentaje_igv'    =>  $porcentaje_igv,
            'registrador'       =>  $registrador,
            'almacenes'         =>  $almacenes,
            'sede_id'           =>  $sede_id
        ]);
    }


/*
array:11 [
  "_token"              => "tUfxjoYQNI8rXMfIXQtSNELqv4znU9yyUA5PT9hD"
  "registrador"         => "ADMINISTRADOR"
  "fecha_registro"      => "2025-02-10"
  "almacen"             => "2"
  "condicion_id"        => "1"
  "cliente"             => "1"
  "lstCotizacion"       => "[{"producto_id":1,"color_id":3,"talla_id":1,"cantidad":8,"precio_venta":"1.00","porcentaje_descuento":0,"precio_venta_nuevo":0},{"producto_id":1,"color_id":3,"talla_id":1,"cantidad":8,"precio_venta":"1.00","porcentaje_descuento":0,"precio_venta_nuevo":0},{"producto_id":1,"color_id":3,"talla_id":1,"cantidad":8,"precio_venta":"1.00","porcentaje_descuento":0,"precio_venta_nuevo":0},{"producto_id":1,"color_id":3,"talla_id":1,"cantidad":8,"precio_venta":"1.00","porcentaje_descuento":0,"precio_venta_nuevo":0}]"
  "sede_id"             => "1"
  "registrador_id"      => "1"
  "montos_cotizacion"   => "{"subtotal":"16.00","embalaje":"0.00","envio":"0.00","total":"13.56","igv":"2.44","totalPagar":"16.00","monto_descuento":"0.00"}"
  "porcentaje_igv"      => "18.00"
]
*/ 
    public function update(Request $request,$id)
    {
       
        DB::beginTransaction();


        try {
            $lstCotizacion      =   json_decode($request->get('lstCotizacion'));
            $montos_cotizacion  =   json_decode($request->get('montos_cotizacion'));

            $almacen            =   Almacen::find($request->get('almacen'));
            $registrador        =   User::find($request->get('registrador_id'));

            //======= CALCULANDO MONTOS ========
            $monto_subtotal     =   0.0;
            $monto_embalaje     =   $montos_cotizacion->embalaje??0;
            $monto_envio        =   $montos_cotizacion->envio??0;
            $monto_total        =   0.0;
            $monto_igv          =   0.0;
            $monto_total_pagar  =   0.0;
            $monto_descuento    =   $montos_cotizacion->monto_descuento??0;

            foreach ($lstCotizacion as $producto) {
                if( floatval($producto->porcentaje_descuento) == 0){
                    $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta);
                }else{
                    $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta_nuevo);
                }
            }

            $monto_total_pagar      =   $monto_subtotal+$monto_embalaje+$monto_envio;
            $monto_total            =   $monto_total_pagar/1.18;
            $monto_igv              =   $monto_total_pagar-$monto_total;
            $porcentaje_descuento   =   ($monto_descuento*100)/($monto_total_pagar);

            //======== REGISTRANDO MAESTRO COTIZACIÓN ======
            $cotizacion                     =   Cotizacion::find($id);
            $cotizacion->empresa_id         =   1;
            $cotizacion->cliente_id         =   $request->get('cliente');
            $cotizacion->condicion_id       =   $request->get('condicion_id');
            $cotizacion->registrador_id     =   $request->get('registrador_id');
            $cotizacion->registrador_nombre =   $registrador->usuario;
            $cotizacion->fecha_documento    =   Carbon::now()->format('Y-m-d');
            $cotizacion->fecha_atencion     =   Carbon::now()->format('Y-m-d');
            $cotizacion->sede_id            =   $request->get('sede_id');
            $cotizacion->almacen_id         =   $almacen->id;
            $cotizacion->almacen_nombre     =   $almacen->descripcion;

            $cotizacion->sub_total              =   $monto_subtotal;
            $cotizacion->monto_embalaje         =   $monto_embalaje;
            $cotizacion->monto_envio            =   $monto_envio;
            $cotizacion->total_igv              =   $monto_igv;
            $cotizacion->total                  =   $monto_total;
            $cotizacion->total_pagar            =   $monto_total_pagar;  
            $cotizacion->monto_descuento        =   $monto_descuento;
            $cotizacion->porcentaje_descuento   =   $porcentaje_descuento;

            $cotizacion->moneda             =   4;
            $cotizacion->igv                =   $request->get('porcentaje_igv');
            $cotizacion->igv_check          =   "1";
            $cotizacion->update();

            if ($lstCotizacion) {

                //======== ELIMINAR DETALLE ANTERIOR ======
                CotizacionDetalle::where('cotizacion_id', $id)->delete();
    
                foreach ($lstCotizacion as $item) {

                    $existe_producto    =   Producto::find($item->producto_id);
                    $existe_color       =   Color::find($item->color_id);
                    $existe_talla       =   Talla::find($item->talla_id);
    
                    if(!$existe_producto){
                        throw new Exception("EL PRODUCTO NO EXISTE EN LA BD!!!");
                    }
    
                    if(!$existe_color){
                        throw new Exception("EL COLOR NO EXISTE EN LA BD!!!");
                    }
    
                    if(!$existe_talla){
                        throw new Exception("LA TALLA NO EXISTE EN LA BD!!!");
                    }
    
                    //==== CALCULANDO MONTOS PARA EL DETALLE ====
                    $importe        =   floatval($item->cantidad) * floatval($item->precio_venta);
                    $precio_venta   =   $item->porcentaje_descuento == 0?$item->precio_venta:$item->precio_venta_nuevo;
    
                    $detalle                        = new CotizacionDetalle();
                    $detalle->cotizacion_id         = $cotizacion->id;
                    $detalle->almacen_id            = $almacen->id;
                    $detalle->producto_id           = $item->producto_id;
                    $detalle->color_id              = $item->color_id;
                    $detalle->talla_id              = $item->talla_id;
                    $detalle->almacen_nombre        = $almacen->descripcion;
                    $detalle->producto_nombre       = $existe_producto->nombre;
                    $detalle->color_nombre          = $existe_color->descripcion;
                    $detalle->talla_nombre          = $existe_talla->descripcion;
                    $detalle->cantidad              = $item->cantidad;
                    $detalle->precio_unitario       = $item->precio_venta;
                    $detalle->importe               = $importe;
                    $detalle->precio_unitario_nuevo = floatval($precio_venta);
                    $detalle->porcentaje_descuento  = floatval($item->porcentaje_descuento);
                    $detalle->monto_descuento       = floatval($importe) * floatval($item->porcentaje_descuento) / 100;
                    $detalle->importe_nuevo         = floatval($precio_venta) * floatval($item->cantidad);
                    $detalle->save();
    
                }
            }
    
            //Registro de actividad
            $descripcion = "SE MODIFICÓ LA COTIZACION CON LA FECHA: ". Carbon::parse($cotizacion->fecha_documento)->format('d/m/y');
            $gestion    = "COTIZACION";
            modificarRegistro($cotizacion, $descripcion , $gestion);
    
            Session::flash('success','Cotización modificada.');
            
            DB::commit();
            return response()->json(['success'=>true,'message'=>"COTIZACIÓN ACTUALIZADA"]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }

    }

    public function show($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);
        $nombre_completo = $cotizacion->user->empleado->persona->apellido_paterno.' '.$cotizacion->user->empleado->persona->apellido_materno.' '.$cotizacion->user->empleado->persona->nombres;
        $presentaciones = presentaciones();
        $detalles = CotizacionDetalle::where('cotizacion_id',$id)->where('estado','ACTIVO')->get();
        $condiciones = Condicion::where('estado','ACTIVO')->get();

        return view('ventas.cotizaciones.show', [
            'cotizacion' => $cotizacion,
            'detalles' => $detalles,
            'presentaciones' => $presentaciones,
            'condiciones' => $condiciones,
            'nombre_completo' => $nombre_completo
        ]);
    }

    public function destroy($id)
    {

        $cotizacion = Cotizacion::findOrFail($id);
        $cotizacion->estado = "ANULADO";
        $cotizacion->update();

        $cotizacion_detalle = CotizacionDetalle::where('cotizacion_id',$id)->get();
        foreach ($cotizacion_detalle as $detalle) {
            $detalle->estado = "ANULADO";
            $detalle->update();

        }

        //Registro de actividad
        $descripcion = "SE ELIMINÓ LA COTIZACION CON LA FECHA: ". Carbon::parse($cotizacion->fecha_documento)->format('d/m/y');
        $gestion = "COTIZACION";
        eliminarRegistro($cotizacion, $descripcion , $gestion);

        Session::flash('success','Cotización eliminada.');
        return redirect()->route('ventas.cotizacion.index')->with('eliminar', 'success');
    }

    public function email($id)
    {

        $cotizacion = Cotizacion::findOrFail($id);
        $nombre_completo = $cotizacion->user->empleado->persona->apellido_paterno.' '.$cotizacion->user->empleado->persona->apellido_materno.' '.$cotizacion->user->empleado->persona->nombres;
        $igv = '';
        $tipo_moneda = '';
        $detalles = $cotizacion->detalles->where('estado', 'ACTIVO');


        // $presentaciones = presentaciones();
        $paper_size = array(0,0,360,360);
        $pdf = PDF::loadview('ventas.cotizaciones.reportes.detalle',[
            'cotizacion' => $cotizacion,
            'nombre_completo' => $nombre_completo,
            'detalles' => $detalles,
            ])->setPaper('a4')->setWarnings(false);

        Mail::send('email.cotizacion',compact("cotizacion"), function ($mail) use ($pdf,$cotizacion) {
            $mail->to($cotizacion->cliente->correo_electronico);
            $mail->subject('COTIZACION OC-0'.$cotizacion->id);
            $mail->attachdata($pdf->output(), 'COTIZACION CO-0'.$cotizacion->id.'.pdf');
        });

        Session::flash('success','Cotización enviado al correo '.$cotizacion->cliente->correo_electronico);
        return redirect()->route('ventas.cotizacion.show', $cotizacion->id)->with('enviar', 'success');
    }

    public function report($id)
    {
        $cotizacion         = Cotizacion::findOrFail($id);
        $sede               = Sede::find($cotizacion->sede_id);
        $tallas             = Talla::all();
        $nombre_completo    = $cotizacion->registrador_nombre;
        $igv = '';
        $tipo_moneda = '';
        $detalles           = $cotizacion->detalles->where('estado', 'ACTIVO');
        $empresa            = Empresa::first();
        $paper_size         = array(0,0,360,360);

        $mostrar_cuentas =   DB::select('select 
                            c.propiedad 
                            from configuracion as c 
                            where c.slug = "MCB"')[0]->propiedad;

        $detalles = $this->formatearArrayDetalle($detalles);

        $vendedor_nombre =  $nombre_completo;


        $pdf = PDF::loadview('ventas.cotizaciones.reportes.detalle_nuevo',[
            'cotizacion'        => $cotizacion,
            'nombre_completo'   => $nombre_completo,
            'detalles'          => $detalles,
            'empresa'           => $empresa,
            'tallas'            => $tallas,
            'vendedor_nombre'   => $vendedor_nombre,
            'mostrar_cuentas'   => $mostrar_cuentas,
            'sede'              => $sede
            ])->setPaper('a4')->setWarnings(false);
        return $pdf->stream('CO-'.$cotizacion->id.'.pdf');
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
                
                $producto['producto_codigo']        =   $detalle->producto->codigo;
                $producto['producto_id']            =   $detalle->producto_id;
                $producto['color_id']               =   $detalle->color_id;
                $producto['producto_nombre']        =   $detalle->producto->nombre;
                $producto['color_nombre']           =   $detalle->color->descripcion;
                $producto['modelo_nombre']          =   $detalle->producto->modelo->descripcion;
                $producto['precio_unitario']        =   $detalle->precio_unitario;
                $producto['porcentaje_descuento']   =   $detalle->porcentaje_descuento;
                $producto['precio_unitario_nuevo']  =   $detalle->precio_unitario_nuevo;

                $tallas             =   [];
                $subtotal           =   0.0;
                $subtotal_with_desc =   0.0;
                $cantidadTotal=0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla=[];
                    $talla['talla_id']=$producto_color_talla->talla_id;
                    $talla['cantidad']=$producto_color_talla->cantidad;
                    $talla['talla_nombre']=$producto_color_talla->talla->descripcion;
                    $subtotal+=$talla['cantidad']*$producto['precio_unitario_nuevo'];

                    $cantidadTotal+=$talla['cantidad'];
                   array_push($tallas,$talla);
                }
                
                $producto['tallas']=$tallas;
                $producto['subtotal']=$subtotal;
                $producto['cantidad_total']=$cantidadTotal;
                array_push($detalleFormateado,$producto);
                $productosProcesados[] = $detalle->producto_id.'-'.$detalle->color_id;
            }
        }
        return $detalleFormateado;
    }

    public function document($id){
        
        $documento = Documento::where('cotizacion_venta',$id)->where('estado','!=','ANULADO')->first();
        if ($documento) {
            Session::flash('error', 'Esta cotizacion ya tiene un documento de venta generado.');
            return redirect()->route('ventas.cotizacion.index');
        }else{
            return redirect()->route('ventas.documento.create',['cotizacion'=>$id]);
        }

    }


/*
array:10 [
  "_token"                  => "qegJeKm09VqQFpw1FLfPFXpfNpbi9D6hQy2p9MtE"
  "cotizacion_id"           => "15"
  "data_envio"              => null
  "fecha_documento_campo"   => "2025-02-11"
  "tipo_venta"              => "129"
  "observacion"             => null
  "fecha_vencimiento_campo" => "2025-02-11"
  "tipo_cliente_documento"  => null
  "tipo_cliente_2"          => "1"
  "cot_doc"                 => "SI"
  "data_envio"              => "{"departamento":{"nombre":"LA LIBERTAD"},"provincia":{"text":"TRUJILLO"},"distrito":{"text":"TRUJILLO"},"empresa_envio":{"id":"2","empresa":"EMTRAFESA"},"sede_envio":{"id":"2","direccion":"AV TUPAC AMARU 123"},"tipo_envio":{"descripcion":"AGENCIA"},"destinatario":{"nro_documento":"75608753","nombres":"LUIS DANIEL ALVA LUJAN","tipo_documento":"DNI"},"tipo_pago_envio":{"descripcion":"PAGAR ENVÍO"},"entrega_domicilio":true,"direccion_entrega":"av rotulos 111","fecha_envio_propuesta":"2025-02-13","origen_venta":{"descripcion":"WATHSAPP"},"obs_rotulo":"obs rotulado","obs_despacho":"obs despacho"}"
 
]
*/ 
    public function convertirADocVenta(CotizacionADocVentaRequest $request){
      
        DB::beginTransaction();
        try {

            //======= VALIDAR EXISTENCIA DEL PARÁMETRO COTIZACIÓN ID =======
            $cotizacion_id  =   $request->get('cotizacion_id');
            if(!$cotizacion_id){
                throw new Exception("NO EXISTE COTIZACIÓN ID EN LA PETICIÓN!!!");
            } 

            //======= VALIDAR EXISTENCIA DE COTIZACIÓN EN LA BD =======
            $cotizacion     =   Cotizacion::find($request->get('cotizacion_id'));
            if(!$cotizacion){
                throw new Exception("NO EXISTE LA COTIZACIÓN EN LA BD!!!");
            }

            //========== VALIDAR QUE LA COTIZACIÓN NO ESTÉ CONVERTIDA AÚN =========
            $documento  =   Documento::where('convertir', $request->get('cotizacion_id'))->first();
            if($documento){
                throw new Exception("LA COTIZACIÓN YA ESTÁ CONVERTIDA EN DOCUMENTO DE VENTA!!!");
            }

            //======== VALIDANDO QUE EL USUARIO QUE CREÓ LA COTIZACIÓN SEA EL MISMO QUE CONVIERTE ======
            if(Auth::user()->id != $cotizacion->registrador_id){
                throw new Exception("SOLO EL USUARIO QUE CREÓ LA COTIZACIÓN PUEDE CONVERTIRLA A DOC VENTA!!!");
            }

            //========= VALIDAR QUE EL COLABORADOR ESTÉ EN UNA CAJA ABIERTA ======
            $caja_movimiento           =   movimientoUser();
          
            if(count($caja_movimiento) == 0 ){
                throw new Exception("DEBES FORMAR PARTE DE UNA CAJA ABIERTA!!!");
            }

            $tipo_venta         =   DB::select('select 
                                    td.* 
                                    from tabladetalles as td
                                    where td.id = ?',[$request->get('tipo_venta')])[0];

            //======= VALIDAR QUE EL DOCUMENTO VENTA ESTÉ ACTIVO =======
            DocumentoController::comprobanteActivo($cotizacion->sede_id,$tipo_venta);

            //======== OBTENIENDO LEYENDA ======
            $legenda                =   UtilidadesController::convertNumeroLetras($cotizacion->total_pagar);


            $cotizacion_detalle =   CotizacionDetalle::where('cotizacion_id',$cotizacion->id)->get();

            $datos_correlativo  =   DocumentoController::getCorrelativo($tipo_venta,$cotizacion->sede_id);
            $condicion          =   Condicion::find($cotizacion->condicion_id);
            $almacen            =   Almacen::find($cotizacion->almacen_id);

            //====== GRABAR MAESTRO VENTA =====
            $documento                      = new Documento();

            //========= FECHAS ========
            $documento->fecha_documento     = Carbon::now()->toDateString();
            $documento->fecha_atencion      = Carbon::now()->toDateString();
  
            if ($condicion->id != 1) {
                $nro_dias                       = $condicion->dias; 
                $documento->fecha_vencimiento   = Carbon::now()->addDays($nro_dias)->toDateString();
            } else {
                  $documento->fecha_vencimiento   = Carbon::now()->toDateString();
            }

  
            //======== EMPRESA ========
            $empresa                                =   Empresa::find($cotizacion->empresa_id);
            $documento->ruc_empresa                 =   $empresa->ruc;
            $documento->empresa                     =   $empresa->razon_social;
            $documento->direccion_fiscal_empresa    =   $empresa->direccion_fiscal;
            $documento->empresa_id                  =   $empresa->id; 
  
             
            //========= CLIENTE =======
            $cliente                            =   Cliente::find($cotizacion->cliente_id);
            $documento->tipo_documento_cliente  =   $cliente->tipo_documento;
            $documento->documento_cliente       =   $cliente->documento;
            $documento->direccion_cliente       =   $cliente->direccion;
            $documento->cliente                 =   $cliente->nombre;
            $documento->cliente_id              =   $cliente->id; 
  
            //======== TIPO VENTA ======
            $documento->tipo_venta_id           = $tipo_venta->id;   //boleta,factura,nota_venta
            $documento->tipo_venta_nombre       = $tipo_venta->descripcion;   
  
            //========= CONDICIÓN PAGO ======
            $documento->condicion_id            = $condicion->id;
  
              
            $documento->observacion = $request->get('observacion');
            $documento->user_id     = $cotizacion->registrador_id;
  
             
            //========= MONTOS Y MONEDA ========
            $documento->sub_total               =   $cotizacion->sub_total;
            $documento->monto_embalaje          =   $cotizacion->monto_embalaje;  
            $documento->monto_envio             =   $cotizacion->monto_envio;  
            $documento->total                   =   $cotizacion->total;  
            $documento->total_igv               =   $cotizacion->total_igv;
            $documento->total_pagar             =   $cotizacion->total_pagar;  
            $documento->igv                     =   $cotizacion->igv;
            $documento->monto_descuento         =   $cotizacion->monto_descuento;
            $documento->porcentaje_descuento    =   $cotizacion->porcentaje_descuento;   
            $documento->moneda                  =   1;
  
            //======= SERIE Y CORRELATIVO ======
            $documento->serie       =   $datos_correlativo->serie;
            $documento->correlativo =   $datos_correlativo->correlativo;
  
            $documento->legenda     =   $legenda;
  
            $documento->sede_id         =   $cotizacion->sede_id;
            $documento->almacen_id      =   $cotizacion->almacen_id;
            $documento->almacen_nombre  =   $cotizacion->almacen_nombre;

            $documento->cotizacion_venta    =   $cotizacion->id;

            if($request->has('facturado') && $request->get('facturado') === 'SI'){
                $documento->estado_pago  =   'PAGADA';
            }
  
            $documento->save();

            //========= GRABAR DETALLE ========
            foreach($cotizacion_detalle as $item){
               
                //====== COMPROBAR SI EXISTE EL PRODUCTO COLOR TALLA EN EL ALMACÉN =====
                $existe =   DB::select('select 
                            pct.*,
                            p.nombre as producto_nombre,
                            p.codigo as producto_codigo,
                            c.descripcion as color_nombre,
                            t.descripcion as talla_nombre,
                            m.descripcion as modelo_nombre
                            from producto_color_tallas as pct
                            inner join productos as p on p.id = pct.producto_id
                            inner join colores as c on c.id = pct.color_id
                            inner join tallas as t on t.id = pct.talla_id
                            inner join modelos as m on m.id = p.modelo_id
                            where 
                            pct.almacen_id = ?
                            AND pct.producto_id = ?
                            AND pct.color_id = ?
                            AND pct.talla_id = ?',
                            [$cotizacion->almacen_id,
                            $item->producto_id,
                            $item->color_id,
                            $item->talla_id]);

                if(count($existe) === 0){
                        throw new Exception($item->producto_nombre.'-'.$item->color_nombre.'-'.$item->talla_nombre.', NO EXISTE EN EL ALMACÉN!!!');
                }

                  
                $detalle                            =   new Detalle();
                $detalle->documento_id              =   $documento->id;
                $detalle->almacen_id                =   $item->almacen_id;
                $detalle->producto_id               =   $item->producto_id;
                $detalle->color_id                  =   $item->color_id;
                $detalle->talla_id                  =   $item->talla_id;
                $detalle->almacen_nombre            =   $item->almacen_nombre;
                $detalle->codigo_producto           =   $existe[0]->producto_codigo;
                $detalle->nombre_producto           =   $item->producto_nombre;
                $detalle->nombre_color              =   $item->color_nombre;
                $detalle->nombre_talla              =   $item->talla_nombre;
                $detalle->nombre_modelo             =   $existe[0]->modelo_nombre;
                $detalle->cantidad                  =   floatval($item->cantidad);
                $detalle->precio_unitario           =   floatval($item->precio_unitario);
                $detalle->importe                   =   $item->importe;
                $detalle->precio_unitario_nuevo     =   floatval($item->precio_unitario_nuevo);
                $detalle->porcentaje_descuento      =   floatval($item->porcentaje_descuento);
                $detalle->monto_descuento           =   $item->monto_descuento;
                $detalle->importe_nuevo             =   $item->importe_nuevo;
                $detalle->cantidad_sin_cambio       =   (int) $item->cantidad;
                $detalle->save();

                //===== ACTUALIZANDO STOCK ===========
                DB::update('UPDATE producto_color_tallas 
                SET stock = stock - ? 
                WHERE 
                almacen_id = ?
                AND producto_id = ? 
                AND color_id = ? 
                AND talla_id = ?', 
                [$item->cantidad,
                $item->almacen_id,
                $item->producto_id, 
                $item->color_id, 
                $item->talla_id]);

                $nuevo_stock    =   DB::table('producto_color_tallas')
                                    ->where('almacen_id', $item->almacen_id)
                                    ->where('producto_id', $item->producto_id)
                                    ->where('color_id', $item->color_id)
                                    ->where('talla_id', $item->talla_id)
                                    ->value('stock');

                //======= KARDEX CON STOCK YA MODIFICADO =======
                $kardex                     =   new Kardex();
                $kardex->sede_id            =   $cotizacion->sede_id;
                $kardex->almacen_id         =   $item->almacen_id;
                $kardex->producto_id        =   $item->producto_id;
                $kardex->color_id           =   $item->color_id;
                $kardex->talla_id           =   $item->talla_id;
                $kardex->almacen_nombre     =   $item->almacen_nombre;
                $kardex->producto_nombre    =   $item->producto_nombre;
                $kardex->color_nombre       =   $item->color_nombre;
                $kardex->talla_nombre       =   $item->talla_nombre;
                $kardex->cantidad           =   $item->cantidad;
                $kardex->precio             =   $item->precio_unitario_nuevo;
                $kardex->importe            =   $item->importe_nuevo;
                $kardex->accion             =   'VENTA';
                $kardex->stock              =   $nuevo_stock;
                $kardex->numero_doc         =   $documento->serie.'-'.$documento->correlativo;
                $kardex->documento_id       =   $documento->id;
                $kardex->registrador_id     =   $documento->user_id;
                $kardex->registrador_nombre =   $cotizacion->registrador_nombre;
                $kardex->fecha              =   Carbon::today()->toDateString();
                $kardex->descripcion        =   mb_strtoupper($request->get('observacion'), 'UTF-8');
                $kardex->save();
                    
            }

            //======== GUARDAR ENVÍO ========
            $data_envio     =   json_decode($request->get('data_envio'));
            if (!empty((array)$data_envio)) {

                $envio_venta                        =   new EnvioVenta();
                $envio_venta->documento_id          =   $documento->id;
                $envio_venta->departamento          =   $data_envio->departamento->nombre;
                $envio_venta->provincia             =   $data_envio->provincia->text;
                $envio_venta->distrito              =   $data_envio->distrito->text;
                $envio_venta->empresa_envio_id      =   $data_envio->empresa_envio->id;
                $envio_venta->empresa_envio_nombre  =   $data_envio->empresa_envio->empresa;
                $envio_venta->sede_envio_id         =   $data_envio->sede_envio->id;
                $envio_venta->sede_envio_nombre     =   $data_envio->sede_envio->direccion;
                $envio_venta->tipo_envio            =   $data_envio->tipo_envio->descripcion;
                $envio_venta->destinatario_tipo_doc =   $data_envio->destinatario->tipo_documento;
                $envio_venta->destinatario_nro_doc  =   $data_envio->destinatario->nro_documento;
                $envio_venta->destinatario_nombre   =   $data_envio->destinatario->nombres;
                $envio_venta->cliente_id            =   $documento->cliente_id;
                $envio_venta->cliente_nombre        =   $documento->cliente;
                $envio_venta->tipo_pago_envio       =   $data_envio->tipo_pago_envio->descripcion;
                $envio_venta->monto_envio           =   $documento->monto_envio;
                $envio_venta->entrega_domicilio     =   $data_envio->entrega_domicilio?"SI":"NO";
                $envio_venta->direccion_entrega     =   $data_envio->direccion_entrega;
                $envio_venta->documento_nro         =   $documento->serie.'-'.$documento->correlativo;
                $envio_venta->fecha_envio_propuesta =   $data_envio->fecha_envio_propuesta;
                $envio_venta->origen_venta          =   $data_envio->origen_venta->descripcion;
                $envio_venta->obs_rotulo            =   $data_envio->obs_rotulo;
                $envio_venta->obs_despacho          =   $data_envio->obs_despacho;
                $envio_venta->cliente_celular       =   $documento->clienteEntidad->telefono_movil;
                $envio_venta->user_vendedor_id      =   $documento->user_id;
                $envio_venta->user_vendedor_nombre  =   $documento->user->usuario;
                $envio_venta->almacen_id            =   $documento->almacen_id;
                $envio_venta->almacen_nombre        =   $documento->almacen_nombre;
                $envio_venta->sede_id               =   $documento->sede_id;
                $envio_venta->sede_despachadora_id  =   $almacen->sede_id;
                $envio_venta->save();
             
            }else{
                   
                    
                //======== OBTENER EMPRESA ENVÍO =======
                $empresa_envio                      =   DB::select('select 
                                                        ee.id,ee.empresa,ee.tipo_envio
                                                        from empresas_envio as ee')[0];
                    
                $sede_envio                         =   DB::select('select 
                                                        ees.id,ees.direccion 
                                                        from empresa_envio_sedes as ees
                                                        where ees.empresa_envio_id=?',[$empresa_envio->id])[0];
                
                $envio_venta                        =   new EnvioVenta();
                $envio_venta->documento_id          =   $documento->id;
                $envio_venta->departamento          =   "LA LIBERTAD";
                $envio_venta->provincia             =   "TRUJILLO";
                $envio_venta->distrito              =   "TRUJILLO";
                $envio_venta->empresa_envio_id      =   $empresa_envio->id;
                $envio_venta->empresa_envio_nombre  =   $empresa_envio->empresa;
                $envio_venta->sede_envio_id         =   $sede_envio->id;
                $envio_venta->sede_envio_nombre     =   $sede_envio->direccion;
                $envio_venta->tipo_envio            =   $empresa_envio->tipo_envio;
                $envio_venta->destinatario_tipo_doc =   $documento->tipo_documento_cliente;
                $envio_venta->destinatario_nro_doc  =   $documento->documento_cliente;
                $envio_venta->destinatario_nombre   =   $documento->cliente;
                $envio_venta->cliente_id            =   $documento->cliente_id;
                $envio_venta->cliente_nombre        =   $documento->cliente;
                $envio_venta->tipo_pago_envio       =   "-";
                $envio_venta->monto_envio           =   $documento->monto_envio;
                $envio_venta->entrega_domicilio     =   "NO";
                $envio_venta->direccion_entrega     =   null;
                $envio_venta->documento_nro         =   $documento->serie.'-'.$documento->correlativo;
                $envio_venta->fecha_envio_propuesta =   null;
                $envio_venta->origen_venta          =   "WHATSAPP";
                $envio_venta->obs_despacho          =   null;
                $envio_venta->obs_rotulo            =   null;
                $envio_venta->estado                =   'DESPACHADO';
                $envio_venta->cliente_celular       =   $documento->clienteEntidad->telefono_movil;
                $envio_venta->user_vendedor_id      =   $documento->user_id;
                $envio_venta->user_vendedor_nombre  =   $documento->user->usuario;
                $envio_venta->user_despachador_id   =   $documento->user_id;
                $envio_venta->user_despachador_nombre   =   $documento->user->usuario;
                $envio_venta->almacen_id            =   $documento->almacen_id;
                $envio_venta->almacen_nombre        =   $documento->almacen_nombre;
                $envio_venta->sede_id               =   $documento->sede_id;
                $envio_venta->sede_despachadora_id  =   $almacen->sede_id;
                $envio_venta->save();
            }

            //======== ASOCIAR LA VENTA CON EL MOVIMIENTO CAJA DEL COLABORADOR ====
            $movimiento_venta                   =   new DetalleMovimientoVentaCaja();
            $movimiento_venta->cdocumento_id    =   $documento->id;
            $movimiento_venta->mcaja_id         =   $caja_movimiento[0]->movimiento_id;
            if($request->has('facturado') && $request->get('facturado') === 'SI'){
                $movimiento_venta->cobrar       =   'NO';
            }
            $movimiento_venta->save();

            //========== ACTUALIZAR ESTADO FACTURACIÓN A INICIADA ======
            DB::table('empresa_numeracion_facturaciones')
             ->where('empresa_id', Empresa::find(1)->id) 
             ->where('sede_id', $cotizacion->sede_id) 
             ->where('tipo_comprobante', $documento->tipo_venta_id) 
             ->where('emision_iniciada', '0') 
             ->where('estado','ACTIVO')
             ->update([
                'emision_iniciada'       => '1',
                'updated_at'             => Carbon::now()
            ]);


            DB::commit();
            return response()->json(['success'=>true,
            'message'=>'DOCUMENTO DE VENTA GENERADO CON ÉXITO',
            'documento_id'=>$documento->id]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public function newDocument($id){
        $documento_old =  Documento::where('cotizacion_venta',$id)->where('estado','!=','ANULADO')->first();
        if($documento_old->sunat == '1' && $documento_old->tipo_venta != '129')
        {
            Session::flash('error','Este documento ya fue informado a sunat, si desea reemplazarlo debe hacerle una nota de credito y crear un nuevo documento.');
            return redirect()->route('ventas.cotizacion.index');
        }
        foreach ($documento_old->detalles as $detalle) {
            $lote = LoteProducto::find($detalle->lote_id);
            $cantidad = $detalle->cantidad - $detalle->detalles->sum('cantidad');
            $lote->cantidad = $lote->cantidad + $cantidad;
            $lote->cantidad_logica = $lote->cantidad_logica + $cantidad;
            $lote->update();
            //ANULAMOS EL DETALLE
            $detalle->estado = "ANULADO";
            $detalle->update();
        }
        //ANULADO ANTERIO DOCUMENTO
        $documento = Documento::findOrFail($documento_old->id);
        $documento->estado = 'ANULADO';
        $documento->update();
        //REDIRECCIONAR AL DOCUMENTO DE VENTA
        return redirect()->route('ventas.documento.create',['cotizacion'=>$id]);

    }

/*
array:1 [
  "cotizacion_id" => 18
]
*/ 
    public function generarPedido(Request $request){
     
        DB::beginTransaction();
        
        try {
           
            $cotizacion_id  =   $request->get('cotizacion_id');

            if(!$cotizacion_id){
                throw new Exception("FALTA EL PARÁMETRO COTIZACIÓN ID EN LA PETICIÓN");
            }

            $pedido =   Pedido::find($cotizacion_id);
            if($pedido){
                throw new Exception("LA COTIZACIÓN YA FUE CONVERTIDA A PEDIDO");
            }

            //===== OBTENIENDO DETALLE DE LA COTIZACIÓN ===========
            $cotizacion =   DB::select('select 
                            c.* 
                            from cotizaciones as c
                            where 
                            c.id = ? 
                            AND c.estado != "ANULADO" 
                            AND c.estado != "VENCIDA"'
                            ,[$cotizacion_id]);

            $detalle_cotizacion =   DB::select('select 
                                    cd.* 
                                    from cotizacion_detalles as cd 
                                    where 
                                    cd.cotizacion_id = ? 
                                    and cd.estado = "ACTIVO"',
                                    [$cotizacion_id]);

            //======== CREAR PEDIDO =========
            $pedido             =   new Pedido();
            $pedido->cliente_id = $cotizacion[0]->cliente_id;

            //======= OBTENIENDO NOMBRE DEL CLIENTE =======
            $cliente    =   DB::select('select 
                            c.nombre 
                            from clientes as c 
                            where 
                            c.estado="ACTIVO" 
                            and c.id=?',[$cotizacion[0]->cliente_id]);
            
            $pedido->cliente_nombre =   $cliente[0]->nombre;   
            $pedido->empresa_id     =   $cotizacion[0]->empresa_id;
            
            //======== OBTENIENDO NOMBRE DE LA EMPRESA ========
            $empresa    =   DB::select('select 
                            e.razon_social 
                            from empresas as e
                            where 
                            e.estado="ACTIVO" 
                            and e.id = ?',
                            [$cotizacion[0]->empresa_id]);

            $pedido->empresa_nombre =   $empresa[0]->razon_social;
            $pedido->user_id        =   $cotizacion[0]->registrador_id;

            //====== OBTENIENDO NOMBRE DEL USUARIO =====
            $usuario    =   DB::select('select 
                            u.usuario 
                            from users as u 
                            where 
                            u.estado = "ACTIVO" 
                            and u.id=?',
                            [$cotizacion[0]->registrador_id]);

            $pedido->user_nombre    =   $usuario[0]->usuario;
            $pedido->condicion_id   =   $cotizacion[0]->condicion_id;
            $pedido->moneda         =   $cotizacion[0]->moneda;

            //======= OBTENIENDO NRO DEL PEDIDO =======
            $cantidad_pedidos   =   Pedido::count();
            $pedido->pedido_nro =   $cantidad_pedidos+1;

            $pedido->sub_total              =   $cotizacion[0]->sub_total;
            $pedido->total                  =   $cotizacion[0]->total;
            $pedido->total_igv              =   $cotizacion[0]->total_igv;
            $pedido->total_pagar            =   $cotizacion[0]->total_pagar;
            $pedido->monto_embalaje         =   $cotizacion[0]->monto_embalaje;
            $pedido->monto_envio            =   $cotizacion[0]->monto_envio;
            $pedido->porcentaje_descuento   =   $cotizacion[0]->porcentaje_descuento;
            $pedido->monto_descuento        =   $cotizacion[0]->monto_descuento;
            $pedido->fecha_registro         =   Carbon::now()->format('Y-m-d');
            $pedido->cotizacion_id          =   $cotizacion[0]->id;
            $pedido->sede_id                =   $cotizacion[0]->sede_id;
            $pedido->almacen_id             =   $cotizacion[0]->almacen_id;
            $pedido->save();

            //=========== CREAR DETALLE DEL PEDIDO ========
            foreach ($detalle_cotizacion as $item) {
                $detalle_pedido                 =   new PedidoDetalle();
                $detalle_pedido->pedido_id      =   $pedido->id;
                $detalle_pedido->almacen_id     =   $cotizacion[0]->almacen_id;
                $detalle_pedido->producto_id    =   $item->producto_id;
                $detalle_pedido->color_id       =   $item->color_id;
                $detalle_pedido->talla_id       =   $item->talla_id;

                //====== OBTENIENDO DATOS DEL PRODUCTO ======
                $producto   =   DB::select('select 
                                p.codigo,
                                p.nombre,
                                p.modelo_id 
                                from productos as p
                                where 
                                p.id = ?',
                                [$item->producto_id]);
                
                $detalle_pedido->producto_codigo = $producto[0]->codigo;
                $detalle_pedido->unidad          = 'NIU';
                $detalle_pedido->producto_nombre = $producto[0]->nombre;

                //====== OBTENIENDO DATOS DEL COLOR =========
                $color      =   DB::select('select 
                                c.descripcion 
                                from colores as c
                                where c.id = ?',
                                [$item->color_id]);

                $detalle_pedido->color_nombre   =  $color[0]->descripcion;

                //======= OBTENIENDO DATOS DE LA TALLA =======
                $talla      =   DB::select('select 
                                t.descripcion 
                                from tallas as t
                                where t.id = ?',
                                [$item->talla_id]);

                $detalle_pedido->talla_nombre   =   $talla[0]->descripcion;

                //===== OBTENIENDO DATOS DEL MODELO ======
                $modelo     =   DB::select('select 
                                m.descripcion 
                                from modelos as m 
                                where m.id = ?',
                                [$producto[0]->modelo_id]);

                $detalle_pedido->modelo_nombre = $modelo[0]->descripcion;
                
                $detalle_pedido->cantidad               =   $item->cantidad;
                $detalle_pedido->precio_unitario        =   $item->precio_unitario;
                $detalle_pedido->importe                =   $item->importe;
                $detalle_pedido->porcentaje_descuento   =   $item->porcentaje_descuento;
                $detalle_pedido->precio_unitario_nuevo  =   $item->precio_unitario_nuevo;
                $detalle_pedido->importe_nuevo          =   $item->importe_nuevo;
                $detalle_pedido->monto_descuento        =   $item->monto_descuento;
                $detalle_pedido->cantidad_atendida      =   0;
                $detalle_pedido->cantidad_pendiente     =   $item->cantidad;
                $detalle_pedido->save();
            }

        
            DB::commit();
            
            return response()->json(['success' => true ,
            'message' => "SE HA GENERADO EL PEDIDO N° ". $pedido->pedido_nro ]);


        } catch (\Throwable $th) {
            
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    
    }

    public function getProductos(Request $request){
        try {
            
            $categoria_id   =   $request->get('categoria_id');
            $marca_id       =   $request->get('marca_id');
            $modelo_id      =   $request->get('modelo_id');


            $query = 'SELECT p.id, p.nombre 
                    FROM productos AS p 
                    WHERE p.estado = "ACTIVO"';

            $params = [];

            if ($modelo_id) {
                $query .= ' AND p.modelo_id = ?';
                $params[] = $modelo_id;
            }

            if ($marca_id) {
                $query .= ' AND p.marca_id = ?';
                $params[] = $marca_id;
            }

            if ($categoria_id) {
                $query .= ' AND p.categoria_id = ?';
                $params[] = $categoria_id;
            }

            $productos = DB::select($query, $params);

            return response()->json(['success' => true,'productos'=>$productos]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getColoresTallas($almacen_id,$producto_id){
       
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
                                    c.descripcion AS color_nombre
                                FROM 
                                    producto_colores AS pc 
                                    inner join productos as p on p.id = pc.producto_id
                                    inner join colores as c on c.id = pc.color_id
                                WHERE 
                                    pc.almacen_id  = ?
                                    AND pc.producto_id = ? 
                                    AND p.estado = "ACTIVO" and c.estado = "ACTIVO" ',
                        [$almacen_id,$producto_id]);

            $stocks =   DB::select('select  
                        pct.producto_id,
                        pct.color_id,
                        pct.talla_id,
                        pct.stock,
                        pct.stock_logico, 
                        t.descripcion as talla_nombre
                        from producto_color_tallas as pct
                        inner join productos as p on p.id = pct.producto_id
                        inner join colores as c on c.id = pct.color_id 
                        inner join tallas as t on t.id = pct.talla_id
                        where 
                        p.estado = "ACTIVO" 
                        and c.estado = "ACTIVO" 
                        and t.estado = "ACTIVO"
                        and pct.almacen_id = ?
                        AND p.id = ?',
                        [$almacen_id,$producto_id]);

            $tallas =   Talla::where('estado','ACTIVO')->orderBy('id')->get();   

            $producto_color_tallas  =   null;
            $_precios_venta          =   null;
            if(count($colores) > 0){
                $producto_color_tallas  =   $this->formatearColoresTallas($colores,$stocks,$tallas);
            }
            if(count($precios_venta)!== 0){
                $_precios_venta   =   $precios_venta[0];
            }

            return response()->json(['success' => true,
            'producto_color_tallas'     =>  $producto_color_tallas,
            'precios_venta'             =>  $_precios_venta,
            'message'                   =>  'TALLAS OBTENIDAS']);

        } catch (\Throwable $th) {
    
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function formatearColoresTallas($colores, $stocks, $tallas)
    {
        $producto = [];

        // Verifica si $colores no está vacío
        if (count($colores) > 0) {
            $producto['id'] = $colores[0]->producto_id;
            $producto['nombre'] = $colores[0]->producto_nombre;
        } else {
            // Maneja el caso cuando $colores está vacío
            $producto['id'] = null;
            $producto['nombre'] = null;
        }

        $lstColores = [];

        //======== RECORRIENDO COLORES =======
        foreach ($colores as $color) {
            $item_color = [];
            $item_color['id'] = $color->color_id;
            $item_color['nombre'] = $color->color_nombre;

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

    public function getProductoBarCode($barcode){
        try{

            $producto   =   DB::select('select 
                            cb.*,
                            c.descripcion as color_nombre,
                            t.descripcion as talla_nombre,
                            p.id,
                            p.nombre as nombre,
                            p.categoria_id,
                            p.marca_id,
                            p.modelo_id,
                            p.precio_venta_1
                            from codigos_barra as cb
                            inner join colores as c on c.id = cb.color_id
                            inner join tallas as t on t.id = cb.talla_id
                            inner join productos as p on p.id = cb.producto_id
                            where cb.codigo_barras = ?',[$barcode]);

            if(count($producto) === 0){
                throw new Exception("NO SE ENCONTRÓ NINGÚN PRODUCTO CON ESTE CÓDIGO DE BARRAS!!!");
            }


            return response()->json(['success'=>true,'producto'=> $producto[0] ]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=> $th->getMessage() ]);
        }
    }

    public function devolverCantidades(Request $request){

        DB::beginTransaction();
        try {
            $cotizacion_detalle =   CotizacionDetalle::where('cotizacion_id',$request->get('cotizacion_id'))->get();
           
            foreach ($cotizacion_detalle as $item) {

                //===== DEVOLVER STOCK LÓGICO ===========
                DB::update('UPDATE producto_color_tallas 
                SET stock_logico = stock_logico + ? 
                WHERE 
                almacen_id = ?
                AND producto_id = ? 
                AND color_id = ? 
                AND talla_id = ?', 
                [$item->cantidad,
                $item->almacen_id,
                $item->producto_id, 
                $item->color_id, 
                $item->talla_id]);

            }
           
            DB::commit();
            return response()->json(['success'=>true,'message'=>'CANTIDAD DEVUELTA CON ÉXITO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,$th->getMessage()]);
        }
    }

}
