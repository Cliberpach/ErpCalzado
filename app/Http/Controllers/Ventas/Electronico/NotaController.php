<?php

namespace App\Http\Controllers\Ventas\Electronico;

use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use App\Events\NotifySunatEvent;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Empresa\Numeracion;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\ErrorNota;
use App\Ventas\Nota;
use App\Ventas\NotaDetalle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Luecano\NumeroALetras\NumeroALetras;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use stdClass;

class NotaController extends Controller
{
    public function index($id)
    {
        $dato = "Message";
        broadcast(new NotifySunatEvent($dato));
        $documento = Documento::find($id);
        return view('ventas.notas.index',compact('documento'));
    }

    public function index_dev($id)
    {
        $documento = Documento::find($id);
        $nota_venta = true;
        return view('ventas.notas.index',compact('documento','nota_venta'));

    }

    public function getNotes($id)
    {
        $notas = Nota::where('tipo_nota',"0")->where('documento_id', $id)->orderBy('id','DESC')->get();

        $coleccion = collect([]);
        foreach($notas as $nota){

            $coleccion->push([
                'id' => $nota->id,
                'tipo_venta' => $nota->documento->tipo_venta,
                'documento_afectado' => $nota->numDocfectado,
                'fecha_emision' =>  $nota->fechaEmision,
                'numero-sunat' =>  $nota->serie.'-'.$nota->correlativo,
                'cliente' => $nota->tipo_documento_cliente.': '.$nota->documento_cliente.' - '.$nota->cliente,
                'empresa' => $nota->empresa,
                'monto' => 'S/. '.number_format($nota->mtoImpVenta, 2, '.', ''),
                'sunat' => $nota->sunat,
                'tipo_nota' => $nota->tipo_nota,
                'estado' => $nota->estado,
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function create(Request $request)
    {
        $documento = Documento::findOrFail($request->documento_id);
        $fecha_hoy = Carbon::now()->toDateString();
        $productos = Producto::where('estado', 'ACTIVO')->get();
        //NOTAS
        //CREDITO -> 0
        //DEBITO -> 1
        
        if($request->nota == '0')
        {
            if( $request->nota_venta)
            {
                $nota_venta = true;
                return view('ventas.notas.credito.create',[
                    'documento' => $documento,
                    'fecha_hoy' => $fecha_hoy,
                    'productos' => $productos,
                    'nota_venta' => $nota_venta,
                    'tipo_nota' => '0'
                ]);
            }
            else
            {
                return view('ventas.notas.credito.create',[
                    'documento' => $documento,
                    'fecha_hoy' => $fecha_hoy,
                    'productos' => $productos,
                    'tipo_nota' => '0'
                ]);
            }

        }
    }

    public function getDetalles($id)
    {

        $detalles = Detalle::where('estado','ACTIVO')->where('documento_id',$id)->get();
        $coleccion_detalles = [];
        foreach ($detalles as $detalle) {
            if($detalle->cantidad - $detalle->detalles->sum('cantidad') > 0)
            {
                $item = [];
                $item['codigo_producto']   =   $detalle->codigo_producto;
                $item['producto_id']       =   $detalle->producto_id;
                $item['color_id']          =   $detalle->color_id;
                $item['talla_id']          =   $detalle->talla_id;   
                $item['producto_nombre']   =   $detalle->nombre_producto;
                $item['color_nombre']      =   $detalle->nombre_color;
                $item['talla_nombre']      =   $detalle->nombre_talla;
                $item['modelo_nombre']     =   $detalle->nombre_modelo;
                $item['cantidad']          =   $detalle->cantidad - $detalle->detalles->sum('cantidad');
                // $item['cantidad']       =   $detalle->cantidad;
                $item['precio_unitario']   =   $detalle->precio_unitario;
                $item['importe']           =   $detalle->importe;
                $coleccion_detalles[]   =   $item;
            }
        }

        // $coleccion = collect();
        // foreach($detalles as $item)
        // {
        //     if($item->cantidad - $item->detalles->sum('cantidad') > 0)
        //     {
        //         $coleccion->push([
        //             'id' => $item->id,
        //             'cantidad' => $item->cantidad - $item->detalles->sum('cantidad'),
        //             'descripcion' => $item->lote->producto->nombre,
        //             'precio_unitario' => $item->precio_nuevo,
        //             'importe_venta' => $item->valor_venta,
        //             'editable' => 0
        //         ]);
        //     }
        // }
        //return DataTables::of($coleccion)->make(true);

        return response()->json([
            'success' => true,
            'id_doc' => $id,
            'detalles' => $coleccion_detalles
        ]);
    }

    public function obtenerFecha($fecha)
    {
        $date = strtotime($fecha);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision.'T'.$hora_emision.'-05:00';

        return $fecha;
    }

    public function convertirTotal($total)
    {
        $formatter = new NumeroALetras();
        $convertir = $formatter->toInvoice($total, 2, 'SOLES');
        return $convertir;
    }

    public function store(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $data = $request->all();
            $rules = [
                'documento_id' => 'required',
                'fecha_emision'=> 'required',
                'tipo_nota'=> 'required',
                'cliente'=> 'required',
                'des_motivo' => 'required',
                'cod_motivo' => 'required',

            ];
            $message = [
                'fecha_emision.required' => 'El campo Fecha de Emisión es obligatorio.',
                'tipo_nota.required' => 'El campo Tipo es obligatorio.',
                'cod_motivo.required' => 'El campo Tipo Nota de Crédito es obligatorio.',
                'cliente.required' => 'El campo Cliente es obligatorio.',
                'des_motivo.required' => 'El campo Motivo es obligatorio.',
            ];

            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => true,
                    'data' => array('mensajes' => $validator->getMessageBag()->toArray())
                ]);

            }

         

            $documento = Documento::find($request->get('documento_id'));

            $igv = $documento->igv ? $documento->igv : 18;

            $nota = new Nota();
            $nota->documento_id = $documento->id;
            $nota->tipDocAfectado = $documento->tipoDocumento();
            $nota->numDocfectado = $documento->serie.'-'.$documento->correlativo;
            $nota->codMotivo = $request->get('cod_motivo');
            $nota->desMotivo =  $request->get('des_motivo');

            $nota->tipoDoc = $request->get('tipo_nota') === '0' ? '07' : '08';
            $nota->fechaEmision = $request->get('fecha_emision');

            //EMPRESA
            $nota->ruc_empresa =  $documento->ruc_empresa;
            $nota->empresa =  $documento->empresa;
            $nota->direccion_fiscal_empresa =  $documento->direccion_fiscal_empresa;
            $nota->empresa_id =  $documento->empresa_id; //OBTENER NUMERACION DE LA EMPRESA

            //CLIENTE
            $nota->cod_tipo_documento_cliente =  $documento->tipoDocumentoCliente();
            $nota->tipo_documento_cliente =  $documento->tipo_documento_cliente;
            $nota->documento_cliente =  $documento->documento_cliente;
            $nota->direccion_cliente =  $documento->direccion_cliente;
            $nota->cliente =  $documento->cliente;

            $nota->sunat = '0';
            $nota->tipo_nota = $request->get('tipo_nota'); //0 -> CREDITO

            $nota->mtoOperGravadas = $request->get('sub_total_nuevo');
            $nota->mtoIGV = $request->get('total_igv_nuevo');
            $nota->totalImpuestos = $request->get('total_igv_nuevo');
            $nota->mtoImpVenta =  $request->get('total_nuevo');

            $nota->value = self::convertirTotal($request->get('total_nuevo'));
            $nota->code = '1000';
            $nota->user_id = auth()->user()->id;
            $nota->save();

     
            //Llenado de los articulos
            $productosJSON = $request->get('productos_tabla');
            $productotabla = json_decode($productosJSON);

       

            foreach ($productotabla as $producto) {
                

                    $detalle =  DB::select('select cdd.id 
                                from cotizacion_documento_detalles as cdd
                                where cdd.documento_id=? and cdd.producto_id=?  and cdd.color_id=?
                                and cdd.talla_id=?',[
                                    $request->get('documento_id'),
                                    $producto->producto_id,
                                    $producto->color_id,
                                    $producto->talla_id
                                ]);

                    NotaDetalle::create(
                        [
                            'nota_id' => $nota->id,
                            'detalle_id' => $detalle[0]->id,
                            'codProducto' => $producto->codigo_producto,
                            'unidad' => 'NIU',
                            'descripcion' => $producto->modelo_nombre.'-'.$producto->producto_nombre.'-'.$producto->color_nombre.'-'.$producto->talla_nombre,
                            'cantidad' => $producto->cantidad_devolver,
                            'mtoBaseIgv' => ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad_devolver,
                            'porcentajeIgv' => 18,
                            'igv' => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)) )) * $producto->cantidad_devolver,
                            'tipAfeIgv' => 10,
                            'totalImpuestos' => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)) )) * $producto->cantidad_devolver,
                            'mtoValorVenta' => ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad_devolver,
                            'mtoValorUnitario'=>  $producto->precio_unitario / (1 + ($documento->igv/100)),
                            'mtoPrecioUnitario' => $producto->precio_unitario,
                            'producto_id'   =>  $producto->producto_id,
                            'color_id'      =>  $producto->color_id,
                            'talla_id'      =>  $producto->talla_id
                        ]);

                    //===== AUMENTANDO EL STOCK LOGICO Y FISICO ====
                    DB::table('producto_color_tallas')
                    ->where('producto_id', $producto->producto_id)
                    ->where('color_id', $producto->color_id)
                    ->where('talla_id', $producto->talla_id)
                    ->update([
                        'stock_logico' => DB::raw('stock_logico + ' . $producto->cantidad_devolver),
                        'stock' => DB::raw('stock + ' . $producto->cantidad_devolver)
                    ]);
                
                if($request->cod_motivo == '01'){   //==== EN CASO DEVOLUCIÓN TOTAL ====
                    $documento->sunat = '2';
                    $documento->update();    
                }
            //     if($request->cod_motivo != '01')
            //     {
            //         if($producto->editable == 1)
            //         {
            //             $detalle = Detalle::find($producto->id);
            //             $lote = LoteProducto::findOrFail($detalle->lote_id);
            //             NotaDetalle::create([
            //                 'nota_id' => $nota->id,
            //                 'detalle_id' => $detalle->id,
            //                 'codProducto' => $lote->producto->codigo,
            //                 'unidad' => $lote->producto->getMedida(),
            //                 'descripcion' => $lote->producto->nombre.' - '.$lote->codigo,
            //                 'cantidad' => $producto->cantidad,

            //                 'mtoBaseIgv' => ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad,
            //                 'porcentajeIgv' => 18,
            //                 'igv' => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)) )) * $producto->cantidad,
            //                 'tipAfeIgv' => 10,

            //                 'totalImpuestos' => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)) )) * $producto->cantidad,
            //                 'mtoValorVenta' => ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad,
            //                 'mtoValorUnitario'=>  $producto->precio_unitario / (1 + ($documento->igv/100)),
            //                 'mtoPrecioUnitario' => $producto->precio_unitario,
            //             ]);

            //             $lote->cantidad = $lote->cantidad + $producto->cantidad;
            //             $lote->cantidad_logica = $lote->cantidad_logica + $producto->cantidad;
            //             if ($lote->cantidad > 0) {
            //                 $lote->estado = '1';
            //             }
            //             $lote->update();
            //         }
            //     }
            //     else
            //     {
            //         $detalle = Detalle::find($producto->id);
            //         $lote = LoteProducto::findOrFail($detalle->lote_id);
            //         NotaDetalle::create([
            //             'nota_id' => $nota->id,
            //             'detalle_id' => $detalle->id,
            //             'codProducto' => $lote->producto->codigo,
            //             'unidad' => $lote->producto->getMedida(),
            //             'descripcion' => $lote->producto->nombre.' - '.$lote->codigo,
            //             'cantidad' => $producto->cantidad,

            //             'mtoBaseIgv' => ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad,
            //             'porcentajeIgv' => 18,
            //             'igv' => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)) )) * $producto->cantidad,
            //             'tipAfeIgv' => 10,

            //             'totalImpuestos' => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)) )) * $producto->cantidad,
            //             'mtoValorVenta' => ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad,
            //             'mtoValorUnitario'=>  $producto->precio_unitario / (1 + ($documento->igv/100)),
            //             'mtoPrecioUnitario' => $producto->precio_unitario,
            //         ]);

            //         $lote->cantidad = $lote->cantidad + $producto->cantidad;
            //         $lote->cantidad_logica = $lote->cantidad_logica + $producto->cantidad;
            //         if ($lote->cantidad > 0) {
            //             $lote->estado = '1';
            //         }
            //         $lote->update();

            //         $documento->sunat = '2';
            //         $documento->update();
            //     }
            }
          
            //==== REGISTRO DE ACTIVIDAD ====
            $descripcion = "SE AGREGÓ UNA NOTA DE DEBITO CON LA FECHA: ". Carbon::parse($nota->fechaEmision)->format('d/m/y');
            $gestion = "NOTA DE DEBITO";
            crearRegistro($nota , $descripcion , $gestion);

            $envio_prev = self::sunat_prev($nota->id);

            if(!isset($request->nota_venta))
            {
                if(!$envio_prev['success']){
                    DB::rollBack();
                    return response()->json([
                         'success' => false,
                         'mensaje'=> $envio_prev['mensaje']
                    ]);
                }
            }

            DB::commit();
            if(!isset($request->nota_venta))
            {
                // $envio_post = self::sunat_post($nota->id);
            }

            $text = 'Nota de crédito creada, se creo un egreso con el monto de la nota de credito.';

            if(isset($request->nota_venta))
            {
                 $text = 'Nota de devolución creada, se creo un egreso con el monto de la nota de devolución.';
            }

            //========== ENVIO A SUNAT ======
            //======= ENVIAR A SUNAT CUANDO NO SEA NOTA DE VENTA ========
            if(!$request->has('nota_venta')){
                $this->sunat($nota->id);
            }

            Session::flash('success', $text);
            return response()->json([
                 'success' => true,
                 'nota_id'=> $nota->id
            ]);

        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje'=> $e->getMessage(),
                'excepcion' => $e->getMessage()
            ]);
        }
    
    }


    public function obtenerLeyenda($nota)
    {
        //CREAR LEYENDA DEL COMPROBANTE
        $arrayLeyenda = Array();
        $arrayLeyenda[] = array(
            "code" => $nota->code,
            "value" => $nota->value
        );
        return $arrayLeyenda;
    }

    public function obtenerProductos($detalles)
    {

        $arrayProductos = Array();
        for($i = 0; $i < count($detalles); $i++){

            $arrayProductos[] = array(
                "codProducto" => $detalles[$i]->codProducto,
                "unidad" => $detalles[$i]->unidad,
                "descripcion"=> $detalles[$i]->descripcion,
                "cantidad" => $detalles[$i]->cantidad,

                'mtoBaseIgv' => floatval($detalles[$i]->mtoBaseIgv),
                'porcentajeIgv'=> floatval( $detalles[$i]->porcentajeIgv),
                'igv' => floatval($detalles[$i]->igv),
                'tipAfeIgv' => floatval($detalles[$i]->tipAfeIgv),

                'totalImpuestos' => floatval($detalles[$i]->totalImpuestos),
                'mtoValorVenta' => floatval($detalles[$i]->mtoValorVenta),
                'mtoValorUnitario' => floatval($detalles[$i]->mtoValorUnitario),
                'mtoPrecioUnitario' => floatval($detalles[$i]->mtoPrecioUnitario),

            );
        }

        return $arrayProductos;
    }

    public function show($id)
    {
        $nota = Nota::with(['documento'])->findOrFail($id);
        $empresa = Empresa::first();
        $detalles = NotaDetalle::where('nota_id',$id)->get();
        //ARREGLO COMPROBANTE
        $arreglo_nota = array(
            "tipDocAfectado" => $nota->tipDocAfectado,
            "numDocfectado" => $nota->numDocfectado,
            "codMotivo" => $nota->codMotivo,
            "desMotivo" => $nota->desMotivo,
            "tipoDoc" => $nota->tipoDoc,
            "fechaEmision" => self::obtenerFecha($nota->fechaEmision),
            "tipoMoneda" => $nota->tipoMoneda,
            "serie" => $nota->sunat==1 ? $nota->serie : '000',
            "correlativo" => $nota->sunat==1 ? $nota->correlativo : '000',
            "company" => array(
                "ruc" => $nota->ruc_empresa,
                "razonSocial" => $nota->empresa,
                "address" => array(
                    "direccion" => $nota->direccion_fiscal_empresa,
                )),

            "client" => array(
                "tipoDoc" =>  $nota->cod_tipo_documento_cliente,
                "numDoc" => $nota->documento_cliente,
                "rznSocial" => $nota->cliente,
                "address" => array(
                    "direccion" => $nota->direccion_cliente,
                )
            ),

            "mtoOperGravadas" =>  floatval($nota->mtoOperGravadas),
            "mtoIGV" => floatval($nota->mtoIGV),
            "totalImpuestos" => floatval($nota->totalImpuestos),
            "mtoImpVenta" => floatval($nota->mtoImpVenta),
            "ublVersion" =>  $nota->ublVersion,
            "details" => self::obtenerProductos($detalles),
            "legends" =>  self::obtenerLeyenda($nota),
        );

        $nota_json= json_encode($arreglo_nota);
        $data = pdfNotaapi($nota_json);

        $name = $nota->serie.'-'.$nota->correlativo.'.pdf';

        if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'))) {
            mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'));
        }

        if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'.DIRECTORY_SEPARATOR.'notas'))) {
            mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'.DIRECTORY_SEPARATOR.'notas'));
        }

        $pathToFile = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'.DIRECTORY_SEPARATOR.'notas'.DIRECTORY_SEPARATOR.$name);

        file_put_contents($pathToFile, $data);

        if($nota->ruta_qr === null)
        {
            /*************************************** */
            $arreglo_qr = array(
                "ruc" => $nota->ruc_empresa,
                "tipo" => $nota->tipoDoc,
                "serie" => $nota->serie,
                "numero" => $nota->correlativo,
                "emision" => self::obtenerFecha($nota->fechaEmision),
                "igv" => 18,
                "total" => floatval($nota->mtoImpVenta),
                "clienteTipo" => $nota->cod_tipo_documento_cliente,
                "clienteNumero" => $nota->documento_cliente
            );

            $data_qr = generarQrApi(json_encode($arreglo_qr), $nota->empresa_id);

            $name_qr = $nota->serie."-".$nota->correlativo.'.svg';

            if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'qrs_nota'))) {
                mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'qrs_nota'));
            }

            $pathToFile_qr = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'qrs_nota'.DIRECTORY_SEPARATOR.$name_qr);

            file_put_contents($pathToFile_qr, $data_qr);

            $nota->ruta_qr = 'public/qrs_nota/'.$name_qr;
            $nota->update();
            /*************************************** */
        }

        //file_put_contents($pathToFile, $data);
        //return response()->file($pathToFile);
        $legends = self::obtenerLeyenda($nota);
        $legends = json_encode($legends,true);
        $legends = json_decode($legends,true);

        $pdf = PDF::loadview('ventas.notas.impresion.comprobante_normal_nuevo',[
            'nota' => $nota,
            'detalles' => $detalles,
            'moneda' => $nota->tipoMoneda,
            'empresa' => $empresa,
            "legends" =>  $legends,
            ])->setPaper('a4')->setWarnings(false);

        $pdf->save(storage_path().'/app/public/comprobantessiscom/notas/'.$name);
        return $pdf->stream($name);
    }

    public function show_dev($id)
    {
        $nota = Nota::with(['documento'])->findOrFail($id);
        $empresa = Empresa::first();
        $detalles = NotaDetalle::where('nota_id',$id)->get();

        $legends = self::obtenerLeyenda($nota);
        $legends = json_encode($legends,true);
        $legends = json_decode($legends,true);

        $name = 'NOTA-'.$nota->id;

        if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'))) {
            mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'));
        }

        if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'.DIRECTORY_SEPARATOR.'notas'))) {
            mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'comprobantessiscom'.DIRECTORY_SEPARATOR.'notas'));
        }

        $pdf = PDF::loadview('ventas.notas.impresion.comprobante_normal_nuevo',[
            'nota' => $nota,
            'detalles' => $detalles,
            'moneda' => $nota->tipoMoneda,
            'empresa' => $empresa,
            "legends" =>  $legends,
            "nota_venta" => 1,
            ])->setPaper('a4')->setWarnings(false);

        $pdf->save(public_path().'/storage/comprobantessiscom/notas/'.$name);
        return $pdf->stream($name);
    }

    public function obtenerCorrelativo($nota, $numeracion)
    {
        if(empty($nota->correlativo))
        {
            $serie_comprobantes = DB::table('empresa_numeracion_facturaciones')
            ->join('empresas','empresas.id','=','empresa_numeracion_facturaciones.empresa_id')
            ->join('cotizacion_documento','cotizacion_documento.empresa_id','=','empresas.id')
            ->join('nota_electronica','nota_electronica.documento_id','=','cotizacion_documento.id')
            ->when($nota->tipo_nota, function ($query, $request) {
                if ($request == '1') {
                    return $query->where('empresa_numeracion_facturaciones.tipo_comprobante',131);
                }else{
                    return $query->where('empresa_numeracion_facturaciones.tipo_comprobante',130);
                }
            })
            ->where('empresa_numeracion_facturaciones.empresa_id',$nota->empresa_id)
            ->whereIn('nota_electronica.tipDocAfectado', ['01', '03'])
            ->select('nota_electronica.*','empresa_numeracion_facturaciones.*')
            ->orderBy('nota_electronica.correlativo','DESC')
            ->get();

            if ($nota->tipDocAfectado == '04') {
                $serie_comprobantes = DB::table('empresa_numeracion_facturaciones')
                ->join('empresas', 'empresas.id', '=', 'empresa_numeracion_facturaciones.empresa_id')
                ->join('cotizacion_documento', 'cotizacion_documento.empresa_id', '=', 'empresas.id')
                ->join('nota_electronica', 'nota_electronica.documento_id', '=', 'cotizacion_documento.id')
                ->when($nota->tipo_nota, function ($query, $request) {
                    if ($request == '1') {
                        return $query->where('empresa_numeracion_facturaciones.tipo_comprobante', 131);
                    } else {
                        return $query->where('empresa_numeracion_facturaciones.tipo_comprobante', 130);
                    }
                })
                    ->where('empresa_numeracion_facturaciones.empresa_id', $nota->empresa_id)
                    ->where('nota_electronica.tipDocAfectado', '04')
                    ->select('nota_electronica.*', 'empresa_numeracion_facturaciones.*')
                    ->orderBy('nota_electronica.correlativo', 'DESC')
                    ->get();
            }


            if (count($serie_comprobantes) === 1) {
                //OBTENER EL DOCUMENTO INICIADO
                $nota->correlativo = $numeracion->numero_iniciar;
                $nota->serie = $numeracion->serie;//$numeracion->serie;
                $nota->update();

                //ACTUALIZAR LA NUMERACION (SE REALIZO EL INICIO)
                self::actualizarNumeracion($numeracion);
                return $nota->correlativo;

            }else{
                //NOTA ES NUEVO EN SUNAT
                if($nota->sunat != '1' ){
                    $ultimo_comprobante = $serie_comprobantes->first();
                    $nota->correlativo = $ultimo_comprobante->correlativo+1;
                    $nota->serie = $numeracion->serie;//$numeracion->serie;
                    $nota->update();

                    //ACTUALIZAR LA NUMERACION (SE REALIZO EL INICIO)
                    self::actualizarNumeracion($numeracion);
                    return $nota->correlativo;
                }
            }
        }
        else
        {
            return $nota->correlativo;
        }
    }

    public function actualizarNumeracion($numeracion)
    {
        $numeracion->emision_iniciada = '1';
        $numeracion->update();
    }

    public function numeracion($nota)
    {
        // $nota = Nota::findOrFail($id);

        if ($nota->tipo_nota == '1') {
            $numeracion = Numeracion::where('empresa_id',$nota->empresa_id)->where('estado','ACTIVO')->where('tipo_comprobante',131)->first();
        }else{
            $numeracion = Numeracion::where('empresa_id',$nota->empresa_id)->where('estado','ACTIVO')->where('tipo_comprobante',130)->first();
        }

        if ($numeracion) {

            $resultado = ($numeracion)->exists();
            $enviar = [
                'existe' => ($resultado == true) ? true : false,
                'numeracion' => $numeracion,
                'correlativo' => self::obtenerCorrelativo($nota,$numeracion)
            ];
            $collection = collect($enviar);
            return  $collection;
        }
    }

    public function sunat($id)
    {
        try
        {
            $nota = Nota::findOrFail($id);
            $documento = Documento::find($nota->documento_id);
            $detalles = NotaDetalle::where('nota_id',$id)->get();
            //OBTENER CORRELATIVO DE LA NOTA CREDITO / DEBITO
            $existe = self::numeracion($nota);
            $nota = Nota::findOrFail($id);
            if($existe){
                if ($existe->get('existe') == true) {
                    if ($nota->sunat != '1') {
                        //ARREGLO COMPROBANTE
                        $arreglo_nota = array(
                            "tipDocAfectado" => $nota->tipDocAfectado,
                            "numDocfectado" => $nota->numDocfectado,
                            "codMotivo" => $nota->codMotivo,
                            "desMotivo" => $nota->desMotivo,
                            "tipoDoc" => $nota->tipoDoc,
                            "fechaEmision" => self::obtenerFecha($nota->fechaEmision),
                            "tipoMoneda" => $nota->tipoMoneda,
                            "serie" => $nota->tipDocAfectado === '03' ? 'BB01' : 'FF01',//$existe->get('numeracion')->serie,
                            "correlativo" => $nota->correlativo,
                            "company" => array(
                                "ruc" => $nota->ruc_empresa,
                                "razonSocial" => $nota->empresa,
                                "address" => array(
                                    "direccion" => $nota->direccion_fiscal_empresa,
                                )),


                            "client" => array(
                                "tipoDoc" =>  $nota->cod_tipo_documento_cliente,
                                "numDoc" => $nota->documento_cliente,
                                "rznSocial" => $nota->cliente,
                                "address" => array(
                                    "direccion" => $nota->direccion_cliente,
                                )
                            ),

                            "mtoOperGravadas" =>  floatval($nota->mtoOperGravadas),
                            "mtoIGV" => floatval($nota->mtoIGV),
                            "totalImpuestos" => floatval($nota->totalImpuestos),
                            "mtoImpVenta" => floatval($nota->mtoImpVenta),
                            "ublVersion" =>  $nota->ublVersion,
                            "details" => self::obtenerProductos($detalles),
                            "legends" =>  self::obtenerLeyenda($nota),
                        );

                        // return $arreglo_nota;
                        //OBTENER JSON DEL COMPROBANTE EL CUAL SE ENVIARA A SUNAT
                        $data = enviarNotaapi(json_encode($arreglo_nota));

                        //RESPUESTA DE LA SUNAT EN JSON
                        $json_sunat = json_decode($data);

                        if ($json_sunat->sunatResponse->success == true) {
                            if($json_sunat->sunatResponse->cdrResponse->code == "0")
                            {
                                $nota->sunat = '1';
                                $respuesta_cdr = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                                $respuesta_cdr = json_decode($respuesta_cdr, true);
                                $nota->getCdrResponse = $respuesta_cdr;

                                $data_comprobante = pdfNotaapi(json_encode($arreglo_nota));
                                $name = $existe->get('numeracion')->serie . "-" . $nota->correlativo . '.pdf';

                                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'))) {
                                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'));
                                }

                                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'nota'))) {
                                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'nota'));
                                }

                                $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'nota' . DIRECTORY_SEPARATOR . $name);

                                /*************************************** */
                                $arreglo_qr = array(
                                    "ruc" => $nota->ruc_empresa,
                                    "tipo" => $nota->tipoDoc,
                                    "serie" => $nota->serie,
                                    "numero" => $nota->correlativo,
                                    "emision" => self::obtenerFecha($nota->fechaEmision),
                                    "igv" => 18,
                                    "total" => floatval($nota->mtoImpVenta),
                                    "clienteTipo" => $nota->cod_tipo_documento_cliente,
                                    "clienteNumero" => $nota->documento_cliente
                                );

                                $data_qr = generarQrApi(json_encode($arreglo_qr), $nota->empresa_id);

                                $name_qr = $nota->serie . "-" . $nota->correlativo . '.svg';

                                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs_nota'))) {
                                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs_nota'));
                                }
                                $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs_nota' . DIRECTORY_SEPARATOR . $name_qr);

                                file_put_contents($pathToFile_qr, $data_qr);
                                /*************************************** */

                                file_put_contents($pathToFile, $data_comprobante);
                                $nota->hash = $json_sunat->hash;
                                $nota->ruta_qr = 'public/qrs_nota/' . $name_qr;
                                $nota->nombre_comprobante_archivo = $name;
                                $nota->ruta_comprobante_archivo = 'public/sunat/nota/' . $name;
                                $nota->update();


                                //Registro de actividad
                                $descripcion = "SE AGREGÓ LA NOTA ELECTRONICA: " . $existe->get('numeracion')->serie . "-" . $nota->correlativo;
                                $gestion = "NOTAS ELECTRONICAS";
                                crearRegistro($nota, $descripcion, $gestion);

                                Session::flash('success', 'Nota enviada a Sunat con exito.');
                                Session::flash('sunat_exito', '1');
                                Session::flash('id_sunat', $json_sunat->sunatResponse->cdrResponse->id);
                                Session::flash('descripcion_sunat', $json_sunat->sunatResponse->cdrResponse->description);

                              
                                return redirect()->route('ventas.notas', $documento->id)->with('sunat_exito', 'success');
                            }
                            else {
                                $nota->sunat = '0';
                                $id_sunat = $json_sunat->sunatResponse->cdrResponse->code;
                                $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

                                $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                                $respuesta_error = json_decode($respuesta_error, true);
                                $nota->getCdrResponse = $respuesta_error;

                                $nota->update();
                                
                                Session::flash('error', 'Nota electronica sin exito en el envio a sunat.');
                                Session::flash('sunat_error', '1');
                                Session::flash('id_sunat', $id_sunat);
                                Session::flash('descripcion_sunat', $descripcion_sunat);

                                return redirect()->route('ventas.notas', $documento->id)->with('sunat_error', 'error');
                            }
                        }else{

                            //COMO SUNAT NO LO ADMITE VUELVE A SER 0
                            $nota->sunat = '0';
                            $nota->regularize = '1';

                            if ($json_sunat->sunatResponse->error) {
                                $id_sunat = $json_sunat->sunatResponse->error->code;
                                $descripcion_sunat = $json_sunat->sunatResponse->error->message;
                                $obj_erro = new stdClass;
                                $obj_erro->code = $json_sunat->sunatResponse->error->code;
                                $obj_erro->description = $json_sunat->sunatResponse->error->message;
                                $respuesta_error = json_encode($obj_erro, true);
                                $respuesta_error = json_decode($respuesta_error, true);
                                $nota->getRegularizeResponse = $respuesta_error;
                            }else {
                                $id_sunat = $json_sunat->sunatResponse->cdrResponse->id;
                                $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;
                                $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                                $respuesta_error = json_decode($respuesta_error, true);
                                $nota->getCdrResponse = $respuesta_error;
                            };

                            $nota->update();
                            Session::flash('error', 'Nota electronica sin exito en el envio a sunat.');
                            Session::flash('sunat_error', '1');
                            Session::flash('id_sunat', $id_sunat);
                            Session::flash('descripcion_sunat', $descripcion_sunat);

                            return redirect()->route('ventas.notas', $documento->id)->with('sunat_error', 'error');
                        }
                    }else{
                        $nota->sunat = '1';
                        $nota->update();
                        Session::flash('error','Nota fue enviado a Sunat.');
                        return redirect()->route('ventas.notas',$documento->id)->with('sunat_existe', 'error');
                    }
                }else{
                    Session::flash('error','Nota no registrado en la empresa.');
                    return redirect()->route('ventas.notas',$documento->id)->with('sunat_existe', 'error');
                }
            }else{
                Session::flash('error','Empresa sin parametros para emitir comprobantes electronicos');
                return redirect()->route('ventas.notas',$documento->id);
            }
        }
        catch(Exception $e)
        {
            Session::flash('error','Ocurrio un error, si el error persiste contactar al administrador del sistema.');
            return redirect()->route('ventas.notas',$documento->id);
        }
    }

    public function sunat_prev($id)
    {
        try
        {
            $nota = Nota::findOrFail($id);
            //OBTENER CORRELATIVO DE LA NOTA CREDITO / DEBITO
            $existe = self::numeracion($nota);
            if($existe){
                if ($existe->get('existe') == true) {
                    return array('success' => true,'mensaje' => 'Nota validada.');
                }else{
                    return array('success' => false,'mensaje' => 'Nota de crédito no se encuentra registrado en la empresa.');
                }
            }else{
                return array('success' => false,'mensaje' => 'Empresa sin parametros para emitir Nota de crédito electrónica.');
            }
        }
        catch(Exception $e)
        {
            return array('success' => false,'mensaje' => $e->getMessage());
        }
    }

    public function sunat_post($id)
    {
        try
        {
            $nota = Nota::findOrFail($id);
            $detalles = NotaDetalle::where('nota_id',$id)->get();
            if ($nota->sunat != '1') {
                //ARREGLO COMPROBANTE
                $arreglo_nota = array(
                    "tipDocAfectado" => $nota->tipDocAfectado,
                    "numDocfectado" => $nota->numDocfectado,
                    "codMotivo" => $nota->codMotivo,
                    "desMotivo" => $nota->desMotivo,
                    "tipoDoc" => $nota->tipoDoc,
                    "fechaEmision" => self::obtenerFecha($nota->fechaEmision),
                    "tipoMoneda" => $nota->tipoMoneda,
                    "serie" => $nota->serie,
                    "correlativo" => $nota->correlativo,
                    "company" => array(
                        "ruc" => $nota->ruc_empresa,
                        "razonSocial" => $nota->empresa,
                        "address" => array(
                            "direccion" => $nota->direccion_fiscal_empresa,
                        )),


                    "client" => array(
                        "tipoDoc" =>  $nota->cod_tipo_documento_cliente,
                        "numDoc" => $nota->documento_cliente,
                        "rznSocial" => $nota->cliente,
                        "address" => array(
                            "direccion" => $nota->direccion_cliente,
                        )
                    ),

                    "mtoOperGravadas" =>  floatval($nota->mtoOperGravadas),
                    "mtoIGV" => floatval($nota->mtoIGV),
                    "totalImpuestos" => floatval($nota->totalImpuestos),
                    "mtoImpVenta" => floatval($nota->mtoImpVenta),
                    "ublVersion" =>  $nota->ublVersion,
                    "details" => self::obtenerProductos($detalles),
                    "legends" =>  self::obtenerLeyenda($nota),
                );
                //OBTENER JSON DEL COMPROBANTE EL CUAL SE ENVIARA A SUNAT
                $data = enviarNotaapi(json_encode($arreglo_nota));

                //RESPUESTA DE LA SUNAT EN JSON
                $json_sunat = json_decode($data);
                if ($json_sunat->sunatResponse->success == true) {
                    if($json_sunat->sunatResponse->cdrResponse->code == "0") {
                        $nota->sunat = '1';
                        $respuesta_cdr = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_cdr = json_decode($respuesta_cdr, true);
                        $nota->getCdrResponse = $respuesta_cdr;

                        $data_comprobante = pdfNotaapi(json_encode($arreglo_nota));
                        $name = $nota->serie . "-" . $nota->correlativo . '.pdf';

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'));
                        }

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'nota'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'nota'));
                        }

                        $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'nota' . DIRECTORY_SEPARATOR . $name);

                        /*************************************** */
                        $arreglo_qr = array(
                            "ruc" => $nota->ruc_empresa,
                            "tipo" => $nota->tipoDoc,
                            "serie" => $nota->serie,
                            "numero" => $nota->correlativo,
                            "emision" => self::obtenerFecha($nota->fechaEmision),
                            "igv" => 18,
                            "total" => floatval($nota->mtoImpVenta),
                            "clienteTipo" => $nota->cod_tipo_documento_cliente,
                            "clienteNumero" => $nota->documento_cliente
                        );

                        $data_qr = generarQrApi(json_encode($arreglo_qr), $nota->empresa_id);

                        $name_qr = $nota->serie . "-" . $nota->correlativo . '.svg';

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'));
                        }

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs_nota'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs_nota'));
                        }

                        $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs_nota' . DIRECTORY_SEPARATOR . $name_qr);

                        file_put_contents($pathToFile_qr, $data_qr);
                        /*************************************** */

                        file_put_contents($pathToFile, $data_comprobante);
                        $nota->hash = $json_sunat->hash;
                        $nota->ruta_qr = 'public/qrs_nota/' . $name_qr;
                        $nota->nombre_comprobante_archivo = $name;
                        $nota->ruta_comprobante_archivo = 'public/sunat/nota/' . $name;
                        $nota->update();


                        //Registro de actividad
                        $descripcion = "SE AGREGÓ LA NOTA ELECTRONICA: " . $nota->serie . "-" . $nota->correlativo;
                        $gestion = "NOTAS ELECTRONICAS";
                        crearRegistro($nota, $descripcion, $gestion);

                        return array('success' => true, 'mensaje' => 'Nota de crédito enviada a Sunat con exito.');
                    }
                    else {
                        $nota->sunat = '0';
                        $id_sunat = $json_sunat->sunatResponse->cdrResponse->code;
                        $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

                        $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $nota->getCdrResponse = $respuesta_error;

                        $nota->update();

                        return array('success' => false, 'mensaje' => $descripcion_sunat);
                    }
                }else{

                    //COMO SUNAT NO LO ADMITE VUELVE A SER 0
                    // $nota->correlativo = null;
                    // $nota->serie = null;
                    $nota->sunat = '0';
                    $nota->regularize = '1';

                    if ($json_sunat->sunatResponse->error) {
                        $id_sunat = $json_sunat->sunatResponse->error->code;
                        $descripcion_sunat = $json_sunat->sunatResponse->error->message;
                        $obj_erro = new stdClass;
                        $obj_erro->code = $json_sunat->sunatResponse->error->code;
                        $obj_erro->description = $json_sunat->sunatResponse->error->message;
                        $respuesta_error = json_encode($obj_erro, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $nota->getRegularizeResponse = $respuesta_error;

                    }else {
                        $id_sunat = $json_sunat->sunatResponse->cdrResponse->id;
                        $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;
                        $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $nota->getCdrResponse = $respuesta_error;
                    };


                    $errorNota = new ErrorNota();
                    $errorNota->nota_id = $nota->id;
                    $errorNota->tipo = 'sunat-envio';
                    $errorNota->descripcion = 'Error al enviar a sunat';
                    $errorNota->ecxepcion = $descripcion_sunat;
                    $errorNota->save();


                    $nota->update();

                    return array('success' => false, 'mensaje' => $descripcion_sunat);
                }
            }else{
                $nota->sunat = '1';
                $nota->update();return array('success' => false, 'mensaje' => 'Nota de crédito ya fue enviado a Sunat.');
            }
        }
        catch(Exception $e)
        {
            $nota = Nota::find($id);

            $errorNota = new ErrorNota();
            $errorNota->nota_id = $nota->id;
            $errorNota->tipo = 'sunat-envio';
            $errorNota->descripcion = 'Error al enviar a sunat';
            $errorNota->ecxepcion = $e->getMessage();
            $errorNota->save();
            return array('success' => false, 'mensaje' => $e->getMessage());
        }
    }
}
