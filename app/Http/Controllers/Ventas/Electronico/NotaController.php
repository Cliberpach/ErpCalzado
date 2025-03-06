<?php

namespace App\Http\Controllers\Ventas\Electronico;

use App\Almacenes\Almacen;
use App\Almacenes\Color;
use App\Almacenes\Kardex;
use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
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

use Greenter\Model\Response\BillResult;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Ws\Services\SunatEndpoints;

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use App\Greenter\Utils\Util;
use DateTime;
use Illuminate\Support\Facades\Auth;

class NotaController extends Controller
{
    public function index($id)
    {
        $dato = "Message";
        broadcast(new NotifySunatEvent($dato));
        $documento          =    Documento::find($id);

        $pedido_facturado   =   DB::select('select p.facturado 
                                from pedidos as p 
                                where p.id = ? and p.facturado = "SI"',[$documento->pedido_id]);

        if(count($pedido_facturado) === 0){
            $pedido_facturado = false;
        }else{
            $pedido_facturado = true;
        }


        return view('ventas.notas.index',compact('documento','pedido_facturado'));
    }

    public function index_dev($id)
    {
        $documento          =   Documento::find($id);
        $nota_venta         =   true;
        $pedido_facturado   =   DB::select('select p.facturado 
                                from pedidos as p 
                                where p.id = ? and p.facturado = "SI"',[$documento->pedido_id]);

        if(count($pedido_facturado) === 0){
            $pedido_facturado = false;
        }else{
            $pedido_facturado = true;
        }

        return view('ventas.notas.index',compact('documento','nota_venta','pedido_facturado'));

    }

    public function getNotes($id)
    {
        $notas = Nota::where('tipo_nota',"0")->where('documento_id', $id)->orderBy('id','DESC')->get();

        $coleccion = collect([]);
        foreach($notas as $nota){

            $coleccion->push([
                'id'                => $nota->id,
                'tipo_venta_id'     => $nota->documento->tipo_venta_id,
                'documento_afectado'=> $nota->numDocfectado,
                'fecha_emision'     =>  $nota->fechaEmision,
                'numero-sunat'      =>  $nota->serie.'-'.$nota->correlativo,
                'cliente'           => $nota->tipo_documento_cliente.': '.$nota->documento_cliente.' - '.$nota->cliente,
                'empresa'           => $nota->empresa,
                'monto'             => 'S/. '.number_format($nota->mtoImpVenta, 2, '.', ''),
                'sunat'             => $nota->sunat,
                'tipo_nota'         => $nota->tipo_nota,
                'estado'            => $nota->estado,
                'cdr_response_code' =>  $nota->cdr_response_code
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

        //======= VALIDANDO QUE LA EMPRESA TENGA ACTIVA EL TIPO DE NOTA DE CRÉDITO A EMITIR =======
        $validacion =   $this->isActive($documento,Auth::user()->sede_id);

        if(count($validacion->existe) === 0){
            Session::flash('nota_credito_error', 'LA EMPRESA NO TIENE ACTIVA LA EMISIÓN DE '.$validacion->message.' ,Debe configurar en Mantenimiento\Empresas');
            return back();
        }

        
        if($request->nota == '0')
        {
            if( $request->nota_venta)
            {
                $nota_venta = true;

                if($documento->pedido_id){
                    return view('ventas.notas.credito.pedidos.create',[
                        'documento' => $documento,
                        'fecha_hoy' => $fecha_hoy,
                        'productos' => $productos,
                        'nota_venta' => $nota_venta,
                        'tipo_nota' => '0'
                    ]);
                }

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

                if($documento->pedido_id){
                    return view('ventas.notas.credito.pedidos.create',[
                        'documento' => $documento,
                        'fecha_hoy' => $fecha_hoy,
                        'productos' => $productos,
                        'tipo_nota' => '0'
                    ]);
                }

                return view('ventas.notas.credito.create',[
                    'documento' => $documento,
                    'fecha_hoy' => $fecha_hoy,
                    'productos' => $productos,
                    'tipo_nota' => '0'
                ]);
            }

        }

    }

    private function isActive($documento,$sede_id){

        //====== VERIFICANDO SI NOTAS DE CRÉDITO DEL TIPO DE DOCUMENTO ESTÁ ACTIVO EN LA EMPRESA =========
        $tipo_venta     =   $documento->tipo_venta_id;
        $parametro      =   null; 
        $message        =   "";

        //===== 127:FACTURA | 128:BOLETA | 129:NOTA DE VENTA ======
        if($tipo_venta == 127){
            $parametro  =   "FF";   //====== NOTA CRÉDITO FACTURA =======
            $message    =   "NOTAS DE CRÉDITO DE FACTURAS ELECTRÓNICAS";
        }
        if($tipo_venta == 128){
            $parametro  =   "BB";   //======= NOTA CRÉDITO BOLETA =====
            $message    =   "NOTAS DE CRÉDITO DE BOLETAS ELECTRÓNICAS";
        }
        if($tipo_venta == 129){
            $parametro  =   "NN";   //===== NOTA DEVOLUCIÓN =======
            $message    =   "NOTAS DE DEVOLUCIÓN DE NOTAS DE VENTA";
        }

        $existe =   DB::table('empresa_numeracion_facturaciones as enf')
                    ->select('enf.serie')
                    ->join('tabladetalles as td', 'td.id', '=', 'enf.tipo_comprobante')
                    ->where('td.parametro', $parametro)
                    ->where('td.tabla_id', 21)
                    ->where('enf.sede_id',$sede_id)
                    ->where('enf.estado','ACTIVO')
                    ->get();

       
        return (object)["existe"=>$existe,"message"=>$message];
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
                $item['precio_unitario_nuevo']   =   $detalle->precio_unitario_nuevo;
                $item['importe_nuevo']           =   $detalle->importe_nuevo;
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

            
            $documento                  =   Documento::find($request->get('documento_id'));

            $igv                        =   $documento->igv ? $documento->igv : 18;

            //============ CALCULAR CORRELATIVO =======
            $sede_id                    =   Auth::user()->sede_id;
            $datos_correlativo          =   NotaController::getCorrelativo($sede_id,$documento->tipo_venta_id);
            $almacen                    =   Almacen::find($documento->almacen_id);


            $nota                       =   new Nota();
            $nota->documento_id         =   $documento->id;
            $nota->tipDocAfectado       =   $documento->tipoDocumento();
            $nota->numDocfectado        =   $documento->serie.'-'.$documento->correlativo;
            $nota->codMotivo            =   $request->get('cod_motivo');
            $nota->desMotivo            =   $request->get('des_motivo');

            $nota->tipoDoc              =   $request->get('tipo_nota') === '0' ? '07' : '08';
            $nota->fechaEmision         =   $request->get('fecha_emision');

            //EMPRESA
            $nota->ruc_empresa              =  $documento->ruc_empresa;
            $nota->empresa                  =  $documento->empresa;
            $nota->direccion_fiscal_empresa =  $documento->direccion_fiscal_empresa;
            $nota->empresa_id               =  $documento->empresa_id; //OBTENER NUMERACION DE LA EMPRESA

            //CLIENTE
            $nota->cod_tipo_documento_cliente   =   $documento->tipoDocumentoCliente();
            $nota->tipo_documento_cliente       =   $documento->tipo_documento_cliente;
            $nota->documento_cliente            =   $documento->documento_cliente;
            $nota->direccion_cliente            =   $documento->direccion_cliente;
            $nota->cliente                      =   $documento->cliente;

            $nota->sunat                        =   '0';
            $nota->tipo_nota                    =   $request->get('tipo_nota'); //0 -> CREDITO

            $nota->mtoOperGravadas              =   $request->get('sub_total_nuevo');
            $nota->mtoIGV                       =   $request->get('total_igv_nuevo');
            $nota->totalImpuestos               =   $request->get('total_igv_nuevo');
            $nota->mtoImpVenta                  =   $request->get('total_nuevo');

            $nota->value        =   self::convertirTotal($request->get('total_nuevo'));
            $nota->code         =   '1000';
            $nota->user_id      =   auth()->user()->id;

            $nota->serie        =   $datos_correlativo->serie;
            $nota->correlativo  =   $datos_correlativo->correlativo;

            $nota->sede_id          =   $sede_id;
            $nota->almacen_id       =   $almacen->id;
            $nota->almacen_nombre   =   $almacen->descripcion;
            $nota->save();

     
            //Llenado de los articulos
            $productosJSON = $request->get('productos_tabla');
            $productotabla = json_decode($productosJSON);


            //========== PRODUCTOS PRESENTES EN LA NOTA DE CRÉDITO O DEVOLUCIÓN ===========
            foreach ($productotabla as $producto) {
                
                $detalle =  DB::select('select 
                            cdd.id 
                            from cotizacion_documento_detalles as cdd
                            where 
                            cdd.documento_id = ? 
                            AND cdd.almacen_id = ?
                            AND cdd.producto_id = ?  
                            AND cdd.color_id = ?
                            AND cdd.talla_id = ?',
                            [
                                $request->get('documento_id'),
                                $documento->almacen_id,
                                $producto->producto_id,
                                $producto->color_id,
                                $producto->talla_id
                            ]);

                $nota_detalle                    = new NotaDetalle();
                $nota_detalle->nota_id           = $nota->id;
                $nota_detalle->detalle_id        = $detalle[0]->id;
                $nota_detalle->codProducto       = $producto->codigo_producto;
                $nota_detalle->unidad            = 'NIU';
                $nota_detalle->descripcion       = $producto->modelo_nombre.'-'.$producto->producto_nombre.'-'.$producto->color_nombre.'-'.$producto->talla_nombre;
                $nota_detalle->cantidad          = $producto->cantidad_devolver;
                $nota_detalle->mtoBaseIgv        = ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad_devolver;
                $nota_detalle->porcentajeIgv     = 18;
                $nota_detalle->igv               = ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)))) * $producto->cantidad_devolver;
                $nota_detalle->tipAfeIgv         = 10;
                $nota_detalle->totalImpuestos    = ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)))) * $producto->cantidad_devolver;
                $nota_detalle->mtoValorVenta     = ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad_devolver;
                $nota_detalle->mtoValorUnitario  = $producto->precio_unitario / (1 + ($documento->igv/100));
                $nota_detalle->mtoPrecioUnitario = $producto->precio_unitario;
                $nota_detalle->almacen_id        = $almacen->id;
                $nota_detalle->almacen_nombre    = $almacen->descripcion;
                $nota_detalle->producto_id       = $producto->producto_id;
                $nota_detalle->color_id          = $producto->color_id;
                $nota_detalle->talla_id          = $producto->talla_id;
                $nota_detalle->save();
                            

                /*NotaDetalle::create([
                    'nota_id'           => $nota->id,
                    'detalle_id'        => $detalle[0]->id,
                    'codProducto'       => $producto->codigo_producto,
                    'unidad'            => 'NIU',
                    'descripcion'       => $producto->modelo_nombre.'-'.$producto->producto_nombre.'-'.$producto->color_nombre.'-'.$producto->talla_nombre,
                    'cantidad'          => $producto->cantidad_devolver,
                    'mtoBaseIgv'        => ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad_devolver,
                    'porcentajeIgv'     => 18,
                    'igv'               => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)) )) * $producto->cantidad_devolver,
                    'tipAfeIgv'         => 10,
                    'totalImpuestos'    => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv/100)) )) * $producto->cantidad_devolver,
                    'mtoValorVenta'     => ($producto->precio_unitario / (1 + ($documento->igv/100))) * $producto->cantidad_devolver,
                    'mtoValorUnitario'  =>  $producto->precio_unitario / (1 + ($documento->igv/100)),
                    'mtoPrecioUnitario' =>  $producto->precio_unitario,
                    'almacen_id'        =>  $almacen->id,
                    'almacen_nombre'    =>  $almacen->descripcion,
                    'producto_id'       =>  $producto->producto_id,
                    'color_id'          =>  $producto->color_id,
                    'talla_id'          =>  $producto->talla_id
                ]);*/

              
                //========= 01:FACTURA 03:BOLETA =======
                //======= PREGUNTAR SI EL DOC DE VENTA ESTÁ ASOCIADO A UN PEDIDO =====
                if( $documento->pedido_id ){

                    //======== EN CASO SEA EL DOC VENTA DE LA FACTURACIÓN DE UN PEDIDO ========
                    if($documento->tipo_doc_venta_pedido === "FACTURACION"){

                        //======== OBTENER EL PRODUCTO DE LA NOTA ELECTRÓNICA EN EL DETALLE DEL PEDIDO ======
                        $producto_en_pedido =   DB::select('select 
                                                pd.almacen_id,
                                                pd.producto_id,
                                                pd.color_id,
                                                pd.talla_id,
                                                pd.cantidad_atendida,
                                                pd.cantidad_pendiente
                                                from pedidos_detalles as pd
                                                where 
                                                pd.pedido_id = ? 
                                                AND pd.almacen_id = ?
                                                AND pd.producto_id = ? 
                                                AND pd.color_id = ? 
                                                AND pd.talla_id = ?',
                                                [
                                                    $documento->pedido_id,
                                                    $documento->almacen_id,
                                                    $producto->producto_id,
                                                    $producto->color_id,
                                                    $producto->talla_id
                                                ]);

    
                        //====== COMPROBAR SI EL PRODUCTO ESTÁ PRESENTE EN EL DETALLE DEL PEDIDO =======
                        if(count($producto_en_pedido) === 1){
                            
                            $cantidad_reponer   =   0;   

                            //======= CASO I:  CANT DEVOLVER <= CANTIDAD PENDIENTE ======
                            if($producto->cantidad_devolver <= $producto_en_pedido[0]->cantidad_pendiente ){

                                //======== NO SE VA REPONER STOCK =======
                                $cantidad_reponer   =   0;

                                //======= LE BAJAMOS A LA CANTIDAD PENDIENTE =====
                                //====== INCREMENTAR LA CANTIDAD DEVUELTA Y LA CANTIDAD PENDIENTE DEVUELTA =======
                                DB::table('pedidos_detalles')
                                ->where('pedido_id', $documento->pedido_id)
                                ->where('almacen_id',$producto_en_pedido[0]->almacen_id)
                                ->where('producto_id', $producto_en_pedido[0]->producto_id)
                                ->where('color_id', $producto_en_pedido[0]->color_id)
                                ->where('talla_id', $producto_en_pedido[0]->talla_id)
                                ->update([
                                    'cantidad_pendiente'                => DB::raw('cantidad_pendiente - ' . $producto->cantidad_devolver),
                                    'cantidad_devuelta'                 => DB::raw('cantidad_devuelta + ' . $producto->cantidad_devolver),
                                    'cantidad_pendiente_devuelta'       => DB::raw('cantidad_pendiente_devuelta + ' . $producto->cantidad_devolver),
                                    'updated_at'                        => Carbon::now()
                                ]);

                            }

                            //======= CASO II: CANT DEVOLVER > CANTIDAD PENDIENTE ========
                            if($producto->cantidad_devolver >  $producto_en_pedido[0]->cantidad_pendiente ){

                                //====== BAJAMOS LA CANTIDAD PENDIENTE A 0 =====
                                $nueva_cantidad_pendiente   =   0;

                                //====== OBTENEMOS LO QUE FALTÓ BAJARLE A LA CANTIDAD PENDIENTE =====
                                $cantidad_falta_disminuir   =   $producto->cantidad_devolver - $producto_en_pedido[0]->cantidad_pendiente;
                                
                                //======= FIJAMOS LA CANTIDAD A REPONER DE STOCK ======
                                $cantidad_reponer           =   $cantidad_falta_disminuir;


                                //======= REPONEMOS STOCK ======
                                DB::table('producto_color_tallas')
                                ->where('almacen_id',$producto_en_pedido[0]->almacen_id)
                                ->where('producto_id', $producto_en_pedido[0]->producto_id)
                                ->where('color_id', $producto_en_pedido[0]->color_id)
                                ->where('talla_id', $producto_en_pedido[0]->talla_id)
                                ->update([
                                    'stock_logico'  => DB::raw('stock_logico + ' . $cantidad_reponer),
                                    'stock'         => DB::raw('stock + ' . $cantidad_reponer)
                                ]);

                                //======== ACTUALIZAMOS DETALLE DEL PEDIDO ======
                                DB::table('pedidos_detalles')
                                ->where('pedido_id', $documento->pedido_id)
                                ->where('almacen_id', $producto_en_pedido[0]->almacen_id)
                                ->where('producto_id', $producto_en_pedido[0]->producto_id)
                                ->where('color_id', $producto_en_pedido[0]->color_id)
                                ->where('talla_id', $producto_en_pedido[0]->talla_id)
                                ->update([
                                    'cantidad_repuesta'                 =>  DB::raw('cantidad_repuesta + ' . $cantidad_falta_disminuir),
                                    'cantidad_atendida_devuelta'        =>  DB::raw('cantidad_atendida_devuelta + ' . $cantidad_falta_disminuir),
                                    'cantidad_devuelta'                 =>  DB::raw('cantidad_devuelta + ' . $producto->cantidad_devolver),
                                    'cantidad_pendiente_devuelta'       =>  DB::raw('cantidad_pendiente_devuelta + ' . $producto_en_pedido[0]->cantidad_pendiente),  
                                    'cantidad_pendiente'                =>  $nueva_cantidad_pendiente,
                                    'updated_at'                        =>  Carbon::now()
                                ]);

                            }

                        }                        
                   
                    }


                    //======== EN CASO SEA EL DOC VENTA DE LA ATENCIÓN DE UN PEDIDO =======
                    if($documento->tipo_doc_venta_pedido === "ATENCION"){

                        //========== TRABAJAR CON PURA CANTIDAD ATENDIDA ======
                        //======= DEBIDO A QUE EL PEDIDO PUEDE MODIFICARSE EN CASO SE REQUIERA BAJAR LAS CANTIDADES PENDIENTES ======
                        
                        //======== OBTENER EL PRODUCTO DE LA NOTA ELECTRÓNICA EN EL DETALLE DEL PEDIDO ======
                        $producto_en_pedido =   DB::select('select 
                                                pd.almacen_id,
                                                pd.producto_id,
                                                pd.color_id,
                                                pd.talla_id,
                                                pd.cantidad_atendida,
                                                pd.cantidad_pendiente
                                                from pedidos_detalles as pd
                                                where 
                                                pd.pedido_id = ? 
                                                AND pd.almacen_id = ?
                                                AND pd.producto_id = ? 
                                                AND pd.color_id = ? 
                                                AND pd.talla_id = ?',
                                                [
                                                    $documento->pedido_id,
                                                    $documento->almacen_id,
                                                    $producto->producto_id,
                                                    $producto->color_id,
                                                    $producto->talla_id
                                                ]);


                        //====== COMPROBAR SI EL PRODUCTO ESTÁ PRESENTE EN EL DETALLE DEL PEDIDO =======
                        if(count($producto_en_pedido) === 1){

                            //======= REPONEMOS STOCK ======
                            DB::table('producto_color_tallas')
                            ->where('almacen_id', $producto_en_pedido[0]->almacen_id)
                            ->where('producto_id', $producto_en_pedido[0]->producto_id)
                            ->where('color_id', $producto_en_pedido[0]->color_id)
                            ->where('talla_id', $producto_en_pedido[0]->talla_id)
                            ->update([
                                 'stock_logico' => DB::raw('stock_logico + ' . $producto->cantidad_devolver),
                                 'stock' => DB::raw('stock + ' . $producto->cantidad_devolver)
                             ]);

                            //========== ACTUALIZANDO DETALLE DEL PEDIDO =======
                            DB::table('pedidos_detalles')
                            ->where('pedido_id', $documento->pedido_id)
                            ->where('almacen_id', $producto_en_pedido[0]->almacen_id)
                            ->where('producto_id', $producto_en_pedido[0]->producto_id)
                            ->where('color_id', $producto_en_pedido[0]->color_id)
                            ->where('talla_id', $producto_en_pedido[0]->talla_id)
                            ->update([
                                'cantidad_repuesta'                 =>  DB::raw('cantidad_repuesta + ' . $producto->cantidad_devolver),
                                'cantidad_atendida_devuelta'        =>  DB::raw('cantidad_atendida_devuelta + ' . $producto->cantidad_devolver),
                                'cantidad_devuelta'                 =>  DB::raw('cantidad_devuelta + ' . $producto->cantidad_devolver),
                                'updated_at'                        =>  Carbon::now()
                            ]);
                        
                        }

                    }

                    //=========== GUARDAR EL PEDIDO ID EN LA NOTA ELECTRÓNICA PARA FÁCIL ACCESO ======
                    $nota->pedido_id    =   $documento->pedido_id;
                    $nota->update();
                    
                }

                //======== SI EL DOC DE VENTA NO ESTÁ ASOCIADO A UN PEDIDO ========
                if(!$documento->pedido_id){

                    //===== AUMENTAR EL STOCK LOGICO Y FISICO ====
                    DB::table('producto_color_tallas')
                    ->where('almacen_id', $documento->almacen_id)
                    ->where('producto_id', $producto->producto_id)
                    ->where('color_id', $producto->color_id)
                    ->where('talla_id', $producto->talla_id)
                    ->update([
                        'stock_logico'    => DB::raw('stock_logico + ' . $producto->cantidad_devolver),
                        'stock'           => DB::raw('stock + ' . $producto->cantidad_devolver)
                    ]);

                }

                if($request->cod_motivo == '01'){   //==== EN CASO DEVOLUCIÓN TOTAL ====
                    $documento->sunat = '2';
                    $documento->update();    
                }

                //======== KARDEX =======
                $producto_color_talla   =   DB::table('producto_color_tallas')
                                            ->where('almacen_id',$documento->almacen_id)
                                            ->where('producto_id', $producto->producto_id)
                                            ->where('color_id', $producto->color_id)
                                            ->where('talla_id', $producto->talla_id)
                                            ->first();


                

                $item_producto              =   Producto::find($producto->producto_id);
                $item_color                 =   Color::find($producto->color_id);
                $item_talla                 =   Talla::find($producto->talla_id);

                $kardex                     =   new Kardex();
                $kardex->sede_id            =   $sede_id;
                $kardex->almacen_id         =   $almacen->id;
                $kardex->producto_id        =   $producto->producto_id;
                $kardex->color_id           =   $producto->color_id;
                $kardex->talla_id           =   $producto->talla_id;
                $kardex->almacen_nombre     =   $almacen->descripcion;
                $kardex->producto_nombre    =   $item_producto->nombre;
                $kardex->color_nombre       =   $item_color->descripcion;
                $kardex->talla_nombre       =   $item_talla->descripcion;
                $kardex->cantidad           =   $producto->cantidad_devolver;
                $kardex->precio             =   $nota_detalle->mtoPrecioUnitario;
                $kardex->importe            =   $nota_detalle->mtoPrecioUnitario * $producto->cantidad_devolver;
                $kardex->accion             =   'INGRESO';
                $kardex->stock              =   $producto_color_talla->stock;
                $kardex->numero_doc         =   'NI-'.$nota->id;
                $kardex->documento_id       =   $nota->id;
                $kardex->registrador_id     =   Auth::user()->id;
                $kardex->registrador_nombre =   Auth::user()->usuario;
                $kardex->fecha              =   $nota->fechaEmision;
                $kardex->descripcion        =   'DEVOLUCIÓN';
                $kardex->save();

                $sumatoria           = NotaDetalle::where('detalle_id',$nota_detalle->detalle_id)->sum('cantidad');
                $detalle_venta       = Detalle::findOrFail($nota_detalle->detalle_id);
                if($detalle_venta->cantidad == $sumatoria)
                {
                    $detalle_venta->estado = 'ANULADO';
                    $detalle_venta->update();
                }
          
            }

            //========== ACTUALIZAR ESTADO FACTURACIÓN A INICIADA ======
            DB::table('empresa_numeracion_facturaciones')
            ->where('empresa_id', Empresa::find(1)->id) 
            ->where('sede_id', $sede_id) 
            ->where('tipo_comprobante', $datos_correlativo->tipo_comprobante->id) 
            ->where('emision_iniciada', '0') 
            ->where('estado','ACTIVO')
            ->update([
                  'emision_iniciada'       => '1',
                  'updated_at'             => Carbon::now()
            ]);
          
            //==== REGISTRO DE ACTIVIDAD ====
            $descripcion = "SE AGREGÓ UNA NOTA DE DEBITO CON LA FECHA: ". Carbon::parse($nota->fechaEmision)->format('d/m/y');
            $gestion = "NOTA DE DEBITO";
            crearRegistro($nota , $descripcion , $gestion);

            //======== OBTENER CORRELATIVO ======
            //$envio_prev = self::sunat_prev($nota->id,$documento->tipo_venta);
            
           
            /*if(!$envio_prev['success']){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mensaje'=> $envio_prev['mensaje']
                ]);
            }else{
                $nota->serie        =   $envio_prev['serie'];
                $nota->correlativo  =   $envio_prev['correlativo'];
                $nota->update();

                //======= SI LA EMISIÓN NO HA INICIADO, ACTUALIZAR =======
                if($envio_prev['model_numeracion']->emision_iniciada == 0){
                    DB::table('empresa_numeracion_facturaciones')
                    ->where('id', $envio_prev['model_numeracion']->id)
                    ->update(['emision_iniciada' => '1']);
                }
            }*/
            
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

            Session::flash('success', $text);
            return response()->json([
                'success' => true,
                'nota_id'=> $nota->id
            ]);

        }
        catch(\Throwable $e)
        {
            DB::rollBack();
           
            return response()->json([
                'success' => false,
                'mensaje'=> $e->getMessage(),
                'excepcion' => $e->getMessage()
            ]);
        }


    }


    /*public function store_old(Request $request)
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

          
            //======== OBTENER CORRELATIVO ======
            $envio_prev = self::sunat_prev($nota->id,$documento->tipo_venta);
            
           
            if(!$envio_prev['success']){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mensaje'=> $envio_prev['mensaje']
                ]);
            }else{
                $nota->serie        =   $envio_prev['serie'];
                $nota->correlativo  =   $envio_prev['correlativo'];
                $nota->update();

                //======= SI LA EMISIÓN NO HA INICIADO, ACTUALIZAR =======
                if($envio_prev['model_numeracion']->emision_iniciada == 0){
                    DB::table('empresa_numeracion_facturaciones')
                    ->where('id', $envio_prev['model_numeracion']->id)
                    ->update(['emision_iniciada' => '1']);
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

            Session::flash('success', $text);
            return response()->json([
                'success' => true,
                'nota_id'=> $nota->id
            ]);

        }
        catch(\Throwable $e)
        {
            DB::rollBack();
           
            return response()->json([
                'success' => false,
                'mensaje'=> $e->getMessage(),
                'excepcion' => $e->getMessage()
            ]);
        }


        // //======= ENVÍO A SUNAT =======
        // //======= ENVIAR A SUNAT CUANDO NO SEA NOTA DE VENTA ========
        // if(!$request->has('nota_venta')){
        //     $res_send_sunat   = $this->sunat($nota->id,'fetch');
        //     //dd($res_send_sunat);
        // }

    }*/


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
        $nota       = Nota::with(['documento'])->findOrFail($id);
        $empresa    = Empresa::first();
        $detalles   = NotaDetalle::where('nota_id',$id)->get();

        

    
        $legends = self::obtenerLeyenda($nota);
        $legends = json_encode($legends,true);
        $legends = json_decode($legends,true);

        $pdf = PDF::loadview('ventas.notas.impresion.comprobante_normal_nuevo',[
            'nota'      =>  $nota,
            'detalles'  =>  $detalles,
            'moneda'    =>  $nota->tipoMoneda,
            'empresa'   =>  $empresa,
            "legends"   =>  $legends,
            ])->setPaper('a4')->setWarnings(false);

        //$pdf->save(storage_path().'/app/public/comprobantessiscom/notas/'.$name);
        return $pdf->stream($nota->serie.'-'.$nota->correlativo);
    }

    public function show_dev($id)
    {
        $nota       = Nota::with(['documento'])->findOrFail($id);
        $empresa    = Empresa::first();
        $detalles   = NotaDetalle::where('nota_id',$id)->get();

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

        $pdf->save(storage_path().'/app/public/comprobantessiscom/notas/'.$name);
        //$pdf->save(public_path().'/storage/comprobantessiscom/notas/'.$name);
        return $pdf->stream($name);
    }

    public function obtenerCorrelativo($nota, $numeracion)
    {
        //dd($numeracion->serie);
        //========= OJO : NUMERACIÓN CONTIENE  SERIE , EMISIÓN INICIADA 1:0 , NUMERO INICIAR ===========

        //======== SI LA NOTA AÚN NO TIENE CORRELATIVO =======
        if(!$nota->correlativo){
            //====== PARA NOTAS DE CRÉDITO ======
            if($nota->tipo_nota == '0'){

                //===== EMISIÓN NO INICIADA =====
                if($numeracion->emision_iniciada == "0"){
                    //====== CORRELATIVO IGUAL AL NRO INICIAR =======
                    return $numeracion->numero_iniciar;
                }

                //======== EMISIÓN YA INICIADA ======
                if($numeracion->emision_iniciada == "1"){
                    //====== CORRELATIVO SERÁ EL SIGUIENTE A LA ULTIMA NOTA ELECTRONICA ==========//
                    //====== DEL MISMO TIPO BB01,FF01,NN01 ======= //
                    $ultima_nota =   DB::select('select ne.correlativo
                                            from nota_electronica as ne
                                            where ne.serie=?
                                            order by ne.id desc',[$numeracion->serie]);
                    
                    return $ultima_nota[0]->correlativo+1;
                }
            }
        }
        // if(empty($nota->correlativo))
        // {
        //     $serie_comprobantes = DB::table('empresa_numeracion_facturaciones')
        //     ->join('empresas','empresas.id','=','empresa_numeracion_facturaciones.empresa_id')
        //     ->join('cotizacion_documento','cotizacion_documento.empresa_id','=','empresas.id')
        //     ->join('nota_electronica','nota_electronica.documento_id','=','cotizacion_documento.id')
        //     ->when($nota->tipo_nota, function ($query, $request) {
        //         //======= TIPO NOTA :0 CRÉDITO - 1:DÉBITO =======
        //         if ($request == '1') {
        //             //======= 131 NOTA DÉBITO ========
        //             return $query->where('empresa_numeracion_facturaciones.tipo_comprobante',131);
        //         }else{
        //             //====== 130 NOTA CRÉDITO =========
        //             return $query->where('empresa_numeracion_facturaciones.tipo_comprobante',130);
        //         }
        //     })
        //     ->where('empresa_numeracion_facturaciones.empresa_id',$nota->empresa_id)
        //     ->whereIn('nota_electronica.tipDocAfectado', ['01', '03'])
        //     ->select('nota_electronica.*','empresa_numeracion_facturaciones.*')
        //     ->orderBy('nota_electronica.correlativo','DESC')
        //     ->get();

        //     if ($nota->tipDocAfectado == '04') {
        //         $serie_comprobantes = DB::table('empresa_numeracion_facturaciones')
        //         ->join('empresas', 'empresas.id', '=', 'empresa_numeracion_facturaciones.empresa_id')
        //         ->join('cotizacion_documento', 'cotizacion_documento.empresa_id', '=', 'empresas.id')
        //         ->join('nota_electronica', 'nota_electronica.documento_id', '=', 'cotizacion_documento.id')
        //         ->when($nota->tipo_nota, function ($query, $request) {
        //             if ($request == '1') {
        //                 return $query->where('empresa_numeracion_facturaciones.tipo_comprobante', 131);
        //             } else {
        //                 return $query->where('empresa_numeracion_facturaciones.tipo_comprobante', 130);
        //             }
        //         })
        //             ->where('empresa_numeracion_facturaciones.empresa_id', $nota->empresa_id)
        //             ->where('nota_electronica.tipDocAfectado', '04')
        //             ->select('nota_electronica.*', 'empresa_numeracion_facturaciones.*')
        //             ->orderBy('nota_electronica.correlativo', 'DESC')
        //             ->get();
        //     }


        //     if (count($serie_comprobantes) === 1) {
        //         //OBTENER EL DOCUMENTO INICIADO
        //         $nota->correlativo  = $numeracion->numero_iniciar;
        //         $nota->serie        = $numeracion->serie;//$numeracion->serie;
        //         $nota->update();

        //         //ACTUALIZAR LA NUMERACION (SE REALIZO EL INICIO)
        //         self::actualizarNumeracion($numeracion);
        //         return $nota->correlativo;

        //     }else{
        //         //NOTA ES NUEVO EN SUNAT
        //         if($nota->sunat != '1' ){
        //             $ultimo_comprobante = $serie_comprobantes->first();
        //             $nota->correlativo = $ultimo_comprobante->correlativo+1;
        //             $nota->serie = $numeracion->serie;//$numeracion->serie;
        //             $nota->update();

        //             //ACTUALIZAR LA NUMERACION (SE REALIZO EL INICIO)
        //             self::actualizarNumeracion($numeracion);
        //             return $nota->correlativo;
        //         }
        //     }
        // }
        // else
        // {
        //     return $nota->correlativo;
        // }
    }

    public function actualizarNumeracion($numeracion)
    {
        $numeracion->emision_iniciada = '1';
        $numeracion->update();
    }

    public function numeracion($nota,$tipo_venta)
    {
        // $nota = Nota::findOrFail($id);

        //======== BUSCAR SI ESTÁ ACTIVADA LA EMISIÓN DE NOTAS DE CREDITO O DÉBITO EN LA EMPRESA ======
        $numeracion         =   null;
        $tipo_comprobante   =   null;

        //======= 1=NOTA DÉBITO ======
        if ($nota->tipo_nota == '1') {
            $numeracion = Numeracion::where('empresa_id',$nota->empresa_id)->where('estado','ACTIVO')->where('tipo_comprobante',131)->first();
        }else{
            //===== NOTA CRÉDITO = 0 ======

            //======== FACTURAS ======
            if($tipo_venta == 127){
                //====== NOTA CRÉDITO FACTURA FF01 =====
                //====== BUSCANDO CODIGO DE NOTA CREDITO FACTURA =====
                $tipo_comprobante =   DB::select('select td.id from tabladetalles as td
                                        where td.descripcion="NOTA DE CRÉDITO FACTURA"');
            }

            //======== BOLETAS ======
            if($tipo_venta == 128){
                $tipo_comprobante =   DB::select('select td.id from tabladetalles as td
                                        where td.descripcion="NOTA DE CRÉDITO BOLETA"');
                //====== NOTA CRÉDITO BOLETA BB01 =====
            }
          
            //======== NOTAS DE VENTA ======
            if($tipo_venta == 129){
                //====== NOTA DE DEVOLUCIÓN NN01 =====
                $tipo_comprobante =   DB::select('select td.id from tabladetalles as td
                                        where td.descripcion="NOTA DE DEVOLUCIÓN"');
            }

            if(count($tipo_comprobante)>0){
                $numeracion = Numeracion::where('empresa_id',$nota->empresa_id)->where('estado','ACTIVO')
                ->where('tipo_comprobante',$tipo_comprobante[0]->id)->first();
            } 
          
        }

        if ($numeracion) {
            $resultado = ($numeracion)->exists();
            if($resultado){
                $enviar = [
                    'existe'        => true,
                    'numeracion'    => $numeracion,
                    'correlativo'   => self::obtenerCorrelativo($nota,$numeracion),
                    'serie'         => $numeracion->serie
                ];
            }else{
                $enviar = [
                    'existe'        => false
                ];
            }
           
            $collection = collect($enviar);
            return  $collection;
        }
    }

    public function sunat($id){
     
        try {
            $util       = Util::getInstance();
            $nota       = Nota::find($id);
            $documento  = Documento::find($nota->documento_id);
            $detalles   = NotaDetalle::where('nota_id',$id)->get();
    
    
            if(!$nota){
                Session::flash('nota_credito_error', 'NO SE ENCONTRÓ LA NOTA DE CRÉDITO EN LA BASE DE DATOS');
                return back();
            }
            if(!$documento){
                Session::flash('nota_credito_error', 'NO SE ENCONTRÓ EL DOC AFECTADO EN LA BASE DE DATOS');
                return back();
            }
            if(count($detalles) === 0){
                Session::flash('nota_credito_error', 'LA NOTA DE CRÉDITO NO TIENE DETALLES');
                return back();
            }
    
            $des_motivo =   '-';
            if($nota->codMotivo == '01'){
                $des_motivo =   "ANULACION DE LA OPERACION";
            }
            if($nota->codMotivo == '07'){
                $des_motivo =   "DEVOLUCION POR ITEM";
            }
    
            //====== CONSTRUIR CLIENTE ======
            $client = (new Client())
            ->setTipoDoc($nota->cod_tipo_documento_cliente)
            ->setNumDoc($nota->documento_cliente)
            ->setRznSocial($nota->cliente)
            ->setAddress((new Address())
            ->setDireccion($nota->direccion_cliente));
    
            //======== CONSTRUYENDO CABEZERA =====
            $note = new Note();
            $note
                ->setUblVersion('2.1')
                ->setTipoDoc('07') // Tipo Doc: Nota de Credito
                ->setSerie($nota->serie) // Serie NCR
                ->setCorrelativo($nota->correlativo) // Correlativo NCR
                ->setFechaEmision(new DateTime($nota->created_at))
                ->setTipDocAfectado($nota->tipDocAfectado) // Tipo Doc: 03-BOLETA 01-FACTURA
                ->setNumDocfectado($nota->numDocfectado) // Boleta: Serie-Correlativo
                ->setCodMotivo($nota->codMotivo) // Catalogo. 09    01:ANULACION DE LA OPERACION    07:DEVOLUCION POR ITEM
                ->setDesMotivo($des_motivo)
                ->setTipoMoneda('PEN')
                ->setCompany($util->shared->getCompany($nota->sede_id))
                ->setClient($client)
                ->setMtoOperGravadas($nota->mtoOperGravadas)
                ->setMtoIGV($nota->mtoIGV)
                ->setTotalImpuestos($nota->totalImpuestos)
                ->setMtoImpVenta($nota->mtoImpVenta);
          
            
            //====== CONSTRUYENDO DETALLE =====
            $items  =   [];
            foreach ($detalles as $detalle) {
                $item1 = new SaleDetail();
                $item1->setCodProducto($detalle->codProducto)
                    ->setUnidad($detalle->unidad)
                    ->setCantidad($detalle->cantidad)
                    ->setDescripcion($detalle->descripcion)
                    ->setMtoBaseIgv($detalle->mtoBaseIgv)
                    ->setPorcentajeIgv($detalle->porcentajeIgv)
                    ->setIgv($detalle->igv)
                    ->setTipAfeIgv((int)$detalle->tipAfeIgv)
                    ->setTotalImpuestos($detalle->totalImpuestos)
                    ->setMtoValorVenta($detalle->mtoValorVenta)
                    ->setMtoValorUnitario($detalle->mtoValorUnitario)
                    ->setMtoPrecioUnitario($detalle->mtoPrecioUnitario);
                
                $items[]    =   $item1;
            }

            //======= CONSTRUYENDO LEGENDA ======
            $legenda_nota    = 'SON'.' '. $nota->value;
    
            $legend = new Legend();
            $legend->setCode('1000')
                ->setValue($legenda_nota);
    
            $note->setDetails($items)
                ->setLegends([$legend]);
    
            $see =   $this->controlConfiguracionGreenter($util);
            $res =   $see->send($note);


            $util->writeXml($note, $see->getFactory()->getLastXml(),$nota->tipoDoc.'-'.$nota->tipDocAfectado,null);
            if($nota->tipDocAfectado == '03'){
                $nota->ruta_xml      =   'storage/greenter/notas_credito_boletas/xml/'.$note->getName().'.xml';
            }
            if($nota->tipDocAfectado == '01'){
                $nota->ruta_xml      =   'storage/greenter/notas_credito_facturas/xml/'.$note->getName().'.xml';
            }
            $nota->nota_name        =   $note->getName();
          
           //======== ENVÍO CORRECTO Y ACEPTADO ==========
           if($res->isSuccess()){
               
                //====== GUARDANDO RESPONSE ======
                $cdr                                    =   $res->getCdrResponse();
                $nota->cdr_response_id                  =   $cdr->getId();
                $nota->cdr_response_code                =   $cdr->getCode();
                $nota->cdr_response_description         =   $cdr->getDescription();
                $nota->cdr_response_notes               =   implode(" | ", $cdr->getNotes());
                $nota->cdr_response_reference           =   $cdr->getReference();

                $util->writeCdr($note, $res->getCdrZip(),$nota->tipoDoc.'-'.$nota->tipDocAfectado,null);

                if($nota->tipDocAfectado == '03'){
                    $nota->ruta_cdr      =   'storage/greenter/notas_credito_boletas/cdr/'.$note->getName().'.zip';
                }
                if($nota->tipDocAfectado == '01'){
                    $nota->ruta_cdr      =   'storage/greenter/notas_credito_facturas/cdr/'.$note->getName().'.zip';
                }
                
                $nota->sunat                        =   "1";
                $nota->update(); 

                Session::flash('nota_credito_sunat_success',$cdr->getDescription());
                return back();
               //return response()->json(["success"   =>  true,"message"=>$cdr->getDescription()]);
           }else{
               $nota->response_error_message  =   $res->getError()->getMessage();
               $nota->response_error_code     =   $res->getError()->getCode();
               $nota->regularize              =   '1';
               $nota->update(); 

                //if($res->getError()->getCode() == 2223){
                //  dd($res);
                //  return response()->json(["success"   =>  true,"message"=>$cdr->getDescription()]);
                //}

               throw new Exception("ERROR AL ENVIAR FACTURA A SUNAT. "."CÓDIGO: ".$res->getError()->getCode()
               .",DESCRIPCIÓN: ".$res->getError()->getMessage());
           }
           
           
        } catch (\Throwable $th) {
            Session::flash('nota_credito_sunat_error',$th->getMessage());
            return back();
        }
       
      
    }

    public function controlConfiguracionGreenter($util){
        //==== OBTENIENDO CONFIGURACIÓN DE GREENTER ======
        $greenter_config    =   DB::select('select 
                                gc.ruta_certificado,
                                gc.id_api_guia_remision,
                                gc.modo,
                                gc.clave_api_guia_remision,
                                e.ruc,e.razon_social,
                                e.direccion_fiscal,
                                e.ubigeo,
                                e.direccion_llegada,
                                gc.sol_user,gc.sol_pass
                                from greenter_config as gc
                                inner join empresas as e on e.id=gc.empresa_id
                                inner join configuracion as c on c.propiedad = gc.modo
                                where gc.empresa_id=1 and c.slug="AG"');


        if(count($greenter_config) === 0){
            throw new Exception('NO SE ENCONTRÓ NINGUNA CONFIGURACIÓN PARA GREENTER');
        }

        if(!$greenter_config[0]->sol_user){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_USER');
        }
        if(!$greenter_config[0]->sol_pass){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_PASS');
        }
        if ($greenter_config[0]->modo !== "BETA" && $greenter_config[0]->modo !== "PRODUCCION") {
            throw new Exception('NO SE HA CONFIGURADO EL AMBIENTE BETA O PRODUCCIÓN PARA GREENTER');
        }

        $see    =   null;
        if($greenter_config[0]->modo === "BETA"){
            //===== MODO BETA ======
            $see = $util->getSee(SunatEndpoints::FE_BETA,$greenter_config[0]);
        }

        if($greenter_config[0]->modo === "PRODUCCION"){
            //===== MODO PRODUCCION ======
            $see = $util->getSee(SunatEndpoints::FE_PRODUCCION,$greenter_config[0]);
        }

        if(!$see){
            throw new Exception('ERROR EN LA CONFIGURACIÓN DE GREENTER, SEE ES NULO');
        }

        return $see;
    }


    public function sunat_prev($nota_id,$tipo_venta)
    {
        try
        {
            $nota = Nota::findOrFail($nota_id);
            //OBTENER CORRELATIVO DE LA NOTA CREDITO / DEBITO
            $res_numeracion = self::numeracion($nota,$tipo_venta);
            if($res_numeracion){
                if ($res_numeracion->get('existe') == true) {
                    return array('success' => true,'mensaje' => 'Nota validada.',
                    'correlativo' => $res_numeracion->get('correlativo'),'serie'=>$res_numeracion->get('serie'),
                    'model_numeracion' => $res_numeracion->get('numeracion'));
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

    public static function getCorrelativo($sede_id,$tipo_venta_id){

        $parametro          =   null;
        $tipDocAfectado     =   null;
        $correlativo        =   null;
        $serie              =   null;


        //===== 127:FACTURA | 128:BOLETA | 129:NOTA DE VENTA ======
        if($tipo_venta_id == 127){
            $tipDocAfectado =   '01';
            $parametro      =   "FF";   //====== NOTA CRÉDITO FACTURA =======
            $message        =   "NOTAS DE CRÉDITO DE FACTURAS ELECTRÓNICAS";
        }
        if($tipo_venta_id == 128){
            $tipDocAfectado =   '03';
            $parametro      =   "BB";   //======= NOTA CRÉDITO BOLETA =====
            $message        =   "NOTAS DE CRÉDITO DE BOLETAS ELECTRÓNICAS";
        }
        if($tipo_venta_id == 129){
            $tipDocAfectado =   '04';
            $parametro      =   "NN";   //===== NOTA DEVOLUCIÓN =======
            $message        =   "NOTAS DE DEVOLUCIÓN DE NOTAS DE VENTA";
        }

        //===== OBTENIENDO LA ÚLTIMA NOTA ELECTRÓNICA DE LA SEDE =======
        $ultima_nota =      DB::select('SELECT
                            n.serie, 
                            n.correlativo 
                            FROM nota_electronica AS n
                            WHERE n.sede_id = ? 
                            AND n.tipDocAfectado = ?
                            ORDER BY n.id DESC 
                            LIMIT 1',
                            [
                                $sede_id,
                                $tipDocAfectado
                            ]);

        $tipo_comprobante   =   DB::select('select 
                                td.* 
                                from tabladetalles as td
                                where
                                td.tabla_id = 21
                                AND td.parametro = ?',[$parametro])[0];

        $serializacion  =   DB::select('select 
                            enf.*
                            from empresa_numeracion_facturaciones as enf
                            where 
                            enf.empresa_id = ?
                            and enf.tipo_comprobante = ?
                            and enf.sede_id = ?',
                            [Empresa::find(1)->id,
                            $tipo_comprobante->id,
                            $sede_id])[0];

        //======== EN CASO NO EXISTAN NOTAS GENERADAS =====
        if(count($ultima_nota) === 0){

            $correlativo        =   $serializacion->numero_iniciar;
            $serie              =   $serializacion->serie;

        }else{

            //======= EN CASO YA EXISTAN NOTAS DE CREDITO DEL TYPE SALE ======
            $correlativo    =   $ultima_nota[0]->correlativo + 1;
            $serie          =   $ultima_nota[0]->serie;
    
        }


        return (object)[
            'tipo_comprobante'  =>  $tipo_comprobante,
            'serie'             =>  $serie,
            'correlativo'       =>  $correlativo
        ];
    }

}
