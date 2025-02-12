<?php
namespace App\Http\Controllers\Ventas;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use App\Almacenes\Color;
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
use App\User;
use Exception;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('ventas.cotizaciones.index');
    }

    public function getTable()
    {

        $cotizaciones   =   DB::select('select 
                            co.id,
                            e.razon_social as empresa,
                            cl.nombre as cliente,
                            co.created_at,
                            u.usuario,
                            co.total_pagar,
                            co.estado,
                            IF(cd.cotizacion_venta IS NULL, "0", "1") as documento, 
                            IF(p.id is null,"-",concat("PE-",p.id)) as pedido_id,
                            IF(cd.cotizacion_venta IS NULL,"-",concat(cd.serie,"-",cd.correlativo)) as documento_cod
                            from cotizaciones  as co
                            left join cotizacion_documento as cd on cd.cotizacion_venta = co.id
                            left join pedidos as p on p.cotizacion_id = co.id
                            inner join empresas as e on e.id=co.empresa_id
                            inner join clientes as cl on cl.id = co.cliente_id
                            inner join users as u on u.id = co.registrador_id
                            order by co.id desc',[Auth::user()->id]);
    
        return DataTables::of($cotizaciones)->toJson();
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

        $almacenes          =   Almacen::where('sede_id',$sede_id)->where('estado','ACTIVO')->get();
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
        $almacenes      =   Almacen::where('estado','ACTIVO')->get();
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
        $cotizacion = Cotizacion::findOrFail($id);
        $tallas = Talla::all();
        $nombre_completo = $cotizacion->user->user->persona->apellido_paterno.' '.$cotizacion->user->user->persona->apellido_materno.' '.$cotizacion->user->user->persona->nombres;
        $igv = '';
        $tipo_moneda = '';
        $detalles = $cotizacion->detalles->where('estado', 'ACTIVO');
        $empresa = Empresa::first();
        $paper_size = array(0,0,360,360);

        $mostrar_cuentas =   DB::select('select c.propiedad 
                            from configuracion as c 
                            where c.slug = "MCB"')[0]->propiedad;

        $detalles = $this->formatearArrayDetalle($detalles);

        $vendedor_nombre = DB::select('SELECT CONCAT(p.nombres, " ", p.apellido_paterno, " ", p.apellido_materno) AS nombre_completo
        FROM user_persona AS up 
        INNER JOIN personas AS p ON p.id = up.persona_id
        WHERE up.user_id = ?', [$cotizacion->user_id])[0]->nombre_completo;


        $pdf = PDF::loadview('ventas.cotizaciones.reportes.detalle_nuevo',[
            'cotizacion' => $cotizacion,
            'nombre_completo' => $nombre_completo,
            'detalles' => $detalles,
            'empresa' => $empresa,
            'tallas' => $tallas,
            'vendedor_nombre' => $vendedor_nombre,
            'mostrar_cuentas'   =>  $mostrar_cuentas
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

            $tipo_venta         =   DB::select('select td.* from tabladetalles as td
                                    where td.id = ?',[$request->get('tipo_venta')])[0];

            //======= VALIDAR QUE EL DOCUMENTO VENTA ESTÉ ACTIVO =======
            DocumentoController::comprobanteActivo($cotizacion->sede_id,$tipo_venta);

            //======== OBTENIENDO LEYENDA ======
            $legenda                =   UtilidadesController::convertNumeroLetras($cotizacion->total_pagar);


            $cotizacion_detalle =   CotizacionDetalle::where('cotizacion_id',$cotizacion->id)->get();

            $datos_correlativo  =   DocumentoController::getCorrelativo($tipo_venta,$cotizacion->sede_id);
            $condicion          =   Condicion::find($cotizacion->condicion_id);

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

            dd('asdas');
  
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
            $documento->sub_total               =   $cotizacion->monto_subtotal;
            $documento->monto_embalaje          =   $cotizacion->monto_embalaje;  
            $documento->monto_envio             =   $cotizacion->monto_envio;  
            $documento->total                   =   $cotizacion->monto_total;  
            $documento->total_igv               =   $cotizacion->monto_igv;
            $documento->total_pagar             =   $cotizacion->monto_total_pagar;  
            $documento->igv                     =   $cotizacion->igv;
            $documento->monto_descuento         =   $cotizacion->monto_descuento;
            $documento->porcentaje_descuento    =   $cotizacion->porcentaje_descuento;   
            $documento->moneda                  =   1;
  
            //======= SERIE Y CORRELATIVO ======
            $documento->serie       =   $datos_correlativo->serie;
            $documento->correlativo =   $datos_correlativo->correlativo;
  
            $documento->legenda     =   $legenda;
  
              $documento->sede_id         =   $datos_validados->sede_id;
              $documento->almacen_id      =   $datos_validados->almacen->id;
              $documento->almacen_nombre  =   $datos_validados->almacen->descripcion;
  
              $documento->save();



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


    public function generarPedido(Request $request){
        DB::beginTransaction();
        
        try {
            $data   =   $request->get('body');
            $data   =   json_decode($data);
           
            $cotizacion_id  =   $data->cotizacion_id;


            //===== OBTENIENDO DETALLE DE LA COTIZACIÓN ===========
            $cotizacion =   DB::select('select * from cotizaciones as c
                            where c.id = ? and c.estado != "ANULADO" and c.estado != "VENCIDA"'
                            ,[$cotizacion_id]);

            $detalle_cotizacion =   DB::select('select * from cotizacion_detalles as cd 
                                    where cd.cotizacion_id = ? and cd.estado = "ACTIVO"',
                                    [$cotizacion_id]);

            //======== CREAR PEDIDO =========
            $pedido             =   new Pedido();
            $pedido->cliente_id = $cotizacion[0]->cliente_id;

            //======= OBTENIENDO NOMBRE DEL CLIENTE =======
            $cliente = DB::select('select c.nombre from clientes as c 
                        where c.estado="ACTIVO" and c.id=?',[$cotizacion[0]->cliente_id]);
            
            $pedido->cliente_nombre =   $cliente[0]->nombre;   
            $pedido->empresa_id     =   $cotizacion[0]->empresa_id;
            
            //======== OBTENIENDO NOMBRE DE LA EMPRESA ========
            $empresa    =   DB::select('select e.razon_social from empresas as e
                            where e.estado="ACTIVO" and e.id = ?',[$cotizacion[0]->empresa_id]);
            $pedido->empresa_nombre =   $empresa[0]->razon_social;
            $pedido->user_id        =   $cotizacion[0]->user_id;

            //====== OBTENIENDO NOMBRE DEL USUARIO =====
            $usuario    =   DB::select('select u.usuario from users as u 
                            where u.estado = "ACTIVO" and u.id=?',
                            [$cotizacion[0]->user_id]);
            $pedido->user_nombre    =   $usuario[0]->usuario;
            $pedido->condicion_id   =   $cotizacion[0]->condicion_id;
            $pedido->moneda         =   $cotizacion[0]->moneda;
            //======= OBTENIENDO NRO DEL PEDIDO =======
            $cantidad_pedidos   =   Pedido::count();
            $pedido->pedido_nro =   $cantidad_pedidos+1;

            $pedido->sub_total      =   $cotizacion[0]->sub_total;
            $pedido->total          =   $cotizacion[0]->total;
            $pedido->total_igv      =   $cotizacion[0]->total_igv;
            $pedido->total_pagar    =   $cotizacion[0]->total_pagar;
            $pedido->monto_embalaje =   $cotizacion[0]->monto_embalaje;
            $pedido->monto_envio    =   $cotizacion[0]->monto_envio;
            $pedido->porcentaje_descuento   =   $cotizacion[0]->porcentaje_descuento;
            $pedido->monto_descuento        =   $cotizacion[0]->monto_descuento;
            $pedido->fecha_registro         =   Carbon::now()->format('Y-m-d');
            $pedido->cotizacion_id          =   $cotizacion[0]->id;
            $pedido->save();

            //=========== CREAR DETALLE DEL PEDIDO ========
            foreach ($detalle_cotizacion as $item) {
                $detalle_pedido =               new PedidoDetalle();
                $detalle_pedido->pedido_id      =   $pedido->id;
                $detalle_pedido->producto_id    =   $item->producto_id;
                $detalle_pedido->color_id       =   $item->color_id;
                $detalle_pedido->talla_id       =   $item->talla_id;

                //====== OBTENIENDO DATOS DEL PRODUCTO ======
                $producto   =   DB::select('select p.codigo,p.nombre,p.modelo_id 
                                from productos as p
                                where p.id = ?',[$item->producto_id]);
                
                $detalle_pedido->producto_codigo = $producto[0]->codigo;
                $detalle_pedido->unidad          = 'NIU';
                $detalle_pedido->producto_nombre = $producto[0]->nombre;

                //====== OBTENIENDO DATOS DEL COLOR =========
                $color      =   DB::select('select c.descripcion from colores as c
                                where c.id = ?',[$item->color_id]);

                $detalle_pedido->color_nombre   =  $color[0]->descripcion;

                //======= OBTENIENDO DATOS DE LA TALLA =======
                $talla      =   DB::select('select t.descripcion from tallas as t
                                where t.id = ?',[$item->talla_id]);

                $detalle_pedido->talla_nombre   =   $talla[0]->descripcion;

                //===== OBTENIENDO DATOS DEL MODELO ======
                $modelo     =   DB::select('select m.descripcion from modelos as m 
                                where m.id = ?',[$producto[0]->modelo_id]);
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
            dd($th->getMessage());
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
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
        try {

            $producto   =   DB::select('select 
                            pct.*,
                            c.descripcion as color_nombre,
                            t.descripcion as talla_nombre,
                            p.id,
                            p.nombre as nombre,
                            p.categoria_id,
                            p.marca_id,
                            p.modelo_id,
                            p.precio_venta_1
                            from producto_color_tallas as pct
                            inner join colores as c on c.id = pct.color_id
                            inner join tallas as t on t.id = pct.talla_id
                            inner join productos as p on p.id = pct.producto_id
                            where pct.codigo_barras = ?',[$barcode]);

            if(count($producto) === 0){
                throw new Exception("NO SE ENCONTRÓ NINGÚN PRODUCTO CON ESTE CÓDIGO DE BARRAS!!!");
            }


            return response()->json(['success'=>true,'producto'=> $producto[0] ]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=> $th->getMessage() ]);
        }
    }

}
