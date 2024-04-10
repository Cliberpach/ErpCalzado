<?php

namespace App\Http\Controllers\Consultas\Ventas;
use Illuminate\Support\Facades\Cache;

use App\Classes\StockMov;
use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use App\Http\Controllers\Controller;
use App\Ventas\Documento\Documento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Events\DocumentoNumeracion;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Pos\DetalleMovimientoVentaCaja;
use App\Ventas\Cliente;
use App\Ventas\Documento\Detalle;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Luecano\NumeroALetras\NumeroALetras;
use Yajra\DataTables\Facades\DataTables;

use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use App\Almacenes\Modelo;
use App\Almacenes\Color;
class NoEnviadosController extends Controller
{
    public function index()
    {
        return view('consultas.ventas.documentos_no.index');
    }

    public function getTable(Request $request)
    {
        $documento = [];
        if ($request->fecha_desde && $request->fecha_hasta) {
            $documentos = Documento::where('estado', '!=', 'ANULADO')->where('sunat', '0')->where('contingencia','0')->where('tipo_venta', '!=', 129)->whereBetween('fecha_documento', [$request->fecha_desde, $request->fecha_hasta])->orderBy('id', 'desc')->get();
        } else {
            $documentos = Documento::where('estado', '!=', 'ANULADO')->where('sunat', '0')->where('contingencia','0')->where('tipo_venta', '!=', 129)->orderBy('id', 'desc')->get();
        }

        $hoy = Carbon::now();

        $coleccion = collect();
        foreach ($documentos as $documento) {

            $transferencia = 0.00;
            $otros = 0.00;
            $efectivo = 0.00;

            if ($documento->tipo_pago_id == 1) {
                $efectivo = $documento->importe;
            } else if ($documento->tipo_pago_id == 2) {
                $transferencia = $documento->importe;
                $efectivo = $documento->efectivo;
            } else {
                $otros = $documento->importe;
                $efectivo = $documento->efectivo;
            }

            $fecha_v = $documento->created_at;
            $diff =  $fecha_v->diffInDays($hoy);

            $cantidad_notas = count($documento->notas);

            $coleccion->push([
                'id' => $documento->id,
                'tipo_venta' => $documento->nombreTipo(),
                'tipo_venta_id' => $documento->tipo_venta,
                'tipo_pago' => $documento->tipo_pago,
                'numero_doc' =>  $documento->serie . '-' . $documento->correlativo,
                'serie' => $documento->serie,
                'correlativo' => $documento->correlativo,
                'cliente' => $documento->tipo_documento_cliente . ': ' . $documento->documento_cliente . ' - ' . $documento->cliente,
                'empresa' => $documento->empresa,
                'cotizacion_venta' =>  $documento->cotizacion_venta,
                'fecha_documento' =>  Carbon::parse($documento->fecha_documento)->format('d/m/Y'),
                'estado' => $documento->estado_pago,
                'sunat' => $documento->sunat,
                'otros' => 'S/. ' . number_format($otros, 2, '.', ''),
                'efectivo' => 'S/. ' . number_format($efectivo, 2, '.', ''),
                'transferencia' => 'S/. ' . number_format($transferencia, 2, '.', ''),
                //'total' => 'S/. ' . number_format($documento->total, 2, '.', ''),
                'total_pagar' => 'S/. ' . number_format($documento->total_pagar, 2, '.', ''),
                'dias' => (int)(4 - $diff < 0 ? 0  : 4 - $diff),
                'notas' => $cantidad_notas
            ]);
        }


        return response()->json([
            'success' => true,
            'ventas' => $coleccion,
        ]);
    }

    public function obtenerLeyenda($documento)
    {
        $formatter = new NumeroALetras();
        $convertir = $formatter->toInvoice($documento->total, 2, 'SOLES');

        //CREAR LEYENDA DEL COMPROBANTE
        $arrayLeyenda = array();
        $arrayLeyenda[] = array(
            "code" => "1000",
            "value" => $convertir
        );
        return $arrayLeyenda;
    }

    public function obtenerProductos($id)
    {
        $detalles = Detalle::where('eliminado', '0')->where('estado','ACTIVO')->where('documento_id', $id)->get();
        $arrayProductos = array();
        for ($i = 0; $i < count($detalles); $i++) {

            $arrayProductos[] = array(
                "codProducto" => $detalles[$i]->codigo_producto,
                "unidad" => $detalles[$i]->unidad,
                "descripcion" => $detalles[$i]->nombre_producto . ' - ' . $detalles[$i]->codigo_lote,
                "cantidad" => (float)$detalles[$i]->cantidad,
                "mtoValorUnitario" => (float)($detalles[$i]->precio_nuevo / 1.18),
                "mtoValorVenta" => (float)($detalles[$i]->valor_venta / 1.18),
                "mtoBaseIgv" => (float)($detalles[$i]->valor_venta / 1.18),
                "porcentajeIgv" => 18,
                "igv" => (float)($detalles[$i]->valor_venta - ($detalles[$i]->valor_venta / 1.18)),
                "tipAfeIgv" => 10,
                "totalImpuestos" =>  (float)($detalles[$i]->valor_venta - ($detalles[$i]->valor_venta / 1.18)),
                "mtoPrecioUnitario" => (float)$detalles[$i]->precio_nuevo

            );
        }

        return $arrayProductos;
    }

