<?php

namespace App\Http\Controllers\Compras;

use Exception;
use Carbon\Carbon;
use App\Compras\Orden;
use App\Compras\Proveedor;
use App\Almacenes\Producto;
use Illuminate\Http\Request;
use App\Almacenes\LoteProducto;
use App\Compras\CuentaProveedor;
use App\Mantenimiento\Condicion;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Ventas\Documento\Pago\Pago;
use Illuminate\Support\Facades\Log;
use App\Compras\Documento\Documento;
use App\Compras\Pago as ComprasPago;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Empresa;
use App\Movimientos\MovimientoAlmacen;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Compras\Detalle as ComprasDetalle;
use App\Compras\Documento\Pago\Transferencia;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;
use App\Compras\Documento\Detalle as DocumentoDetalle;

class DocumentoController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','documento_compra.index');
        return view('compras.documentos.index');
    }

    public function getDocument(){
        $this->authorize('haveaccess','documento_compra.index');
        $documentos = Documento::where('estado','!=','ANULADO')->get();
        $coleccion = collect([]);
        foreach($documentos as $doc){
            $detalles = DocumentoDetalle::where('documento_id',$doc->id)->where('estado','ACTIVO')->get();
            $documento = Documento::findOrFail($doc->id);
            $subtotal = 0;
            $igv = '';
            $tipo_moneda = '';
            foreach($detalles as $detalle){
                $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
            }

            foreach(tipos_moneda() as $moneda){
                if ($moneda->descripcion == $documento->moneda) {
                    $tipo_moneda= $moneda->simbolo;
                }
            }

            if (!$documento->igv) {
                $igv = $subtotal * 0.18;
                $total = $subtotal + $igv + $documento->percepcion;
                $decimal_total = number_format($total, 2, '.', '');
            }else{
                $calcularIgv = $documento->igv/100;
                $base = $subtotal / (1 + $calcularIgv);
                $nuevo_igv = $subtotal - $base;
                $decimal_total = number_format(($subtotal  + $documento->percepcion), 2, '.', '');
            }
            //TIPO DE PAGO (OTROS)

            // CALCULAR ACUENTA EN MONEDA
            $acuenta = 0;
            $saldo = 0;

            if($documento->cuenta)
            {

                $efectivo = $documento->cuenta->detallePago->sum('efectivo');
                $importe = $documento->cuenta->detallePago->sum('importe');
                $acuenta = $efectivo + $importe;
            }
            else
            {
                $acuenta = $decimal_total;
            }

            $documento->total = $decimal_total;
            $documento->update();


            $saldo = $decimal_total - $acuenta;
            //CALCULAR SALDO
            if ($saldo == 0.0) {
                $documento->estado = "PAGADA";
                $documento->update();
            }else{
                $documento->estado = "PENDIENTE";
                $documento->update();
            }

            $coleccion->push([
                'id' => $documento->id,
                'tipo' => $documento->tipo_compra,
                'tipo_pago' => $documento->tipo_pago,
                'proveedor' => $documento->proveedor->descripcion,
                'empresa' => $documento->empresa->razon_social,
                'fecha_emision' =>  Carbon::parse($documento->fecha_emision)->format( 'd/m/Y'),
                'igv' =>  $documento->igv,
                'numero_doc' =>  $documento->serie_tipo.'-'.$documento->numero_tipo,
                'moneda' =>  $documento->moneda,
                'tipo_cambio' =>  $documento->tipo_cambio,
                'orden_compra' =>  $documento->orden_compra,
                'subtotal' => $tipo_moneda.' '.number_format($subtotal, 2, '.', ''),
                'estado' => $documento->estado,
                'saldo' => $tipo_moneda.' '.number_format($saldo, 2, '.', ''),
                'acuenta' => $tipo_moneda.' '.number_format($acuenta, 2, '.', ''),
                'total' => $tipo_moneda.' '.number_format($decimal_total, 2, '.', ''),
                'total_pagar' => $tipo_moneda.' '.number_format($documento->total_pagar, 2, '.', ''),
                'modo' => $documento->modo_compra,
                'notas' => count($documento->notas)
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function create(Request $request)
    {
        $this->authorize('haveaccess','documento_compra.index');
        $orden = '';
        $detalles = '';
        $fecha_hoy = Carbon::now()->toDateString();
        $fecha_actual = Carbon::now();
        $fecha_actual = date("d/m/Y",strtotime($fecha_actual));
        $fecha_5 = date("d/m/Y",strtotime($fecha_hoy."+ 5 years"));
        if($request->get('orden')){
            $orden = Orden::findOrFail( $request->get('orden') );
            $detalles = ComprasDetalle::where('orden_id', $request->get('orden'))->get();
            foreach($detalles as $item)
            {
                $item['fecha_vencimiento'] = $fecha_5;
                $item['lote_aux'] = 'L-'.$fecha_actual;
            }
        }
        $empresas = Empresa::where('estado','ACTIVO')->get();
        $proveedores = Proveedor::where('estado','ACTIVO')->get();
        $productos = Producto::where('estado','ACTIVO')->get();
        $modos =  modo_compra();
        $condiciones = Condicion::where('estado','ACTIVO')->get();

        $monedas =  tipos_moneda();
        if (empty($orden)) {
            return view('compras.documentos.create',[
                'empresas' => $empresas,
                'proveedores' => $proveedores,
                'productos' => $productos,
                'modos' => $modos,
                'monedas' => $monedas,
                'fecha_hoy' => $fecha_hoy,
                'fecha_actual' => $fecha_actual,
                'condiciones' => $condiciones,
                'fecha_5' => $fecha_5,
            ]);
        }else{
            return view('compras.documentos.create',[
                'orden' => $orden,
                'empresas' => $empresas,
                'proveedores' => $proveedores,
                'productos' => $productos,
                'modos' => $modos,
                'monedas' => $monedas,
                'fecha_hoy' => $fecha_hoy,
                'fecha_actual' => $fecha_actual,
                'condiciones' => $condiciones,
                'detalles' => $detalles,
                'fecha_5' => $fecha_5,
            ]);
        }
    }

    public function getProduct()
    {
        // $productos = Producto::where('estado', 'ACTIVO')->get();
        // foreach($productos as $item)
        // {
        //     $item['medida_desc'] = $item->medidaCompleta();
        // }
        // return response()->json([
        //     'productos' => $productos
        // ]);
        $consulta = DB::table('productos')
        ->join('tabladetalles','tabladetalles.id','=','productos.medida')
        ->select(
            'productos.*',
            'tabladetalles.descripcion as medida_desc'
        )
        ->where('productos.estado','ACTIVO');
        return datatables()->query(
            $consulta
        )->toJson();
    }

    public function store(Request $request){
        $this->authorize('haveaccess','documento_compra.index');
        try
        {
            DB::beginTransaction();
            $productosJSON = $request->get('productos_tabla');
            $productotabla = json_decode($productosJSON[0]);

            $data = $request->all();
            $rules = [
                'fecha_emision'=> 'required',
                'fecha_entrega'=> 'required',
                'tipo_compra'=> 'required',
                'numero_tipo'=> 'required',
                'serie_tipo'=> 'required',
                'proveedor_id'=> 'required',
                'condicion_id'=> 'required',
                'observacion' => 'nullable',
                'moneda' => 'nullable',
                'tipo_cambio' => 'nullable|numeric',
                'igv' => 'required_if:igv_check,==,on|numeric|digits_between:1,3',
            ];

            $message = [
                'fecha_emision.required' => 'El campo Fecha de Emisión es obligatorio.',
                'tipo_compra.required' => 'El campo Tipo es obligatorio.',
                'fecha_entrega.required' => 'El campo Fecha de Entrega es obligatorio.',
                'numero_tipo.required' => 'El campo Número es obligatorio.',
                'serie_tipo.required' => 'El campo Serie es obligatorio.',
                'proveedor_id.required' => 'El campo Proveedor es obligatorio.',
                'condicion_id.required' => 'El campo condicion es obligatorio.',
                'moneda.required' => 'El campo Moneda es obligatorio.',
                'igv.required_if' => 'El campo Igv es obligatorio.',
                'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
                'igv.numeric' => 'El campo Igv debe se numérico.',
                'tipo_cambio.numeric' => 'El campo Tipo de Cambio debe se numérico.',
            ];

            Validator::make($data, $rules, $message)->validate();
            $dolar_aux = json_encode(precio_dolar(), true);
            $dolar_aux = json_decode($dolar_aux, true);

            $dolar = (float)$dolar_aux['original']['venta'];

            $documento = new Documento();
            $documento->fecha_emision = Carbon::createFromFormat('d/m/Y', $request->get('fecha_emision'))->format('Y-m-d');
            $documento->fecha_entrega = Carbon::createFromFormat('d/m/Y', $request->get('fecha_entrega'))->format('Y-m-d');
            $documento->sub_total = (float) $request->get('monto_sub_total');
            $documento->total_igv = (float) $request->get('monto_total_igv');
            $documento->percepcion = (float) $request->get('monto_percepcion');
            $documento->total = (float) $request->get('monto_total');
            $documento->total_pagar = (float) $request->get('monto_total');
            //-------------------------------
            if($request->get('moneda') == 'DOLARES')
            {
                $documento->sub_total_soles = (float) $request->get('monto_sub_total') * (float) $request->get('tipo_cambio');
                $documento->total_igv_soles = (float) $request->get('monto_total_igv') * (float) $request->get('tipo_cambio');
                $documento->percepcion_soles = (float) $request->get('monto_percepcion') * (float) $request->get('tipo_cambio');
                $documento->total_soles = (float) $request->get('monto_total') * (float) $request->get('tipo_cambio');

                $documento->sub_total_dolares = (float) $request->get('monto_sub_total');
                $documento->total_igv_dolares = (float) $request->get('monto_total_igv');
                $documento->percepcion_dolares = (float) $request->get('monto_percepcion');
                $documento->total_dolares = (float) $request->get('monto_total');
            }
            else
            {
                $documento->sub_total_soles = (float) $request->get('monto_sub_total');
                $documento->total_igv_soles = (float) $request->get('monto_total_igv');
                $documento->percepcion_soles = (float) $request->get('monto_percepcion');
                $documento->total_soles = (float) $request->get('monto_total');

                $documento->sub_total_dolares = (float) $request->get('monto_sub_total') / $dolar;
                $documento->total_igv_dolares = (float) $request->get('monto_total_igv') / $dolar;
                $documento->percepcion_dolares = (float) $request->get('monto_percepcion') / $dolar;
                $documento->total_dolares = (float) $request->get('monto_total') / $dolar;
            }
            //-------------------------------
            $condicion = Condicion::find($request->get('condicion_id'));
            $documento->empresa_id = 1;
            $documento->numero_tipo = $request->get('numero_tipo');
            $documento->serie_tipo = $request->get('serie_tipo');
            $documento->proveedor_id = $request->get('proveedor_id');
            $documento->condicion_id = $request->get('condicion_id');
            $documento->modo_compra = $condicion->descripcion;
            $documento->observacion = $request->get('observacion');
            $documento->moneda = $request->get('moneda');
            $documento->tipo_cambio = $request->get('tipo_cambio');
            $documento->dolar = $dolar;
            $documento->usuario_id = auth()->user()->id;
            $documento->igv = $request->get('igv');
            if ($request->get('igv_check') == "on") {
                $documento->igv_check = "1";
            };
            $documento->tipo_compra = $request->get('tipo_compra');
            $documento->orden_compra = $request->get('orden_id');
            $documento->save();

            $numero_doc = $documento->id;
            $documento->numero_doc = 'COMPRA-'.$numero_doc;
            $documento->update();
            //Llenado de los productos
            $productosJSON = $request->get('productos_tabla');
            $productotabla = json_decode($productosJSON[0]);
            foreach ($productotabla as $detalle) {
                $producto = Producto::findOrFail($detalle->producto_id);
                $precio_soles = $detalle->precio;
                $costo_flete_soles = $detalle->costo_flete;
                $precio_dolares = $detalle->precio;
                $costo_flete_dolares = $detalle->costo_flete;
                //-------------------------------
                if($request->get('moneda') == 'DOLARES')
                {
                    $precio_soles = (float) $detalle->precio * (float) $request->get('tipo_cambio');
                    $costo_flete_soles = (float) $detalle->costo_flete * (float) $request->get('tipo_cambio');

                    $precio_dolares = (float) $detalle->precio;
                    $costo_flete_dolares = (float) $detalle->costo_flete;
                }
                else
                {
                    $precio_soles = (float) $detalle->precio;
                    $costo_flete_soles = (float) $detalle->costo_flete;

                    $precio_dolares = (float) $detalle->precio / (float) $dolar;
                    $costo_flete_dolares = (float) $detalle->costo_flete / (float) $dolar;
                }
                //-------------------------------
                $precio_mas_igv_soles = $detalle->precio;
                $precio_mas_igv_dolares = $detalle->precio;
                if ($request->get('igv_check') == "on") {
                    $precio_mas_igv_soles = $precio_soles;
                    $precio_mas_igv_dolares = $precio_dolares;
                }else{
                    $precio_mas_igv_soles = $precio_soles * (1.18);
                    $precio_mas_igv_dolares = $precio_dolares * (1.18);
                }
                //-------------------------------
                DocumentoDetalle::create([
                    'documento_id' => $documento->id,
                    'producto_id' => $detalle->producto_id,
                    'descripcion_producto' => $producto->nombre,
                    'presentacion_producto' => '-',
                    'codigo_producto' => $producto->codigo,
                    'medida_producto' => $producto->medida,
                    'cantidad' => $detalle->cantidad,
                    'precio' => $detalle->precio,
                    'precio_mas_igv_soles' => $precio_mas_igv_soles,
                    'precio_mas_igv_dolares' => $precio_mas_igv_dolares,
                    'precio_soles' => $precio_soles,
                    'precio_dolares' => $precio_dolares,
                    'costo_flete' => $detalle->costo_flete,
                    'costo_flete_soles' => $costo_flete_soles,
                    'costo_flete_dolares' => $costo_flete_dolares,
                    'fecha_vencimiento' =>  Carbon::createFromFormat('d/m/Y', $detalle->fecha_vencimiento)->format('Y-m-d'),
                    'lote' => $detalle->lote,
                ]);
            }
            // TRANSFERRIR PAGOS DE LA ORDEN SI EXISTEN
            if($request->get('orden_id')){

                $documento = Documento::findOrFail($documento->id);
                $documento->tipo_pago =  "1";
                $documento->update();
            }
            //Registro de actividad
            $descripcion = "SE AGREGÓ EL DOCUMENTO DE COMPRA CON LA FECHA DE EMISION: ". Carbon::parse($documento->fecha_emision)->format('d/m/y');
            $gestion = "DOCUMENTO DE COMPRA";

            crearRegistro($documento, $descripcion , $gestion);
            DB::commit();
            Session::flash('success','Documento de Compra creada.');
            return redirect()->route('compras.documento.index')->with('guardar', 'success');

        }
        catch(Exception $e)
        {
            Log::info($e);
            DB::rollBack();
            Session::flash('error',$e->getMessage()."dddddd");
            return redirect()->route('compras.documento.index')->with('guardar', 'error');
        }
    }

    public function edit($id)
    {
        $this->authorize('haveaccess','documento_compra.index');
        $empresas = Empresa::where('estado','ACTIVO')->get();
        $detalles = DocumentoDetalle::where('documento_id', $id)->where('estado','ACTIVO')->get();
        $proveedores = Proveedor::where('estado','ACTIVO')->get();
        $documento = Documento::findOrFail($id);
        $productos = producto::where('estado','ACTIVO')->get();
        $presentaciones =  presentaciones();
        $fecha_hoy = Carbon::now()->toDateString();
        $fecha_actual = Carbon::now();
        $fecha_actual = date("d/m/Y",strtotime($fecha_actual));
        $fecha_5 = date("d/m/Y",strtotime($fecha_hoy."+ 5 years"));
        $condiciones = Condicion::where('estado','ACTIVO')->get();
        return view('compras.documentos.edit',[
            'empresas' => $empresas,
            'proveedores' => $proveedores,
            'documento' => $documento,
            'productos' => $productos,
            'presentaciones' => $presentaciones,
            'fecha_hoy' => $fecha_hoy,
            'fecha_actual' => $fecha_actual,
            'condiciones' => $condiciones,
            'fecha_5' => $fecha_5,
            'detalles' => $detalles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('haveaccess','documento_compra.index');
        $data = $request->all();
        $rules = [
            'fecha_emision'=> 'required',
            'fecha_entrega'=> 'required',
            'tipo_compra'=> 'required',
            'numero_tipo'=> 'required',
            'serie_tipo'=> 'required',
            'proveedor_id'=> 'required',
            'condicion_id'=> 'required',
            'observacion' => 'nullable',
            'moneda' => 'nullable',
            'tipo_cambio' => 'nullable|numeric',
            'igv' => 'required_if:igv_check,==,on|numeric|digits_between:1,3',
        ];
        $message = [
            'fecha_emision.required' => 'El campo Fecha de Emisión es obligatorio.',
            'tipo_compra.required' => 'El campo Tipo es obligatorio.',
            'fecha_entrega.required' => 'El campo Fecha de Entrega es obligatorio.',
            'numero_tipo.required' => 'El campo Número es obligatorio.',
            'serie_tipo.required' => 'El campo Serie es obligatorio.',
            'proveedor_id.required' => 'El campo Proveedor es obligatorio.',
            'condicion_id.required' => 'El campo Condicion es obligatorio.',
            'moneda.required' => 'El campo Moneda es obligatorio.',
            'igv.required_if' => 'El campo Igv es obligatorio.',
            'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
            'igv.numeric' => 'El campo Igv debe se numérico.',
            'tipo_cambio.numeric' => 'El campo Tipo de Cambio debe se numérico.',
        ];
        Validator::make($data, $rules, $message)->validate();
        $dolar_aux = json_encode(precio_dolar(), true);
        $dolar_aux = json_decode($dolar_aux, true);

        $dolar = (float)$dolar_aux['original']['venta'];

        $documento = Documento::findOrFail($id);
        $documento->fecha_emision = Carbon::createFromFormat('d/m/Y', $request->get('fecha_emision'))->format('Y-m-d');
        $documento->fecha_entrega = Carbon::createFromFormat('d/m/Y', $request->get('fecha_entrega'))->format('Y-m-d');
        $documento->sub_total = (float) $request->get('monto_sub_total');
        $documento->total_igv = (float) $request->get('monto_total_igv');
        $documento->percepcion = (float) $request->get('monto_percepcion');
        $documento->total = (float) $request->get('monto_total');
        //-------------------------------
        if($request->get('moneda') === 'DOLARES')
        {
            $documento->sub_total_soles = (float) $request->get('monto_sub_total') * (float) $request->get('tipo_cambio');
            $documento->total_igv_soles = (float) $request->get('monto_total_igv') * (float) $request->get('tipo_cambio');
            $documento->percepcion_soles = (float) $request->get('monto_percepcion') * (float) $request->get('tipo_cambio');
            $documento->total_soles = (float) $request->get('monto_total') * (float) $request->get('tipo_cambio');

            $documento->sub_total_dolares = (float) $request->get('monto_sub_total');
            $documento->total_igv_dolares = (float) $request->get('monto_total_igv');
            $documento->percepcion_dolares = (float) $request->get('monto_percepcion') * (float) $request->get('tipo_cambio');
            $documento->total_dolares = (float) $request->get('monto_total');
        }
        else
        {
            $documento->sub_total_soles = (float) $request->get('monto_sub_total');
            $documento->total_igv_soles = (float) $request->get('monto_total_igv');
            $documento->percepcion_soles = (float) $request->get('monto_percepcion');
            $documento->total_soles = (float) $request->get('monto_total');

            $documento->sub_total_dolares = (float) $request->get('monto_sub_total') / $dolar;
            $documento->total_igv_dolares = (float) $request->get('monto_total_igv') / $dolar;

            $documento->percepcion_soles = (float) $request->get('monto_percepcion') / $dolar;
            $documento->total_dolares = (float) $request->get('monto_total') / $dolar;
        }
            //-------------------------------
        $condicion = Condicion::find($request->get('condicion_id'));
        $documento->empresa_id = '1';
        $documento->proveedor_id = $request->get('proveedor_id');
        $documento->condicion_id = $request->get('condicion_id');
        $documento->modo_compra = $condicion->descripcion;
        $documento->observacion = $request->get('observacion');
        $documento->moneda = $request->get('moneda');
        $documento->tipo_cambio = $request->get('tipo_cambio');
        $documento->numero_tipo = $request->get('numero_tipo');
        $documento->serie_tipo = $request->get('serie_tipo');
        $documento->usuario_id = auth()->user()->id;
        if ($request->get('igv_check') == "on") {
            $documento->igv_check = "1";
            $documento->igv = $request->get('igv');
        }else{
            $documento->igv_check = '';
            $documento->igv = '';
        }
        $documento->tipo_compra = $request->get('tipo_compra');
        $documento->update();
        $productosJSON = $request->get('productos_tabla');
        $productotabla = json_decode($productosJSON[0]);
        if ($productotabla) {
            foreach ($productotabla as $detalle) {
                if($detalle->detalle_id == 0)
                {
                    $producto = Producto::findOrFail($detalle->producto_id);
                    $precio_soles = $detalle->precio;
                    $costo_flete_soles = $detalle->costo_flete;
                    $precio_dolares = $detalle->precio;
                    $costo_flete_dolares = $detalle->costo_flete;
                    //-------------------------------
                    if($request->get('moneda') === 'DOLARES')
                    {
                        $precio_soles = (float) $detalle->precio * (float) $request->get('tipo_cambio');
                        $costo_flete_soles = (float) $detalle->costo_flete * (float) $request->get('tipo_cambio');
                    }
                    else
                    {
                        $precio_soles = (float) $detalle->precio;
                        $costo_flete_soles = (float) $detalle->costo_flete;

                        $precio_dolares = (float) $detalle->precio / (float) $dolar;
                        $costo_flete_dolares = (float) $detalle->costo_flete / (float) $dolar;
                    }
                    $precio_mas_igv_soles = $detalle->precio;
                    $precio_mas_igv_dolares = $detalle->precio;
                    if ($request->get('igv_check') == "on") {
                        $precio_mas_igv_soles = $precio_soles;
                        $precio_mas_igv_dolares = $precio_dolares;
                    }else{
                        $precio_mas_igv_soles = $precio_soles * (1.18);
                        $precio_mas_igv_dolares = $precio_dolares * (1.18);
                    }
                    DocumentoDetalle::create([
                        'documento_id' => $documento->id,
                        'producto_id' => $detalle->producto_id,
                        'descripcion_producto' => $producto->nombre,
                        'presentacion_producto' => '-',
                        'codigo_producto' => $producto->codigo,
                        'medida_producto' => $producto->medida,
                        'cantidad' => $detalle->cantidad,
                        'precio' => $detalle->precio,
                        'precio_mas_igv_soles' => $precio_mas_igv_soles,
                        'precio_mas_igv_dolares' => $precio_mas_igv_dolares,
                        'precio_soles' => $precio_soles,
                        'precio_dolares' => $precio_dolares,
                        'costo_flete' => $detalle->costo_flete,
                        'costo_flete_soles' => $costo_flete_soles,
                        'costo_flete_dolares' => $costo_flete_dolares,
                        'fecha_vencimiento' =>  Carbon::createFromFormat('d/m/Y', $detalle->fecha_vencimiento)->format('Y-m-d'),
                        'lote' => $detalle->lote,
                    ]);
                }else
                {
                    $producto = Producto::findOrFail($detalle->producto_id);
                    $precio_soles = $detalle->precio;
                    $costo_flete_soles = $detalle->costo_flete;
                    $precio_dolares = $detalle->precio;
                    $costo_flete_dolares = $detalle->costo_flete;
                    //-------------------------------
                    if($request->get('moneda') === 'DOLARES')
                    {
                        $precio_soles = (float) $detalle->precio * (float) $request->get('tipo_cambio');
                        $costo_flete_soles = (float) $detalle->costo_flete * (float) $request->get('tipo_cambio');
                    }
                    else
                    {
                        $precio_soles = (float) $detalle->precio;
                        $costo_flete_soles = (float) $detalle->costo_flete;

                        $precio_dolares = (float) $detalle->precio / (float) $dolar;
                        $costo_flete_dolares = (float) $detalle->costo_flete / (float) $dolar;
                    }
                    $precio_mas_igv_soles = $detalle->precio;
                    $precio_mas_igv_dolares = $detalle->precio;
                    if ($request->get('igv_check') == "on") {
                        $precio_mas_igv_soles = $precio_soles;
                        $precio_mas_igv_dolares = $precio_dolares;
                    }else{
                        $precio_mas_igv_soles = $precio_soles * (1.18);
                        $precio_mas_igv_dolares = $precio_dolares * (1.18);
                    }
                    $detalle_update = DocumentoDetalle::find($detalle->detalle_id);
                    $detalle_update->documento_id = $documento->id;
                    $detalle_update->producto_id = $detalle->producto_id;
                    $detalle_update->descripcion_producto = $producto->nombre;
                    $detalle_update->presentacion_producto = '-';
                    $detalle_update->codigo_producto = $producto->codigo;
                    $detalle_update->medida_producto = $producto->medida;
                    $detalle_update->cantidad = $detalle->cantidad;
                    $detalle_update->precio = $detalle->precio;
                    $detalle_update->precio_mas_igv_soles = $precio_mas_igv_soles;
                    $detalle_update->precio_mas_igv_dolares = $precio_mas_igv_dolares;
                    $detalle_update->precio_soles = $precio_soles;
                    $detalle_update->precio_dolares = $precio_dolares;
                    $detalle_update->costo_flete = $detalle->costo_flete;
                    $detalle_update->costo_flete_soles = $costo_flete_soles;
                    $detalle_update->costo_flete_dolares = $costo_flete_dolares;
                    $detalle_update->fecha_vencimiento =  Carbon::createFromFormat('d/m/Y', $detalle->fecha_vencimiento)->format('Y-m-d');
                    $detalle_update->lote = $detalle->lote;
                    $detalle_update->update();
                }
            }
        }
        //Registro de actividad
        $descripcion = "SE MODIFICÓ EL DOCUMENTO DE COMPRA CON LA FECHA DE EMISION: ". Carbon::parse($documento->fecha_emision)->format('d/m/y');
        $gestion = "DOCUMENTO DE COMPRA";
        modificarRegistro($documento, $descripcion , $gestion);
        Session::flash('success','Documento de Compra modificada.');
        return redirect()->route('compras.documento.index')->with('modificar', 'success');
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess','documento_compra.index');
        $documento = Documento::findOrFail($id);

        $detalles_ = DocumentoDetalle::where('documento_id', $documento->id)->where('estado','ACTIVO')->get();
        $cont = 0;
        $success = true;
        while($cont < count($detalles_))
        {
            $lote = LoteProducto::find($detalles_[$cont]->lote_id);
            if(count($lote->detalles_venta) > 0)
            {
                $success = false;
                $cont = count($detalles_);
            }
            $cont = $cont + 1;
        }

        if(!$success)
        {
            Session::flash('error','Documento de compra no se puede eliminar porque ya se han realizado ventas de uno de los detalles.');
            return redirect()->route('compras.documento.index')->with('eliminar', 'error');
        }

        $detalles = DocumentoDetalle::where('documento_id', $documento->id)->where('estado','ACTIVO')->get();

        foreach($detalles as $item)
        {
            $item->estado = 'ANULADO';
            $item->update();
        }

        $documento->estado = 'ANULADO';
        $documento->update();
        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL DOCUMENTO DE COMPRA CON LA FECHA DE EMISION: ". Carbon::parse($documento->fecha_emision)->format('d/m/y');
        $gestion = "DOCUMENTO DE COMPRA";
        eliminarRegistro($documento, $descripcion , $gestion);

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

        Session::flash('success','Documento de Compra eliminada.');
        return redirect()->route('compras.documento.index')->with('eliminar', 'success');
    }

    public function show($id)
    {
        $this->authorize('haveaccess','documento_compra.index');
        $documento = Documento::findOrFail($id);
        $nombre_completo = $documento->usuario->user->persona->apellido_paterno.' '.$documento->usuario->user->persona->apellido_materno.' '.$documento->usuario->user->persona->nombres;
        $detalles = DocumentoDetalle::where('documento_id',$id)->get();
        $presentaciones = presentaciones();
        $subtotal = 0;
        $igv = '';
        $tipo_moneda = '';
        foreach($detalles as $detalle){
            $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
        }
        foreach(tipos_moneda() as $moneda){
            if ($moneda->descripcion == $documento->moneda) {
                $tipo_moneda= $moneda->simbolo;
            }
        }
        if (!$documento->igv) {
               $igv = $subtotal * 0.18;
               $total = $subtotal + $igv;
               $decimal_subtotal = number_format($subtotal, 2, '.', '');
               $decimal_total = number_format($total, 2, '.', '');
               $decimal_igv = number_format($igv, 2, '.', '');
        }else{
            $calcularIgv = $documento->igv/100;
            $base = $subtotal / (1 + $calcularIgv);
            $nuevo_igv = $subtotal - $base;
            $decimal_subtotal = number_format($base, 2, '.', '');
            $decimal_total = number_format($subtotal, 2, '.', '');
            $decimal_igv = number_format($nuevo_igv, 2, '.', '');
        }
        return view('compras.documentos.show', [
            'documento' => $documento,
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
        $this->authorize('haveaccess','documento_compra.index');
        ini_set("max_execution_time", 60000);
        $documento = Documento::findOrFail($id);
        $nombre_completo = $documento->usuario->user->persona->apellido_paterno.' '.$documento->usuario->user->persona->apellido_materno.' '.$documento->usuario->user->persona->nombres;
        $detalles = DocumentoDetalle::where('documento_id',$id)->get();
        $subtotal = 0;
        $igv = '';
        $tipo_moneda = '';
        foreach($detalles as $detalle){
            $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
        }
        foreach(tipos_moneda() as $moneda){
            if ($moneda->descripcion == $documento->moneda) {
                $tipo_moneda= $moneda->simbolo;
            }
        }
        if (!$documento->igv) {
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;
            $decimal_subtotal = number_format($subtotal, 2, '.', '');
            $decimal_total = number_format($total, 2, '.', '');
            $decimal_igv = number_format($igv, 2, '.', '');
        }else{
            $calcularIgv = $documento->igv/100;
            $base = $subtotal / (1 + $calcularIgv);
            $nuevo_igv = $subtotal - $base;
            $decimal_subtotal = number_format($base, 2, '.', '');
            $decimal_total = number_format($subtotal, 2, '.', '');
            $decimal_igv = number_format($nuevo_igv, 2, '.', '');
        }

        $presentaciones = presentaciones();

        // $data = [
        //     'documento' => $documento,
        //     'nombre_completo' => $nombre_completo,
        //     'detalles' => $detalles,
        //     'presentaciones' => $presentaciones,
        //     'subtotal' => $decimal_subtotal,
        //     'moneda' => $tipo_moneda,
        //     'igv' => $decimal_igv,
        //     'total' => $decimal_total,
        // ];

        // return $data;


        $paper_size = array(0,0,360,360);
        $pdf = PDF::loadview('compras.documentos.reportes.detalle',[
            'documento' => $documento,
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

    public function TypePay($id)
    {

    }

    public function comprobante_store(Request $request)
    {
        $compras = Documento::where('moneda', $request->moneda)->where('serie_tipo', $request->serie_tipo)->where('numero_tipo', $request->numero_tipo)->where('proveedor_id',$request->proveedor_id)->where('tipo_compra',$request->tipo_compra)->where('estado','ACTIVO')->get();
        $success = true;
        if(count($compras) > 0){
            $success = false;
        }

        return response()->json([
            'success' => $success,
            'compras' => $compras
        ]);
    }

    public function comprobante_update(Request $request)
    {
        $compras = Documento::where('moneda', $request->moneda)->where('serie_tipo', $request->serie_tipo)->where('numero_tipo', $request->numero_tipo)->where('proveedor_id',$request->proveedor_id)->where('tipo_compra',$request->tipo_compra)->where('estado','ACTIVO')->where('id', '!=',$request->id)->get();
        $success = true;
        if(count($compras) > 0){
            $success = false;
        }

        return response()->json([
            'success' => $success
        ]);
    }
}
