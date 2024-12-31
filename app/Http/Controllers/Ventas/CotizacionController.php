<?php
namespace App\Http\Controllers\Ventas;

use App\Almacenes\Categoria;
use App\Almacenes\LoteProducto;
use App\Almacenes\Marca;
use App\Almacenes\Producto;
use App\Almacenes\Modelo;
use App\Almacenes\Talla;
use App\Http\Controllers\Controller;
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
use Exception;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('ventas.cotizaciones.index');
    }

    public function getTable()
    {
        //$cotizaciones = Cotizacion::where('estado', '<>', 'ANULADO')->orderBy('id', 'desc')->get();

        $cotizaciones   =   DB::select('select co.id,e.razon_social as empresa,cl.nombre as cliente,
                            co.created_at,u.usuario,
                            co.total_pagar,co.estado,
                            IF(cd.cotizacion_venta IS NULL, "0", "1") as documento, 
                            IF(p.id is null,"-",concat("PE-",p.id)) as pedido_id,
                            IF(cd.cotizacion_venta IS NULL,"-",concat(cd.serie,"-",cd.correlativo)) as documento_cod
                            from cotizaciones  as co
                            left join cotizacion_documento as cd on cd.cotizacion_venta = co.id
                            left join pedidos as p on p.cotizacion_id = co.id
                            inner join empresas as e on e.id=co.empresa_id
                            inner join clientes as cl on cl.id=co.cliente_id
                            inner join users as u on u.id = co.user_id
                            order by co.id desc',[Auth::user()->id]);
        
        //$cotizaciones = collect($cotizaciones);
        // $coleccion = collect([]);
        // foreach($cotizaciones as $cotizacion) {
        //     $coleccion->push([
        //         'id' => $cotizacion->id,
        //         'empresa' => $cotizacion->empresa->razon_social,
        //         'cliente' => $cotizacion->cliente->nombre,
        //         'fecha_documento' => Carbon::parse($cotizacion->fecha_documento)->format( 'd/m/Y'),
        //         'total_pagar' => $cotizacion->total_pagar,
        //         'estado' => $cotizacion->estado,
        //         'documento' => $cotizacion->documento ? '1' : '0'
        //     ]);
        // }
        return DataTables::of($cotizaciones)->toJson();
    }

    public function create()
    {
        $tipos_documento    =   tipos_documento();
        $departamentos      =   departamentos();
        $tipo_clientes      =   tipo_clientes();

        $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
        $clientes           =   Cliente::where('estado', 'ACTIVO')->get();
        $fecha_hoy          =   Carbon::now()->toDateString();
        $condiciones        =   Condicion::where('estado','ACTIVO')->get();
        $modelos            =   Modelo::where('estado','ACTIVO')->get();
        $categorias         =   Categoria::where('estado','ACTIVO')->get();
        $marcas             =   Marca::where('estado','ACTIVO')->get();
        $tallas             =   Talla::where('estado','ACTIVO')->get();
       
        $vendedor_actual    =   DB::select('select c.id from user_persona as up
                                inner join colaboradores  as c
                                on c.persona_id=up.persona_id
                                where up.user_id = ?',[Auth::id()]);

        $vendedor_actual    =   $vendedor_actual?$vendedor_actual[0]->id:null;
        
        
        return view('ventas.cotizaciones.create', compact('vendedor_actual','tallas','modelos','empresas',
         'clientes', 'fecha_hoy', 'condiciones','tipos_documento','departamentos','tipo_clientes','categorias','marcas'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $productos = json_decode($request->input('productos_tabla')[0]);
        
        $rules = [
            'empresa' => 'required',
            'cliente' => 'required',
            'condicion_id' => 'required',
            'fecha_documento' => 'required',
            'fecha_atencion' => 'nullable',
            // 'igv' => 'required_if:igv_check,==,on|numeric|digits_between:1,3',
        ];

        $message = [
            'empresa.required' => 'El campo Empresa es obligatorio',
            'cliente.required' => 'El campo Cliente es obligatorio',
            'condicion_id.required' => 'El campo condicion es obligatorio',
            'moneda' => 'El campo Moneda es obligatorio',
            'fecha_documento.required' => 'El campo Fecha de Documento es obligatorio',
            // // 'igv.required_if' => 'El campo Igv es obligatorio.',
            // // 'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
            // // 'igv.numeric' => 'El campo Igv debe se numérico.',
        ];

        Validator::make($data, $rules, $message)->validate();

        // $igv = $request->get('igv') && $request->get('igv_check') == "on" ? (float) $request->get('igv') : 18;
        // $total = (float) $request->get('monto_total');
        // $sub_total = $total / (1 + ($igv/100));
        // $total_igv = $total - $sub_total;


        //======= CALCULANDO MONTOS ========
        $monto_subtotal     =   0.0;
        $monto_embalaje     =   $request->get('monto_embalaje')??0;
        $monto_envio        =   $request->get('monto_envio')??0;
        $monto_total        =   0.0;
        $monto_igv          =   0.0;
        $monto_total_pagar  =   0.0;
        $monto_descuento    =   $request->get('monto_descuento')??0;

        foreach ($productos as $producto) {
            if( floatval($producto->porcentaje_descuento) == 0){
                $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta);
            }else{
                $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta_nuevo);
            }
        }

        $monto_total_pagar      =   $monto_subtotal+$monto_embalaje+$monto_envio;
        $monto_total            =   $monto_total_pagar/1.18;
        $monto_igv              =   $monto_total_pagar-$monto_total;
        $porcentaje_descuento   = ($monto_descuento*100)/($monto_total_pagar);


        $cotizacion = new Cotizacion();
        $cotizacion->empresa_id         = $request->get('empresa');
        $cotizacion->cliente_id         = $request->get('cliente');
        $cotizacion->condicion_id       = $request->get('condicion_id');
        //$cotizacion->vendedor_id = $request->get('vendedor');
        $cotizacion->vendedor_id        =   $request->get('vendedor');
        $cotizacion->moneda             = 4;
        $cotizacion->fecha_documento    = $request->get('fecha_documento');
        $cotizacion->fecha_atencion     = $request->get('fecha_atencion');

        $cotizacion->sub_total              = $monto_subtotal;
        $cotizacion->monto_embalaje         = $monto_embalaje;
        $cotizacion->monto_envio            = $monto_envio;
        $cotizacion->total_igv              = $monto_igv;
        $cotizacion->total                  = $monto_total;
        $cotizacion->total_pagar            = $monto_total_pagar;  
        $cotizacion->monto_descuento        = $monto_descuento;
        $cotizacion->porcentaje_descuento   = $porcentaje_descuento;

        $cotizacion->user_id = Auth::id();
        //$cotizacion->igv = $request->get('igv');
        $cotizacion->igv = "18";
        //if ($request->get('igv_check') == "on") {
        $cotizacion->igv_check = "1";
        //}
        $cotizacion->save();

        //Llenado de los Productos
        //$productosJSON = $request->get('productos_tabla');
        //$productotabla = json_decode($productosJSON[0]);
        foreach ($productos as $producto) {
            //==== CALCULANDO MONTOS PARA EL DETALLE ====
            $importe =  floatval($producto->cantidad) * floatval($producto->precio_venta);
            $precio_venta   =   $producto->porcentaje_descuento == 0?$producto->precio_venta:$producto->precio_venta_nuevo;

            CotizacionDetalle::create([
                'cotizacion_id' => $cotizacion->id,
                'producto_id' => $producto->producto_id,
                'color_id'  => $producto->color_id,
                'talla_id' => $producto->talla_id,
                'cantidad' => $producto->cantidad,
                'precio_unitario' => $producto->precio_venta,
                'importe' => $importe,
                'precio_unitario_nuevo'     =>  floatval($precio_venta),
                'porcentaje_descuento'      =>  floatval($producto->porcentaje_descuento),
                'monto_descuento'           =>  floatval($importe)*floatval($producto->porcentaje_descuento)/100,
                'importe_nuevo'             =>  floatval($precio_venta) * floatval($producto->cantidad),  

                //'descuento'=> $producto->descuento,
                //'dinero'=> $producto->dinero,
                //'valor_unitario' => $producto->valor_unitario,
                //'precio_unitario' => $producto->precio_unitario,
                //'precio_inicial' => $producto->precio_inicial,
                //'precio_nuevo' => $producto->precio_nuevo,
                //'cantidad' => $producto->cantidad,
                //'valor_venta' => $producto->valor_venta,
            ]);
        }

        //Registro de actividad
        $descripcion = "SE AGREGÓ LA COTIZACION CON LA FECHA: ". Carbon::parse($cotizacion->fecha_documento)->format('d/m/y');
        $gestion = "COTIZACION";
        crearRegistro($cotizacion, $descripcion , $gestion);

        Session::flash('success','Cotización creada.');
        return redirect()->route('ventas.cotizacion.index')->with('guardar', 'success');
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

        $cotizacion = Cotizacion::findOrFail($id);
        $empresas = Empresa::where('estado', 'ACTIVO')->get();
        $clientes = Cliente::where('estado', 'ACTIVO')->get();
        $condiciones = Condicion::where('estado','ACTIVO')->get();
        $fecha_hoy = Carbon::now()->toDateString();
        //$lotes = LoteProducto::where('estado', '1')->distinct()->get(['producto_id']);
        // $lotes = Producto::where('estado','ACTIVO')->get();
        $detalles = CotizacionDetalle::where('cotizacion_id',$id)->where('estado', 'ACTIVO')
                    ->with('producto', 'color', 'talla')->get();
        $modelos = Modelo::where('estado','ACTIVO')->get();
        $tallas = Talla::where('estado','ACTIVO')->get();


        return view('ventas.cotizaciones.edit', [
            'cotizacion' => $cotizacion,
            'empresas' => $empresas,
            'clientes' => $clientes,
            'fecha_hoy' => $fecha_hoy,
            'condiciones' => $condiciones,
            // 'lotes' => $lotes,
            'detalles' => $detalles,
            'modelos' => $modelos,
            'tallas' => $tallas,
            'tipos_documento' => $tipos_documento,
            'departamentos' => $departamentos,
            'tipo_clientes' => $tipo_clientes
        ]);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        $productos = json_decode($request->input('productos_tabla')[0]);
        
       
        $rules = [
            'empresa' => 'required',
            'cliente' => 'required',
            'condicion_id' => 'required',
            'fecha_documento' => 'required',
            'fecha_atencion' => 'nullable',
            // 'igv' => 'required_if:igv_check,==,on|numeric|digits_between:1,3',
        ];

        $message = [
            'empresa.required' => 'El campo Empresa es obligatorio',
            'cliente.required' => 'El campo Cliente es obligatorio',
            'condicion_id.required' => 'El campo condicion es obligatorio',
            'moneda' => 'El campo Moneda es obligatorio',
            'fecha_documento.required' => 'El campo Fecha de Documento es obligatorio',
            // 'igv.required_if' => 'El campo Igv es obligatorio.',
            // 'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
            // 'igv.numeric' => 'El campo Igv debe se numérico.',
        ];

        Validator::make($data, $rules, $message)->validate();

        // $igv = $request->get('igv') && $request->get('igv_check') == "on" ? (float) $request->get('igv') : 18;
        // $total = (float) $request->get('monto_total');
        // $sub_total = $total / (1 + ($igv/100));
        // $total_igv = $total - $sub_total;

        //======= CALCULANDO MONTOS ========
        $monto_subtotal     =   0.0;
        $monto_embalaje     =   $request->get('monto_embalaje')??0;
        $monto_envio        =   $request->get('monto_envio')??0;
        $monto_total        =   0.0;
        $monto_igv          =   0.0;
        $monto_total_pagar  =   0.0;
        $monto_descuento    =   $request->get('monto_descuento')??0;
     
        foreach ($productos as $producto) {
            if( floatval($producto->porcentaje_descuento) == 0){
                $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta);
            }else{
                     $monto_subtotal +=  ($producto->cantidad * $producto->precio_venta_nuevo);
            }
        }
     
        $monto_total_pagar      =   $monto_subtotal+$monto_embalaje+$monto_envio;
        $monto_total            =   $monto_total_pagar/1.18;
        $monto_igv              =   $monto_total_pagar-$monto_total;
        $porcentaje_descuento   =   ($monto_descuento*100)/($monto_total_pagar+$monto_descuento);
        
             
        $cotizacion =  Cotizacion::findOrFail($id);
        $cotizacion->empresa_id = $request->get('empresa');
        $cotizacion->cliente_id = $request->get('cliente');
        $cotizacion->condicion_id = $request->get('condicion_id');
        //$cotizacion->vendedor_id = $request->get('vendedor');
        $cotizacion->vendedor_id    =   $request->get('vendedor');
        $cotizacion->fecha_documento = $request->get('fecha_documento');
        $cotizacion->fecha_atencion = $request->get('fecha_atencion');

        $cotizacion->sub_total              = $monto_subtotal;
        $cotizacion->monto_embalaje         = $monto_embalaje;
        $cotizacion->monto_envio            = $monto_envio;
        $cotizacion->total_igv              = $monto_igv;
        $cotizacion->total                  = $monto_total;
        $cotizacion->total_pagar            = $monto_total_pagar;  
        $cotizacion->monto_descuento        = $monto_descuento;
        $cotizacion->porcentaje_descuento   = $porcentaje_descuento;

        $cotizacion->user_id = Auth::id();
         //$cotizacion->igv = $request->get('igv');
         $cotizacion->igv = "18";
         //if ($request->get('igv_check') == "on") {
            $cotizacion->igv_check = "1";
         //}

        $cotizacion->update();

        //$productosJSON = $request->get('productos_tabla');
        //$productotabla = json_decode($productosJSON[0]);
        if ($productos) {
            CotizacionDetalle::where('cotizacion_id', $id)->delete();

            foreach ($productos as $producto) {
                //==== CALCULANDO MONTOS PARA EL DETALLE ====
                $importe =  floatval($producto->cantidad) * floatval($producto->precio_venta);
                $precio_venta   =   $producto->porcentaje_descuento == 0?$producto->precio_venta:$producto->precio_venta_nuevo;

                CotizacionDetalle::create([
                    'cotizacion_id'             => $cotizacion->id,
                    'producto_id'               => $producto->producto_id,
                    'color_id'                  => $producto->color_id,
                    'talla_id'                  => $producto->talla_id,
                    'cantidad'                  => $producto->cantidad,
                    'precio_unitario'           => $producto->precio_venta,
                    'importe'                   => $importe,
                    'precio_unitario_nuevo'     =>  floatval($precio_venta),
                    'porcentaje_descuento'      =>  floatval($producto->porcentaje_descuento),
                    'monto_descuento'           =>  floatval($importe)*floatval($producto->porcentaje_descuento)/100,
                    'importe_nuevo'             =>  floatval($precio_venta) * floatval($producto->cantidad),  
                ]);
            }
        }

        //Registro de actividad
        $descripcion = "SE MODIFICÓ LA COTIZACION CON LA FECHA: ". Carbon::parse($cotizacion->fecha_documento)->format('d/m/y');
        $gestion = "COTIZACION";
        modificarRegistro($cotizacion, $descripcion , $gestion);

        Session::flash('success','Cotización modificada.');
        return redirect()->route('ventas.cotizacion.index')->with('modificar', 'success');


        //ELIMINAR DOCUMENTO DE ORDEN DE COMPRA SI EXISTE
        // $documento = Documento::where('cotizacion_venta',$id)->where('estado','!=','ANULADO')->first();
        // if ($documento) {
        //     $documento->estado = 'ANULADO';
        //     $documento->update();

        //     $detalles = Detalle::where('documento_id',$id)->get();
        //     foreach ($detalles as $detalle) {
        //         $lote = LoteProducto::find($detalle->lote_id);
        //         $cantidad = $lote->cantidad + $detalle->cantidad;
        //         $lote->cantidad = $cantidad;
        //         $lote->cantidad_logica = $cantidad;
        //         $lote->update();
        //         //ANULAMOS EL DETALLE
        //         $detalle->estado = "ANULADO";
        //         $detalle->update();
        //     }

        //     Session::flash('success','Cotización modificada y documento eliminado.');
        //     return redirect()->route('ventas.cotizacion.index')->with('modificar', 'success');

        // }else{
        //     Session::flash('success','Cotización modificada.');
        //     return redirect()->route('ventas.cotizacion.index')->with('modificar', 'success');
        // }
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
            // return view('ventas.cotizaciones.index',[
            //     'id' => $id
            // ]);
        }else{
            //REDIRECCIONAR AL DOCUMENTO DE VENTA

            return redirect()->route('ventas.documento.create',['cotizacion'=>$id]);
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
                                    c.descripcion AS color_nombre
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
            $producto['id'] = $colores[0]->producto_id;
            $producto['nombre'] = $colores[0]->producto_nombre;
        } else {
            // Maneja el caso cuando $colores está vacío
            $producto['id'] = null;
            $producto['nombre'] = null;
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