    public function obtenerFechaEmision($documento)
    {
        $date = strtotime($documento->fecha_documento);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision . 'T' . $hora_emision . '-05:00';

        return $fecha;
    }

    public function obtenerFechaVencimiento($documento)
    {
        $date = strtotime($documento->fecha_vencimiento);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision . 'T' . $hora_emision . '-05:00';

        return $fecha;
    }

    public function sunat($id)
    {
        $documento = Documento::findOrFail($id);
        //OBTENER CORRELATIVO DEL COMPROBANTE ELECTRONICO
        $existe = event(new DocumentoNumeracion($documento));
        if ($existe[0]) {
            if ($existe[0]->get('existe') == true) {
                if ($documento->sunat != '1') {
                    //ARREGLO COMPROBANTE
                    $arreglo_comprobante = array(
                        "tipoOperacion" => $documento->tipoOperacion(),
                        "tipoDoc" => $documento->tipoDocumento(),
                        "serie" => $existe[0]->get('numeracion')->serie,
                        "correlativo" => $documento->correlativo,
                        "fechaEmision" => self::obtenerFechaEmision($documento),
                        "fecVencimiento" => self::obtenerFechaVencimiento($documento),
                        "observacion" => $documento->observacion,
                        "formaPago" => array(
                            "moneda" =>  $documento->simboloMoneda(),
                            "tipo" =>  $documento->formaPago(),
                        ),
                        "tipoMoneda" => $documento->simboloMoneda(),
                        "client" => array(
                            "tipoDoc" => $documento->tipoDocumentoCliente(),
                            "numDoc" => $documento->documento_cliente,
                            "rznSocial" => $documento->cliente,
                            "address" => array(
                                "direccion" => $documento->direccion_cliente,
                            )
                        ),
                        "company" => array(
                            "ruc" =>  $documento->ruc_empresa,
                            "razonSocial" => $documento->empresa,
                            "address" => array(
                                "direccion" => $documento->direccion_fiscal_empresa,
                            )
                        ),
                        "mtoOperGravadas" => (float)$documento->sub_total,
                        "mtoOperExoneradas" => 0,
                        "mtoIGV" => (float)$documento->total_igv,

                        "valorVenta" => (float)$documento->sub_total,
                        "totalImpuestos" => (float)$documento->total_igv,
                        "subTotal" => (float)$documento->total,
                        "mtoImpVenta" => (float)$documento->total,
                        "ublVersion" => "2.1",
                        "details" => self::obtenerProductos($documento->id),
                        "legends" =>  self::obtenerLeyenda($documento),
                    );

                    //return $arreglo_comprobante;
                    //OBTENER JSON DEL COMPROBANTE EL CUAL SE ENVIARA A SUNAT
                    $data = enviarComprobanteapi(json_encode($arreglo_comprobante), $documento->empresa_id);

                    //RESPUESTA DE LA SUNAT EN JSON
                    $json_sunat = json_decode($data);
                    if ($json_sunat->sunatResponse->success == true) {

                        $documento->sunat = '1';

                        $data_comprobante = generarComprobanteapi(json_encode($arreglo_comprobante), $documento->empresa_id);

                        $name = $documento->serie . "-" . $documento->correlativo . '.pdf';

                        $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . $name);

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'));
                        }

                        file_put_contents($pathToFile, $data_comprobante);

                        $arreglo_qr = array(
                            "ruc" => $documento->ruc_empresa,
                            "tipo" => $documento->tipoDocumento(),
                            "serie" => $documento->serie,
                            "numero" => $documento->correlativo,
                            "emision" => self::obtenerFechaEmision($documento),
                            "igv" => 18,
                            "total" => (float)$documento->total,
                            "clienteTipo" => $documento->tipoDocumentoCliente(),
                            "clienteNumero" => $documento->documento_cliente
                        );

                        /********************************/
                        $data_qr = generarQrApi(json_encode($arreglo_qr), $documento->empresa_id);

                        $name_qr = $documento->serie . "-" . $documento->correlativo . '.svg';

                        $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . $name_qr);

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'));
                        }

                        file_put_contents($pathToFile_qr, $data_qr);

                        /********************************/

                        $data_xml = generarXmlapi(json_encode($arreglo_comprobante), $documento->empresa_id);
                        $name_xml = $documento->serie . '-' . $documento->correlativo . '.xml';
                        $pathToFile_xml = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . $name_xml);
                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml'));
                        }
                        file_put_contents($pathToFile_xml, $data_xml);

                        /********************************* */

                        $documento->nombre_comprobante_archivo = $name;
                        $documento->hash = $json_sunat->hash;
                        $documento->xml = $name_xml;
                        $documento->ruta_comprobante_archivo = 'public/sunat/' . $name;
                        $documento->ruta_qr = 'public/qrs/' . $name_qr;
                        $documento->update();


                        //Registro de actividad
                        $descripcion = "SE AGREGÓ EL COMPROBANTE ELECTRONICO: " . $documento->serie . "-" . $documento->correlativo;
                        $gestion = "COMPROBANTES ELECTRONICOS";
                        crearRegistro($documento, $descripcion, $gestion);

                        Session::flash('success', 'Documento de Venta enviada a Sunat con exito.');
                        return view('consultas.ventas.documentos_no.index', [

                            'id_sunat' => $json_sunat->sunatResponse->cdrResponse->id,
                            'descripcion_sunat' => $json_sunat->sunatResponse->cdrResponse->description,
                            'notas_sunat' => $json_sunat->sunatResponse->cdrResponse->notes,
                            'sunat_exito' => true

                        ])->with('sunat_exito', 'success');
                    } else {

                        //COMO SUNAT NO LO ADMITE VUELVE A SER 0
                        $documento->sunat = '0';
                        $documento->update();

                        if ($json_sunat->sunatResponse->error) {
                            $id_sunat = $json_sunat->sunatResponse->error->code;
                            $descripcion_sunat = $json_sunat->sunatResponse->error->message;
                        } else {
                            $id_sunat = $json_sunat->sunatResponse->cdrResponse->id;
                            $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;
                        };

                        Session::flash('error', 'Documento de Venta sin exito en el envio a sunat.');
                        return view('consultas.ventas.documentos_no.index', [
                            'id_sunat' =>  $id_sunat,
                            'descripcion_sunat' =>  $descripcion_sunat,
                            'sunat_error' => true,

                        ])->with('sunat_error', 'error');
                    }
                } else {
                    $documento->sunat = '1';
                    $documento->update();
                    Session::flash('error', 'Documento de venta fue enviado a Sunat.');
                    return redirect()->route('consultas.ventas.documentos_no.index')->with('sunat_existe', 'error');
                }
            } else {
                Session::flash('error', 'Tipo de Comprobante no registrado en la empresa.');
                return redirect()->route('consultas.ventas.documentos_no.index')->with('sunat_existe', 'error');
            }
        } else {
            Session::flash('error', 'Empresa sin parametros para emitir comprobantes electronicos');
            return redirect()->route('consultas.ventas.documentos_no.index');
        }
    }

    public function edit($id)
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        $empresas = Empresa::where('estado', 'ACTIVO')->get();
        $clientes = Cliente::where('estado', 'ACTIVO')->get();
        $productos = Producto::where('estado', 'ACTIVO')->get();
        $documento = Documento::findOrFail($id);
        $detalles = Detalle::where('documento_id', $id)->where('eliminado', '0')->where('estado', 'ACTIVO')->with(['lote', 'lote.producto'])->get();
        $condiciones = Condicion::where('estado', 'ACTIVO')->get();
        $fullaccess = false;
        $fecha_hoy = Carbon::now()->toDateString();
        $tallas = Talla::where('estado','ACTIVO')->get();
        $modelos =  Modelo::where('estado','ACTIVO')->get();
        

        if (count(Auth::user()->roles) > 0) {
            $cont = 0;
            while ($cont < count(Auth::user()->roles)) {
                if (Auth::user()->roles[$cont]['full-access'] == 'SI') {
                    $fullaccess = true;
                    $cont = count(Auth::user()->roles);
                }
                $cont = $cont + 1;
            }
        }
        return view('consultas.ventas.documentos_no.edit', [
            'documento' => $documento,
            'detalles' => $detalles,
            'empresas' => $empresas,
            'clientes' => $clientes,
            'productos' => $productos,
            'condiciones' => $condiciones,
            'fullaccess' => $fullaccess,
            'fecha_hoy' => $fecha_hoy,
            'tallas'=>$tallas,
            'modelos' => $modelos
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        ini_set("max_execution_time", 60000);
        try {

            DB::beginTransaction();
            $data = $request->all();

            $rules = [
                'fecha_documento_campo' => 'required',
                'fecha_atencion_campo' => 'required',
                //'tipo_venta'=> 'required',
                'condicion_id' => 'required',
                'tipo_pago_id' => 'nullable',
                'efectivo' => 'required',
                'importe' => 'required',
                'empresa_id' => 'required',
                'cliente_id' => 'required',
                'observacion' => 'nullable',
                'igv' => 'required_if:igv_check,==,on|numeric|digits_between:1,3',

            ];

            $message = [
                'fecha_documento_campo.required' => 'El campo Fecha de Emisión es obligatorio.',
                //'tipo_venta.required' => 'El campo tipo de venta es obligatorio.',
                'condicion_id.required' => 'El campo condición de pago es obligatorio.',
                //'tipo_pago_id.required' => 'El campo modo de pago es obligatorio.',
                'importe.required' => 'El campo importe es obligatorio.',
                'efectivo.required' => 'El campo efectivo es obligatorio.',
                'fecha_atencion_campo.required' => 'El campo Fecha de Entrega es obligatorio.',
                'empresa_id.required' => 'El campo Empresa es obligatorio.',
                'cliente_id.required' => 'El campo Cliente es obligatorio.',
                'igv.required_if' => 'El campo Igv es obligatorio.',
                'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
                'igv.numeric' => 'El campo Igv debe se numérico.',


            ];


            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => true,
                    'data' => array('mensajes' => $validator->getMessageBag()->toArray())
                ]);
            }

            $documento = Documento::find($id);
            $monto = $documento->total;
            $documento->fecha_documento             = $request->get('fecha_documento_campo');
            $documento->fecha_atencion              =  $request->get('fecha_atencion_campo');
            $documento->fecha_vencimiento           =  $request->get('fecha_vencimiento_campo');
            //EMPRESA
            $empresa                                = Empresa::findOrFail($request->get('empresa_id'));
            $documento->ruc_empresa                 = $empresa->ruc;
            $documento->empresa                     = $empresa->razon_social;
            $documento->direccion_fiscal_empresa    = $empresa->direccion_fiscal;
            $documento->empresa_id                  = $request->get('empresa_id'); //OBTENER NUMERACION DE LA EMPRESA
            //CLIENTE
            $cliente = Cliente::findOrFail($request->get('cliente_id'));

            //CONDICION
            $cadena                     = explode('-', $request->get('condicion_id'));
            $condicion                  = Condicion::findOrFail($cadena[0]);
            $documento->condicion_id    = $condicion->id;

            $documento->tipo_documento_cliente  =  $cliente->tipo_documento;
            $documento->documento_cliente       =  $cliente->documento;
            $documento->direccion_cliente       =  $cliente->direccion;
            $documento->cliente                 =  $cliente->nombre;
            $documento->cliente_id              = $request->get('cliente_id');

            $documento->observacion         = $request->get('observacion');

            $monto_sub_total        =   str_replace('S/', '', $request->get('monto_sub_total'));
            $monto_embalaje         =   str_replace('S/', '', $request->get('monto_embalaje') ?? 0);
            $monto_envio            =   str_replace('S/', '', $request->get('monto_envio') ?? 0);
            $monto_total            =   str_replace('S/', '', $request->get('monto_total'));
            $monto_total_igv        =   str_replace('S/', '', $request->get('monto_total_igv'));
            $monto_total_pagar      =   str_replace('S/', '', $request->get('monto_total_pagar'));  
            $monto_descuento        =   str_replace('S/', '', $request->get('monto_descuento')??0);  
            $porcentaje_descuento   =   ($monto_descuento*100)/($monto_total_pagar+$monto_descuento);

            $documento->sub_total               =   $monto_sub_total;
            $documento->monto_embalaje          =   $monto_embalaje;  
            $documento->monto_envio             =   $monto_envio;  
            $documento->total                   =   $monto_total;  
            $documento->total_igv               =   $monto_total_igv;
            $documento->total_pagar             =   $monto_total_pagar;  
            $documento->monto_descuento         =   $monto_descuento;
            $documento->porcentaje_descuento    =   $porcentaje_descuento;

            $documento->igv                 = $request->get('igv') ? $request->get('igv') : 18;
            $documento->moneda              = 1;

            if ($monto != $request->get('monto_total')) {
                 $documento->tipo_pago_id = $request->get('tipo_pago_id');
                 $documento->importe = $request->get('importe');
                 $documento->efectivo = $request->get('efectivo');
                 $documento->estado_pago = 'PENDIENTE';
            }

            if ($request->get('igv_check') == "on") {
                 $documento->igv_check = "1";
            }

            $numero_doc = $documento->id;
            $documento->numero_doc = 'VENTA-' . $numero_doc;
            $documento->update();

            //Llenado de los articulos
            $productosJSON = $request->get('productos_tabla');
            $productotabla = json_decode($productosJSON);

            $detalles = Detalle::where('eliminado', '0')->where('estado','ACTIVO')->where('documento_id', $id)->get();

            //========= ELIMINAR DETALLE ANTERIOR ====
            foreach ($detalles as $detalle) {

               //==== reponer stock de producto_color_tallas =====
               //====== NOTA: EL STOCK_LOGICO se actualizó en la vista =======
               DB::table('producto_color_tallas')
               ->where('producto_id', $detalle->producto_id)
               ->where('color_id', $detalle->color_id)
               ->where('talla_id', $detalle->talla_id)
               ->update(['stock' => DB::raw('stock + ' . $detalle->cantidad)]);
           
            }

            //========== eliminar detalles ======
            DB::table('cotizacion_documento_detalles')
             ->where('documento_id', $id)
             ->delete();

            //===== GRABAR EL NUEVO DETALLE ========
            //==== actualizamos solo stock, ya que el stock logico ya está actualizado =======
            foreach ($productotabla as $producto) {
                foreach ($producto->tallas as $talla) {
                    //==== CALCULANDO MONTOS PARA EL DETALLE ====
                    $importe                =   floatval($talla->cantidad) * floatval($producto->precio_venta);
                    $precio_unitario        =   $producto->porcentaje_descuento==0?$producto->precio_venta:$producto->precio_venta_nuevo;

                       
                    $lote = ProductoColorTalla::where('producto_id', $producto->producto_id)
                    ->where('color_id', $producto->color_id)
                    ->where('talla_id', $talla->talla_id)
                    ->with('producto')
                    ->with('color')
                    ->with('talla')
                    ->firstOrFail();

                    Detalle::create([
                        'documento_id'      =>  $documento->id,
                        'producto_id'       =>  $producto->producto_id,
                        'color_id'          =>  $producto->color_id,
                        'talla_id'          =>  $talla->talla_id,
                        'codigo_producto'   =>  $lote->producto->codigo,
                        'nombre_producto'   =>  $lote->producto->nombre,
                        'nombre_color'      =>  $lote->color->descripcion,
                        'nombre_talla'      =>  $lote->talla->descripcion,
                        'nombre_modelo'     =>  $lote->producto->modelo->descripcion,
                        'cantidad'          =>  floatval($talla->cantidad),
                        'precio_unitario'   =>  floatval($producto->precio_venta),
                        'importe'           =>  floatval($talla->cantidad) * floatval($producto->precio_venta),
                        'precio_unitario_nuevo' => floatval($precio_unitario),
                        'porcentaje_descuento'  =>  floatval($producto->porcentaje_descuento),
                        'monto_descuento'           =>  floatval($importe)*floatval($producto->porcentaje_descuento)/100,
                        'importe_nuevo'             =>  floatval($precio_unitario) * floatval($talla->cantidad)
                    ]);

                    //ACTUALIZANDO STOCK...
                    DB::update('UPDATE producto_color_tallas 
                    SET stock = stock - ? 
                    WHERE producto_id = ? AND color_id = ? AND talla_id = ?', 
                    [$talla->cantidad, $producto->producto_id, $producto->color_id, $talla->talla_id]);

                    if ($lote->stock == 0) {
                        DB::update('UPDATE producto_color_tallas 
                        SET estado = ? 
                        WHERE producto_id = ? AND color_id = ? AND talla_id = ?', 
                        ['0', $producto->producto_id, $producto->color_id, $talla->talla_id]);        
                    }
                }  
            }

           

            // foreach ($detalles as $item) {
            //     $lote = LoteProducto::findOrFail($item->lote_id);
            //     $lote->cantidad =  $lote->cantidad + $item->cantidad;
            //     $lote->cantidad_logica =  $lote->cantidad_logica + $item->cantidad;
            //     $lote->estado = '1';
            //     $lote->update();
            //     $item->eliminado = '1';
            //     $item->update();
            // }

            // foreach ($productotabla as $producto) {
            //     if ($producto->detalle_id != 0) {
            //         $lote = LoteProducto::findOrFail($producto->lote_id);
            //         $detalle = Detalle::find($producto->detalle_id);
            //         $cantidad = $detalle->cantidad;
            //         $detalle->codigo_producto = $lote->producto->codigo;
            //         $detalle->unidad = $lote->producto->getMedida();
            //         $detalle->nombre_producto = $lote->producto->nombre;
            //         $detalle->codigo_lote = $lote->codigo_lote;
            //         $detalle->cantidad = $producto->cantidad;
            //         $detalle->precio_unitario = $producto->precio_unitario;
            //         $detalle->precio_inicial = $producto->precio_inicial;
            //         $detalle->precio_nuevo = $producto->precio_nuevo;
            //         $detalle->dinero = $producto->dinero;
            //         $detalle->descuento = $producto->descuento;
            //         $detalle->valor_unitario = $producto->valor_unitario;
            //         $detalle->valor_venta = $producto->valor_venta;
            //         $detalle->estado = 'ACTIVO';
            //         $detalle->eliminado = '0';
            //         $detalle->update();

            //         $lote->cantidad = $lote->cantidad - $cantidad;
            //         $lote->cantidad_logica = $lote->cantidad_logica - $cantidad;
            //         if ($cantidad >= $producto->cantidad) {
            //             $cant_aux = $cantidad - $producto->cantidad;
            //             $lote->cantidad =  $lote->cantidad + $cant_aux;
            //         } else {
            //             $cant_aux = $producto->cantidad - $cantidad;
            //             $lote->cantidad =  $lote->cantidad - $cant_aux;
            //         }

            //         if ($cantidad >= $producto->cantidad) {
            //             $cant_aux = $cantidad - $producto->cantidad;
            //             $lote->cantidad_logica =  $lote->cantidad_logica + $cant_aux;
            //         }
            //         $lote->update();
            //     } else {
            //         $lote = LoteProducto::findOrFail($producto->lote_id);
            //         Detalle::create([
            //             'documento_id' => $documento->id,
            //             'lote_id' => $producto->lote_id, //LOTE
            //             'codigo_producto' => $lote->producto->codigo,
            //             'unidad' => $lote->producto->getMedida(),
            //             'nombre_producto' => $lote->producto->nombre,
            //             'codigo_lote' => $lote->codigo_lote,
            //             'cantidad' => $producto->cantidad,
            //             'precio_unitario' => $producto->precio_unitario,
            //             'precio_inicial' => $producto->precio_inicial,
            //             'precio_nuevo' => $producto->precio_nuevo,
            //             'dinero' => $producto->dinero,
            //             'descuento' => $producto->descuento,
            //             'valor_unitario' => $producto->valor_unitario,
            //             'valor_venta' => $producto->valor_venta,
            //         ]);

            //         if ($lote->cantidad - $producto->cantidad == 0) {
            //             $lote->cantidad_logica =  0;
            //         }

            //         $lote->cantidad =  $lote->cantidad - $producto->cantidad;
            //         $lote->update();
            //     }
            // }


            $documento = Documento::find($documento->id);
            $documento->nombre_comprobante_archivo = $documento->serie . '-' . $documento->correlativo . '.pdf';
            $documento->update();

            //Registro de actividad
            $descripcion = "SE AGREGÓ EL DOCUMENTO DE VENTA MODIFICADO CON LA FECHA: " . Carbon::parse($documento->fecha_documento)->format('d/m/y');
            $gestion = "DOCUMENTO DE VENTA";
            crearRegistro($documento, $descripcion, $gestion);

            DB::commit();
            Session::flash('success', 'Documento de venta modificado.');
            return response()->json([
                'success' => true,
                'documento_id' => $documento->id
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => 'Ocurrio un error porfavor volver a intentar, si el error persiste comunicarse con el administrador del sistema.',
                'excepcion' => $e->getMessage()
            ]);
        }
    }

    public function getLot($id)
    {
        return datatables()->query(
            DB::table('lote_productos')
                ->join('productos', 'productos.id', '=', 'lote_productos.producto_id')
                ->join('productos_clientes', 'productos_clientes.producto_id', '=', 'productos.id')
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->join('tabladetalles', 'tabladetalles.id', '=', 'productos.medida')
                ->leftJoin('detalle_nota_ingreso', 'detalle_nota_ingreso.lote_id', '=', 'lote_productos.id')
                ->leftJoin('nota_ingreso', 'nota_ingreso.id', '=', 'detalle_nota_ingreso.nota_ingreso_id')
                ->leftJoin('compra_documento_detalles', 'compra_documento_detalles.lote_id', '=', 'lote_productos.id')
                ->leftJoin('compra_documentos', 'compra_documentos.id', '=', 'compra_documento_detalles.documento_id')
                ->select(
                    'nota_ingreso.moneda as moneda_ingreso',
                    'compra_documentos.moneda as moneda_compra',
                    'compra_documentos.dolar as dolar_compra',
                    'compra_documentos.igv_check as igv_compra',
                    'compra_documento_detalles.precio_soles',
                    'compra_documento_detalles.precio as precio_compra',
                    'compra_documento_detalles.costo_flete_soles',
                    'compra_documento_detalles.costo_flete_dolares',
                    'compra_documento_detalles.cantidad as cantidad_comprada',
                    'detalle_nota_ingreso.costo as precio_ingreso',
                    'detalle_nota_ingreso.costo_soles as precio_ingreso_soles',
                    'nota_ingreso.dolar as dolar_ingreso',
                    'compra_documento_detalles.precio_mas_igv_soles',
                    'lote_productos.*',
                    'productos.nombre',
                    'productos.igv',
                    'productos.codigo_barra',
                    //'productos.porcentaje_normal',
                    DB::raw('ifnull((select porcentaje
                    from productos_clientes pc
                    where pc.producto_id = lote_productos.producto_id
                    and pc.cliente = 121
                    and pc.estado = "ACTIVO"
                order by id desc
                limit 1),20) as porcentaje_normal'),
                    //'productos.porcentaje_distribuidor',
                    DB::raw('ifnull((select porcentaje
                    from productos_clientes pc
                    where pc.producto_id = lote_productos.producto_id
                    and pc.cliente = 122
                    and pc.estado = "ACTIVO"
                order by id desc
                limit 1),20) as porcentaje_distribuidor'),
                    'productos_clientes.cliente',
                    'productos_clientes.moneda',
                    'productos_clientes.porcentaje',
                    'tabladetalles.simbolo as unidad_producto',
                    'categorias.descripcion as categoria',
                    'marcas.marca',
                    DB::raw('DATE_FORMAT(lote_productos.fecha_vencimiento, "%d/%m/%Y") as fecha_venci')
                )
                ->where('lote_productos.cantidad_logica', '>', 0)
                ->where('lote_productos.estado', '1')
                ->where('productos_clientes.cliente', $id)
                ->where('productos_clientes.moneda', '1')
                ->orderBy('lote_productos.id', 'ASC')
                ->where('productos_clientes.estado', 'ACTIVO')
        )->toJson();
    }

    public function getLotRecientes($id)
    {
        $detalles = Detalle::where('eliminado', '0')->where('estado','ACTIVO')->where('documento_id', $id)->get();
        $colecction = collect([]);
        foreach ($detalles as $detalle) {
            $precio_soles = 0;
            if (!empty($detalle->lote->detalle_compra)) {
                $precio_soles = $detalle->lote->detalle_compra->precio_soles;
            } else {
                $precio_soles = $detalle->lote->detalle_nota->costo_soles;
            }

            $colecction->push([
                'id' => $detalle->lote->id,
                'detalle_id' => $detalle->id,
                'precio_soles' => $precio_soles,
                'precio_nuevo' => $detalle->precio_nuevo,
                'precio_inicial' => $detalle->precio_inicial,
                'precio_unitario' => $detalle->precio_unitario,
                'valor_unitario' => $detalle->valor_unitario,
                'valor_venta' => $detalle->valor_venta,
                'dinero' => $detalle->dinero,
                'descuento' => $detalle->descuento,
                'nombre' => $detalle->lote->producto->nombre,
                'codigo_barra' => $detalle->lote->producto->codigo_barra,
                'cliente' => $detalle->lote->producto->tipoCliente->where('cliente', 121)->first()->cliente,
                'monto' => $detalle->precio_nuevo,
                'moneda' => $detalle->lote->producto->tipoCliente->where('cliente', 121)->first()->moneda,
                'unidad_producto' => $detalle->lote->producto->getMedida(),
                'descripcion' => $detalle->lote->producto->categoria->descripcion,
                'fecha_venci' => $detalle->lote->fecha_vencimiento,
                'codigo_lote' => $detalle->lote->codigo_lote,
                'cantidad' => $detalle->lote->cantidad,
                'cantidad_logica' => $detalle->cantidad,
            ]);
        }
        return DataTables::of($colecction)->make(true);
    }

    //CAMBIAR CANTIDAD LOGICA DEL LOTE
    public function quantity(Request $request)
    {
        $data           =   $request->all();
        $modo           =   $request->get('modo');
        $producto_id    =   $request->get('producto_id');
        $color_id       =   $request->get('color_id');
        $talla_id       =   $request->get('talla_id');
        $cantidad       =   $request->get('cantidad');

        
        if($modo == 'nuevo'){
            DB::table('producto_color_tallas')
            ->where('producto_id', $producto_id)
            ->where('color_id', $color_id)
            ->where('talla_id', $talla_id)
            ->update(['stock_logico' => DB::raw('stock_logico - ' . $cantidad)]);

            //$this->saveCache($producto_id,$color_id,$talla_id,$cantidad);
        }

        if($modo == 'editar'){
            $cantidadAnterior   =   $request->get('cantidadAnterior');
            DB::table('producto_color_tallas')
            ->where('producto_id', $producto_id)
            ->where('color_id', $color_id)
            ->where('talla_id', $talla_id)
            ->update(['stock_logico' => DB::raw('stock_logico + ' . ($cantidadAnterior-$cantidad) )]);
        }

        if($modo == 'eliminar'){
            $tallas = $request->get('tallas');
            foreach ($tallas as $talla) {
                DB::table('producto_color_tallas')
                ->where('producto_id', $producto_id)
                ->where('color_id', $color_id)
                ->where('talla_id', $talla['talla_id'])
                ->update(['stock_logico' => DB::raw('stock_logico + ' . $talla['cantidad'] )]);
            }
        }

        // $data = $request->all();
        // $producto_id = $data['lote_id'];
        // $cantidad = $data['cantidad'];
        // $condicion = $data['condicion'];
        // $mensaje = '';
        // $lote = LoteProducto::findOrFail($producto_id);
        // //DISMINUIR
        // if ($lote->cantidad_logica >= $cantidad && $condicion == '1') {
        //     $nuevaCantidad = $lote->cantidad_logica - $cantidad;
        //     $lote->cantidad_logica = $nuevaCantidad;
        //     $lote->update();
        //     $mensaje = 'Cantidad aceptada';
        // }
        // //AUMENTAR
        // if ($condicion == '0') {
        //     $nuevaCantidad = $lote->cantidad_logica + $cantidad;
        //     $lote->cantidad_logica = $nuevaCantidad;
        //     $lote->update();
        //     $mensaje = 'Cantidad regresada';
        // }

        return 'completado';
    }


    public function saveCache($producto_id,$color_id,$talla_id,$cantidad){

        $stockMov = new StockMov($producto_id,$color_id,$talla_id,$cantidad);
        $productos = [];

        if (Cache::has('productos')) {
            $productos = Cache::get('productos');
        }

        $productos[] = $stockMov;
        Cache::forever('productos',$productos);
    
    }

    //DEVOLVER CANTIDAD LOGICA AL CERRAR VENTANA
    public function returnQuantity(Request $request)
    {
        $mensaje = false;

        $data           =   $request->all();
        $productosTabla =   json_decode($request->get('carrito'));
        $detalles       =   json_decode($request->get('detalles'));
        $detallesColeccion = collect($detalles);


        
        //====== devolvemos stock (increment) ==========
        foreach($productosTabla as $producto){
            foreach($producto->tallas as $talla){
                //======= update en DB =======
                DB::table('producto_color_tallas')
                ->where('producto_id', $producto->producto_id)
                ->where('color_id', $producto->color_id)
                ->where('talla_id', $talla->talla_id) 
                ->increment('stock_logico', $talla->cantidad); 
            }
            $mensaje    = true;
        }


        //=========== quitamos stock previo ===========
        foreach ($detalles as $detalle) {
            DB::table('producto_color_tallas')
            ->where('producto_id', $detalle->producto_id)
            ->where('color_id', $detalle->color_id)
            ->where('talla_id', $detalle->talla_id) 
            ->decrement('stock_logico', floatval($detalle->cantidad));    
            $mensaje    = true;    
        }
         
        
        
        // $data = $request->all();
        // $cantidades = $data['cantidades'];
        // $productosJSON = $cantidades;
        // $productotabla = json_decode($productosJSON);

        // $detalles_aux = $data['detalles'];
        // $productos = $detalles_aux;
        // $detalles = json_decode($productos);

        // $mensaje = true;
        // foreach ($productotabla as $detalle) {
        //     $cont = 0;
        //     $existe = false;
        //     $indice =  -1;
        //     while ($cont < count($detalles)) {
        //         if ($detalles[$cont]->id == $detalle->detalle_id) {
        //             $existe = true;
        //             $indice = $cont;
        //             $cont = count($detalles);
        //         }
        //         $cont = $cont + 1;
        //     }

        //     if ($existe) {
        //         if ($indice >= 0) {
        //             $lot = $detalles[$indice];

        //             if ($detalle->cantidad - $lot->cantidad > 0) {
        //                 $lote = LoteProducto::findOrFail($lot->lote_id);
        //                 $lote->cantidad_logica = $lote->cantidad_logica + ($detalle->cantidad - $lot->cantidad);
        //                 $lote->estado = '1';
        //                 $lote->update();
        //             }
        //         }
        //     } else {
        //         //DEVOLVEMOS CANTIDAD AL LOTE Y AL LOTE LOGICO
        //         $lote = LoteProducto::findOrFail($detalle->lote_id);
        //         $lote->cantidad_logica = $lote->cantidad_logica + $detalle->cantidad;
        //         $lote->estado = '1';
        //         $lote->update();
        //     }
        //     $mensaje = true;
        // };

        return $mensaje;
    }

    //DEVOLVER LOTE
    public function returnLote(Request $request)
    {
        $data = $request->all();
        $lote_id = $data['lote_id'];
        $lote = LoteProducto::find($lote_id);

        if ($lote) {
            return response()->json([
                'success' => true,
                'lote' => $lote,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function updateLote(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $lote_id = $data['lote_id'];
            $cantidad_sum = $data['cantidad_sum'];
            $cantidad_res = $data['cantidad_res'];
            $lote = LoteProducto::find($lote_id);

            if ($lote) {
                $lote->cantidad_logica = $lote->cantidad_logica + ($cantidad_sum - $cantidad_res);
                $lote->update();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'lote' => $lote,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
