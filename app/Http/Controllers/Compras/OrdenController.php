<?php

namespace App\Http\Controllers\Compras;

use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
use App\Almacenes\Modelo;
use App\Compras\CuentaProveedor;
use App\Compras\Detalle;
use App\Compras\Documento\Documento;
use App\Compras\Enviado;
use App\Compras\Orden;
use App\Compras\Proveedor;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Movimientos\MovimientoAlmacen;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OrdenController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','orden.index');
        return view('compras.ordenes.index');
    }

    public function getOrder(){
        $this->authorize('haveaccess','orden.index');
        //Cambiar a estado pendiente si la fecha de la de hoy
        DB::table('ordenes')->where('estado','!=','ANULADO')
        ->where('estado','!=','CONCRETADA')
        ->chunkById(100,function ($ordenes) {
            $fecha_hoy = Carbon::now();

            foreach ($ordenes as $orden) {

                if ($orden->fecha_entrega <= $fecha_hoy->toDateString()) {
                    DB::table('ordenes')
                    ->where('id', $orden->id)
                    ->update(['estado' => 'PENDIENTE']);
                }else{
                    DB::table('ordenes')
                    ->where('id', $orden->id)
                    ->update(['estado' => 'VIGENTE']);
                }

            }
        });
        $ordenes = Orden::where('estado','!=','CONCRETADA')->where('estado','!=','ANULADO')->get();

        $coleccion = collect([]);
        foreach($ordenes as $orden){


            $detalles = Detalle::where('orden_id',$orden->id)->get();
            $subtotal = 0;
            $igv = '';
            $tipo_moneda = '';

            foreach($detalles as $detalle){
                $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
            }

            foreach(tipos_moneda() as $moneda){
                if ($moneda->descripcion == $orden->moneda) {
                    $tipo_moneda= $moneda->simbolo;
                }
            }

            if (!$orden->igv) {
                $igv = $subtotal * 0.18;
                $total = $subtotal + $igv;
                $decimal_total = number_format($total, 2, '.', '');
            }else{
                $calcularIgv = $orden->igv/100;
                $base = $subtotal / (1 + $calcularIgv);
                $nuevo_igv = $subtotal - $base;
                $decimal_total = number_format($subtotal, 2, '.', '');
            }


            // CALCULAR ACUENTA EN MONEDA
            $acuenta = 0;

            //CALCULAR SALDO
            $saldo = $decimal_total - $acuenta;

            //CAMBIAR ESTADO DE LA ORDEN A PAGADA

            if ($saldo == 0.0) {
                $orden->estado = "PAGADA";
                $orden->update();
            }



            $coleccion->push([
                'id' => $orden->id,
                'proveedor' => $orden->proveedor->descripcion,
                'fecha_emision' =>  Carbon::parse($orden->fecha_emision)->format( 'd/m/Y'),
                'fecha_entrega' =>  Carbon::parse($orden->fecha_entrega)->format( 'd/m/Y'),
                'estado' => $orden->estado,
                'total' => $tipo_moneda.' '.number_format($decimal_total, 2, '.', ''),
            ]);
        }

        return DataTables::of($coleccion)->toJson();
    }

    public function create(Request $request)
    {
        $this->authorize('haveaccess','orden.index');
        $empresas       = Empresa::where('estado','ACTIVO')->get();
        $proveedores    = Proveedor::where('estado','ACTIVO')->get();
        $productos      = Producto::where('estado','ACTIVO')->get();
        $presentaciones =  presentaciones();
        $modos          =  modo_compra();
        $fecha_hoy      = Carbon::now()->toDateString();
        $monedas        =  tipos_moneda();
        $condiciones    = Condicion::where('estado','ACTIVO')->get();

        $tallas         =   Talla::where('estado','ACTIVO')->get();
        $modelos        =   Modelo::where('estado','ACTIVO')->get();

        return view('compras.ordenes.create',[
            'empresas' => $empresas,
            'proveedores' => $proveedores,
            'productos' => $productos,
            'presentaciones' => $presentaciones,
            'condiciones' => $condiciones,
            'fecha_hoy' => $fecha_hoy,
            'modos' => $modos,
            'monedas' => $monedas,
            'tallas'    =>  $tallas,
            'modelos'   =>  $modelos
        ]);
    }


    public function edit($id)
    {
        $this->authorize('haveaccess','orden.index');
        $empresas = Empresa::where('estado','ACTIVO')->get();
        $detalles = Detalle::where('orden_id',$id)->get();
        $proveedores = Proveedor::where('estado','ACTIVO')->get();
        $orden = Orden::findOrFail($id);
        $condiciones = Condicion::where('estado','ACTIVO')->get();

        $documento = Documento::where('orden_compra',$id)->where('estado','!=','ANULADO')->first();

        if ($documento) {
            $aviso = $documento->id;
        }

        $productos = Producto::where('estado','ACTIVO')->get();
        $presentaciones =  presentaciones();
        $modos =  modo_compra();
        $monedas =  tipos_moneda();
        $fecha_hoy = Carbon::now()->toDateString();

        if($documento){
            return view('compras.ordenes.edit',[
                'empresas' => $empresas,
                'proveedores' => $proveedores,
                'orden' => $orden,
                'productos' => $productos,
                'presentaciones' => $presentaciones,
                'condiciones' => $condiciones,
                'fecha_hoy' => $fecha_hoy,
                'detalles' => $detalles,
                'modos' => $modos,
                'monedas' => $monedas,
                'aviso' => $aviso
            ]);
        }else{
            return view('compras.ordenes.edit',[
                'empresas' => $empresas,
                'proveedores' => $proveedores,
                'orden' => $orden,
                'productos' => $productos,
                'presentaciones' => $presentaciones,
                'condiciones' => $condiciones,
                'fecha_hoy' => $fecha_hoy,
                'detalles' => $detalles,
                'modos' => $modos,
                'monedas' => $monedas,
            ]);
        }
    }

    public function store(Request $request){

        $this->authorize('haveaccess','orden.index');

        $data = $request->all();
        $rules = [
            'fecha_emision'=> 'required',
            'fecha_entrega'=> 'required',
            'proveedor_id'=> 'required',
            'condicion_id'=> 'required',
            'observacion' => 'nullable',
            'moneda' => 'nullable',
            'tipo_cambio' => 'nullable|numeric',
            //'igv' => 'required_if:igv_check,==,on|numeric|digits_between:1,3',

        ];
        $message = [
            'fecha_emision.required' => 'El campo Fecha de Emisión es obligatorio.',
            'fecha_entrega.required' => 'El campo Fecha de Entrega es obligatorio.',
            'proveedor_id.required' => 'El campo Proveedor es obligatorio.',
            'condicion_id.required' => 'El campo Condicion es obligatorio.',
            'moneda.required' => 'El campo Moneda es obligatorio.',
            // 'igv.required_if' => 'El campo Igv es obligatorio.',
            // 'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
            // 'igv.numeric' => 'El campo Igv debe se numérico.',
            'tipo_cambio.numeric' => 'El campo Tipo de Cambio debe se numérico.',


        ];
        Validator::make($data, $rules, $message)->validate();

        $orden = new Orden();
        $orden->fecha_emision = Carbon::createFromFormat('d/m/Y', $request->get('fecha_emision'))->format('Y-m-d');
        $orden->fecha_entrega = Carbon::createFromFormat('d/m/Y', $request->get('fecha_entrega'))->format('Y-m-d');

        $orden->sub_total = (float) $request->get('monto_sub_total');
        $orden->total_igv = (float) $request->get('monto_total_igv');
        $orden->total = (float) $request->get('monto_total');

        $condicion = Condicion::find($request->get('condicion_id'));

        $orden->empresa_id = '1';
        $orden->proveedor_id = $request->get('proveedor_id');
        $orden->condicion_id = $request->get('condicion_id');
        $orden->modo_compra = $condicion->descripcion;
        $orden->observacion = $request->get('observacion');
        $orden->moneda = $request->get('moneda');
        $orden->tipo_cambio = $request->get('tipo_cambio');
        $orden->usuario_id = auth()->user()->id;
        // $orden->igv = $request->get('igv');
        $orden->igv = '18';

        if ($request->get('igv_check') == "on") {
            $orden->igv_check = "1";
        };
        $orden->save();

        //Llenado de los productos
        $productosJSON = $request->get('productos_tabla');
        $productotabla = json_decode($productosJSON[0]);

        foreach ($productotabla as $producto) {
            foreach ($producto->tallas as $talla) {
                Detalle::create([
                    'orden_id'      => $orden->id,
                    'producto_id'   => $producto->producto_id,
                    'color_id'      => $producto->color_id,
                    'talla_id'      => $talla->talla_id,
                    'cantidad'      => $talla->cantidad,
                    'precio'        => $producto->precio_unitario,
                ]);            
            } 
        }

        //Registro de actividad
        $descripcion = "SE AGREGÓ LA ORDEN DE COMPRA CON LA FECHA DE EMISION: ". Carbon::parse($orden->fecha_emision)->format('d/m/y');
        $gestion = "ORDEN DE COMPRA";
        crearRegistro($orden, $descripcion , $gestion);

        Session::flash('success','Orden de Compra creada.');
        return redirect()->route('compras.orden.index')->with('guardar', 'success');
    }

    public function update(Request $request, $id){
        $this->authorize('haveaccess','orden.index');
        $data = $request->all();
        $rules = [
            'fecha_emision'=> 'required',
            'fecha_entrega'=> 'required',
            'proveedor_id'=> 'required',
            'condicion_id'=> 'required',
            'observacion' => 'nullable',
            'moneda' => 'nullable',
            'igv' => 'required_if:igv_check,==,on|digits_between:1,3',
        ];

        $message = [
            'fecha_emision.required' => 'El campo Fecha de Emisión es obligatorio.',
            'fecha_entrega.required' => 'El campo Fecha de Entrega es obligatorio.',
            'proveedor_id.required' => 'El campo Proveedor es obligatorio.',
            'condicion_id.required' => 'El campo Condicion es obligatorio.',
            'moneda.required' => 'El campo Moneda es obligatorio.',
            'igv.required_if' => 'El campo Igv es obligatorio.',
            'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
            'igv.numeric' => 'El campo Igv debe se numérico.',

        ];
        Validator::make($data, $rules, $message)->validate();

        $orden = Orden::findOrFail($id);
        $orden->fecha_emision = Carbon::createFromFormat('d/m/Y', $request->get('fecha_emision'))->format('Y-m-d');
        $orden->fecha_entrega = Carbon::createFromFormat('d/m/Y', $request->get('fecha_entrega'))->format('Y-m-d');

        $orden->sub_total = (float) $request->get('monto_sub_total');
        $orden->total_igv = (float) $request->get('monto_total_igv');
        $orden->total = (float) $request->get('monto_total');

        $condicion = Condicion::find($request->get('condicion_id'));

        $orden->empresa_id = '1';
        $orden->proveedor_id = $request->get('proveedor_id');
        $orden->condicion_id = $request->get('condicion_id');
        $orden->modo_compra = $condicion->descripcion;
        $orden->moneda = $request->get('moneda');
        $orden->observacion = $request->get('observacion');
        $orden->usuario_id = auth()->user()->id;
        $orden->igv = $request->get('igv');
        $orden->tipo_cambio = $request->get('tipo_cambio');

        if ($request->get('igv_check') == "on") {
            $orden->igv_check = "1";
        }else{
            $orden->igv_check = '';
        }
        $orden->save();

        $productosJSON = $request->get('productos_tabla');
        $productotabla = json_decode($productosJSON[0]);


        if ($productotabla) {

            Detalle::where('orden_id', $orden->id)->delete();

            foreach ($productotabla as $producto) {
                Detalle::create([
                    'orden_id' => $orden->id,
                    'producto_id' => $producto->producto_id,
                    'cantidad' => $producto->cantidad,
                    'precio' => $producto->precio,
                ]);
            }
        }

        //ELIMINAR DOCUMENTO DE ORDEN DE COMPRA SI EXISTE
        $documento = Documento::where('orden_compra',$id)->where('estado','!=','ANULADO')->first();
        if ($documento) {
            $documento->estado = 'ANULADO';
            $documento->update();

            foreach($documento->lotes as $lot)
            {
                MovimientoAlmacen::where('lote_id', $lot->id)->where('producto_id', $lot->producto_id)->where('compra_documento_id', $lot->compra_documento_id)->where('nota', 'COMPRA')->where('movimiento', 'INGRESO')->delete();
                $lote = LoteProducto::find($lot->id);
                $producto_id = $lote->producto_id;
                $lote->estado = '0';
                $lote->update();

                //RECORRER DETALLE NOTAS
                $cantidadProductos = LoteProducto::where('producto_id',$producto_id)->where('estado','1')->sum('cantidad');
                //ACTUALIZAR EL STOCK DEL PRODUCTO
                $producto = Producto::findOrFail($lote->producto_id);
                $producto->stock = $cantidadProductos ? $cantidadProductos : 0.00;
                $producto->update();
            }



           if($documento->cuenta)
           {
                $cuenta_proveedor = CuentaProveedor::find($documento->cuenta->id);
                $cuenta_proveedor->delete();

                //Registro de actividad
                $descripcion = "SE ELIMINÓ EL DOCUMENTO DE COMPRA DEL DOCUMENTO (".$documento->id.") CON LA FECHA DE EMISION: ". Carbon::parse($documento->fecha_emision)->format('d/m/y');
                $gestion = "DOCUMENTO DE COMPRA";
                eliminarRegistro($documento, $descripcion , $gestion);
           }

            //Registro de actividad
            $descripcion = "SE ELIMINÓ LA CUENTA DE PROVEEDOR: ". Carbon::parse($documento->fecha_emision)->format('d/m/y');
            $gestion = "CUENTA PROVEEDOR";
            eliminarRegistro($documento, $descripcion , $gestion);

            Session::flash('success','Orden de Compra modificada y documento eliminado.');
            return redirect()->route('compras.orden.index')->with('modificar', 'success');
        }else{

            //Registro de actividad
            $descripcion = "SE MODIFICÓ LA ORDEN DE COMPRA CON LA FECHA DE EMISION: ". Carbon::parse($orden->fecha_emision)->format('d/m/y');
            $gestion = "ORDEN DE COMPRA";
            modificarRegistro($orden, $descripcion , $gestion);

            Session::flash('success','Orden de Compra modificada.');
            return redirect()->route('compras.orden.index')->with('modificar', 'success');
        }
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess','orden.index');
        $documento = Documento::where('orden_compra',$id)->where('estado','!=','ANULADO')->first();
        if ($documento) {
            $id_eliminar = $id;
            return view('compras.ordenes.index',[
                'id_eliminar' => $id_eliminar
            ]);

        }else{
            $orden = Orden::findOrFail($id);
            $orden->estado = 'ANULADO';
            $orden->update();

            //Registro de actividad
            $descripcion = "SE ELIMINÓ LA ORDEN DE COMPRA CON LA FECHA DE EMISION: ". Carbon::parse($orden->fecha_emision)->format('d/m/y');
            $gestion = "ORDEN DE COMPRA";
            eliminarRegistro($orden, $descripcion , $gestion);

            Session::flash('success','Orden de compra eliminado.');
            return redirect()->route('compras.orden.index')->with('eliminar', 'success');

        }

    }

    public function confirmDestroy($id)
    {
        $orden = Orden::findOrFail($id);
        $documento = Documento::where('orden_compra',$id)->where('estado','!=','ANULADO')->first();
        if ($documento) {
            $documento->estado = 'ANULADO';
            $documento->update();

            DB::table('kardex')
            ->where('numero_doc', $documento->numero_doc)
            ->update(['estado' => 'ANULADO']);

            if($documento->cuenta)
            {
                $cuenta_proveedor = CuentaProveedor::find($documento->cuenta->id);
                $cuenta_proveedor->delete();

                //Registro de actividad
                $descripcion = "SE ELIMINÓ EL DOCUMENTO DE COMPRA DEL DOCUMENTO (".$documento->id.") CON LA FECHA DE EMISION: ". Carbon::parse($documento->fecha_emision)->format('d/m/y');
                $gestion = "DOCUMENTO DE COMPRA";
                eliminarRegistro($documento, $descripcion , $gestion);
            }

            //Registro de actividad
            $descripcion = "SE ELIMINÓ LA CUENTA DE PROVEEDOR: ". Carbon::parse($documento->fecha_emision)->format('d/m/y');
            $gestion = "CUENTA PROVEEDOR";
            eliminarRegistro($documento, $descripcion , $gestion);

            $orden->estado = 'ANULADO';
            $orden->update();
        }

        //Registro de actividad
        $descripcion = "SE ELIMINÓ LA ORDEN DE COMPRA CON LA FECHA DE EMISION: ". $orden->fecha_emision;
        $gestion = "ORDEN DE COMPRA";
        eliminarRegistro($orden, $descripcion , $gestion);

        Session::flash('success','Orden y Documento de compra eliminado.');
        return redirect()->route('compras.orden.index')->with('eliminar', 'success');
    }

    public function show($id)
    {
        $orden = Orden::findOrFail($id);
        $nombre_completo = $orden->usuario->user->persona->apellido_paterno.' '.$orden->usuario->user->persona->apellido_materno.' '.$orden->usuario->user->persona->nombres;
        $detalles = Detalle::where('orden_id',$id)->get();
        $presentaciones = presentaciones();
        $subtotal = 0;
        $igv = '';
        $tipo_moneda = '';


        foreach($detalles as $detalle){
            $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
        }


        foreach(tipos_moneda() as $moneda){
            if ($moneda->descripcion == $orden->moneda) {
                $tipo_moneda= $moneda->simbolo;
            }
        }


        if (!$orden->igv) {
               $igv = $subtotal * 0.18;
               $total = $subtotal + $igv;
               $decimal_subtotal = number_format($subtotal, 2, '.', '');
               $decimal_total = number_format($total, 2, '.', '');
               $decimal_igv = number_format($igv, 2, '.', '');
        }else{
            $calcularIgv = $orden->igv/100;
            $base = $subtotal / (1 + $calcularIgv);
            $nuevo_igv = $subtotal - $base;
            $decimal_subtotal = number_format($base, 2, '.', '');
            $decimal_total = number_format($subtotal, 2, '.', '');
            $decimal_igv = number_format($nuevo_igv, 2, '.', '');
        }



        return view('compras.ordenes.show', [
            'orden' => $orden,
            'detalles' => $detalles,
            'presentaciones' => $presentaciones,
            'subtotal' => $decimal_subtotal,
            'moneda' => $tipo_moneda,
            'igv' => $decimal_igv,
            'total' => $decimal_total,
            'nombre_completo' => $nombre_completo
        ]);

    }

    public function report($id)
    {
        $orden = Orden::findOrFail($id);
        $nombre_completo = $orden->usuario->user->persona->apellido_paterno.' '.$orden->usuario->user->persona->apellido_materno.' '.$orden->usuario->user->persona->nombres;
        $detalles = Detalle::where('orden_id',$id)->get();
        $subtotal = 0;
        $igv = '';
        $tipo_moneda = '';
        foreach($detalles as $detalle){
            $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
        }
        foreach(tipos_moneda() as $moneda){
            if ($moneda->descripcion == $orden->moneda) {
                $tipo_moneda= $moneda->simbolo;
            }
        }

        if (!$orden->igv) {
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;
            $decimal_subtotal = number_format($subtotal, 2, '.', '');
            $decimal_total = number_format($total, 2, '.', '');
            $decimal_igv = number_format($igv, 2, '.', '');
        }else{
            $calcularIgv = $orden->igv/100;
            $base = $subtotal / (1 + $calcularIgv);
            $nuevo_igv = $subtotal - $base;
            $decimal_subtotal = number_format($base, 2, '.', '');
            $decimal_total = number_format($subtotal, 2, '.', '');
            $decimal_igv = number_format($nuevo_igv, 2, '.', '');
        }



        $presentaciones = presentaciones();
        $paper_size = array(0,0,360,360);
        $pdf = PDF::loadview('compras.ordenes.reportes.detalle',[
            'orden' => $orden,
            'nombre_completo' => $nombre_completo,
            'detalles' => $detalles,
            'presentaciones' => $presentaciones,
            'subtotal' => $decimal_subtotal,
            'moneda' => $tipo_moneda,
            'igv' => $decimal_igv,
            'total' => $decimal_total,
            ])->setPaper('a4')->setWarnings(false);
        return $pdf->stream();
    }

    public function email($id)
    {
        $orden = Orden::findOrFail($id);
        $nombre_completo = $orden->usuario->user->persona->apellido_paterno.' '.$orden->usuario->user->persona->apellido_materno.' '.$orden->usuario->user->persona->nombres;
        $detalles = Detalle::where('orden_id',$id)->get();
        $subtotal = 0;
        $igv = '';
        $tipo_moneda = '';
        foreach($detalles as $detalle){
            $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
        }

        foreach(tipos_moneda() as $moneda){
            if ($moneda->descripcion == $orden->moneda) {
                $tipo_moneda= $moneda->simbolo;
            }
        }


        if (!$orden->igv) {
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;
            $decimal_subtotal = number_format($subtotal, 2, '.', '');
            $decimal_total = number_format($total, 2, '.', '');
            $decimal_igv = number_format($igv, 2, '.', '');
        }else{
            $calcularIgv = $orden->igv/100;
            $base = $subtotal / (1 + $calcularIgv);
            $nuevo_igv = $subtotal - $base;
            $decimal_subtotal = number_format($base, 2, '.', '');
            $decimal_total = number_format($subtotal, 2, '.', '');
            $decimal_igv = number_format($nuevo_igv, 2, '.', '');
        }



        $presentaciones = presentaciones();
        $paper_size = array(0,0,360,360);
        $pdf = PDF::loadview('compras.ordenes.reportes.detalle',[
            'orden' => $orden,
            'nombre_completo' => $nombre_completo,
            'detalles' => $detalles,
            'presentaciones' => $presentaciones,
            'subtotal' => $decimal_subtotal,
            'moneda' => $tipo_moneda,
            'igv' => $decimal_igv,
            'total' => $decimal_total,
            ])->setPaper('a4')->setWarnings(false);

        Mail::send('email.ordencompra',compact("orden"), function ($mail) use ($pdf,$orden) {
            $mail->to($orden->proveedor->correo);
            $mail->subject('ORDEN DE COMPRA OC-00'.$orden->id);
            $mail->attachdata($pdf->output(), 'ORDEN DE COMPRA OC-00'.$orden->id.'.pdf');
        });


        //Registrar en correos enviados
        $correo = new Enviado();
        $correo->orden_id = $orden->id;
        $correo->enviado = '1';
        $correo->usuario = Auth::user()->usuario;
        $correo->save();

        //Añadir check
        $orden->enviado = '1';
        $orden->update();


        Session::flash('success','Orden de Compra enviado al correo '.$orden->proveedor->correo);
        return redirect()->route('compras.orden.show',$orden->id)->with('enviar', 'success');

    }

    public function concretized($id)
    {
        $orden = Orden::findOrFail($id);
        if(empty($orden->documento))
        {
            Session::flash('error','Debes crear un documento de esta orden.');
            return redirect()->route('compras.orden.index')->with('concretar', 'error');
        }
        $orden->estado = 'CONCRETADA';
        $orden->update();

        Session::flash('success','Orden de Compra concretada.');
        return redirect()->route('compras.orden.index')->with('concretar', 'success');

    }


    public function send($id)
    {
        $enviado = Enviado::where('orden_id',$id)->orderby('created_at','DESC')->take(1)->get();;
        $coleccion = collect([]);
        foreach($enviado as $envio){
            $coleccion->push([
                'id' => $envio->id,
                'fecha' => Carbon::parse($envio->created)->format( 'd/m/Y'),
                'hora' => Carbon::parse($envio->created)->format( 'H:i:s'),
                'correo' => $envio->orden->proveedor->correo,
                'usuario' => $envio->usuario,
            ]);
        }

        return $coleccion;
    }

    public function document($id){
        $documento = Documento::where('orden_compra',$id)->where('estado','!=','ANULADO')->first();
        if ($documento) {
            return view('compras.ordenes.index',[
                'id' => $id
            ]);
        }else{
            //REDIRECCIONAR AL DOCUMENTO DE COMPRA
            return redirect()->route('compras.documento.create',['orden'=>$id]);
        }

    }


    public function newDocument($id){
        $documento_old = Documento::where('orden_compra',$id)->where('estado','!=','ANULADO')->first();
        DB::table('kardex')
            ->where('numero_doc', $documento_old->numero_doc)
            ->update(['estado' => 'ANULADO']);

        if($documento_old->cuenta)
        {
            $cuenta_proveedor = CuentaProveedor::find($documento_old->cuenta->id);
            $cuenta_proveedor->delete();
        }
        //ANULADO ANTERIO DOCUMENTO
        $documento = Documento::findOrFail($documento_old->id);
        $documento->estado = 'ANULADO';
        $documento->update();
        //REDIRECCIONAR AL NUEVO DOCUMENTO DE COMPRA
        return redirect()->route('compras.documento.create',['orden'=>$id]);

    }

    public function dolar()
    {
        return precio_dolar();
    }

    public function getProductosByModelo($modelo_id){
        try {
            $productos  =   DB::select('select distinct p.id as producto_id,c.id as color_id, t.id as talla_id,
            m.id as modelo_id, p.nombre as producto_nombre,c.descripcion as color_nombre, 
            t.descripcion as talla_nombre,pct.stock,m.descripcion as modelo_nombre 
            from producto_colores as pc 
            left join producto_color_tallas as pct on (pc.producto_id=pct.producto_id and pc.color_id=pct.color_id) 
            inner join productos as p on p.id=pc.producto_id 
            inner join colores as c on c.id=pc.color_id 
            inner join modelos as m on m.id=p.modelo_id
            left join tallas as t on t.id=pct.talla_id 
            where m.id=?
            order by p.nombre,c.descripcion',[$modelo_id]);    

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
                    $producto_color['color_nombre']         =   $producto->color_nombre;
                    $producto_color['modelo_nombre']        =   $producto->modelo_nombre;
                    $producto_color['importe']              =   0;

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
                        $producto_color['print_importe']   =   true;
                    }else{
                        $producto_color['print_importe']   =   false;
                    }
                    
                    $productos_formateado[]         =   $producto_color;
                    $producto_color_procesados[]    =   $llave;
                }
                $productos_procesados[]    =   $llave_2;
            }
            
            return response()->json(['type'=>'success','message'=>$productos_formateado]);

        } catch (\Throwable $e) {
            return response()->json(['type'=>'error','message'=>$e->getMessage()]);
        }
    }
}
