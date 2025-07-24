<?php

namespace App\Http\Services\Pedidos\Pedidos;

use App\Http\Controllers\Ventas\DocumentoController;
use App\Http\Requests\Ventas\DocVenta\DocVentaStoreRequest;
use App\User;
use App\Ventas\Cliente;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\Pedido;
use App\Ventas\PedidoDetalle;
use Carbon\Carbon;
use Dom\Document;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoService
{

    public function __construct() {}

    public function facturar(array $datos): object
    {
        //====== RECIBIENDO PEDIDO ID =====
        $pedido_id      =   $datos['pedido_id'];
        $pedido         =   Pedido::findOrFail($pedido_id);

        if (!$pedido) {
            throw new Exception('NO SE ENCONTRÓ EL PEDIDO EN LA BASE DE DATOS');
        }
        if ($pedido->estado !== 'PENDIENTE') {
            throw new Exception("NO PUEDE FACTURARSE EL PEDIDO, SU ESTADO ES: " . $pedido->estado);
        }

        //===== OBTENIENDO EL TIPO DOC DEL CLIENTE =======
        $cliente    =   DB::select('SELECT
                        c.tipo_documento
                        FROM clientes AS c
                        WHERE c.id = ?', [$pedido->cliente_id]);

        if (count($cliente) === 0 || count($cliente) > 1) {
            throw new Exception('NO SE ENCONTRÓ EL CLIENTE EN LA BASE DE DATOS');
        }

        $cliente_tipo_documento =   $cliente[0]->tipo_documento;
        $tipo_venta             =   $datos['comprobante'];

        if (($cliente_tipo_documento !== "RUC" && $cliente_tipo_documento !== "DNI") && ($tipo_venta == 127 || $tipo_venta == 128)) {
            throw new Exception("SE REQUIERE QUE EL CLIENTE TENGA RUC O DNI PARA FACTURAR CON FACTURA O BOLETA ELECTRÓNICA");
        }
        if ($cliente_tipo_documento === "RUC" && $tipo_venta == 128) {
            throw new Exception("EL CLIENTE TIENE RUC, NO PUEDE FACTURARSE CON BOLETA ELECTRÓNICA");
        }
        if ($cliente_tipo_documento === "DNI" && $tipo_venta == 127) {
            throw new Exception("EL CLIENTE TIENE RUC, NO PUEDE FACTURARSE CON FACTURA ELECTRÓNICA");
        }

        //======= OBTENIENDO DETALLE DEL PEDIDO ===========
        $detalle_pedido     = PedidoDetalle::where('pedido_id', $pedido_id)->get();
        $detalle_formateado = $this->formatearArrayDetalleObjetos($detalle_pedido);
        $productos          = json_encode($detalle_formateado);

        //======= AGREGANDO DATOS AL REQUEST =====
        $additionalData = [
            'empresa'                   =>  $pedido->empresa_id,
            'tipo_venta'                =>  $tipo_venta,
            'condicion_id'              =>  $pedido->condicion_id,
            'fecha_vencimiento_campo'   =>  Carbon::now(),
            'cliente_id'                =>  $pedido->cliente_id,
            'igv'                       =>  "18",
            "igv_check"                 =>  "on",
            "efectivo"                  =>  "0",
            "importe"                   =>  "0",
            "empresa_id"                =>  $pedido->empresa_id,
            "monto_sub_total"           =>  $pedido->sub_total,
            "monto_embalaje"            =>  $pedido->monto_embalaje,
            "monto_envio"               =>  $pedido->monto_envio,
            "monto_total_igv"           =>  $pedido->total_igv,
            "monto_descuento"           =>  $pedido->monto_descuento,
            "monto_total"               =>  $pedido->total,
            "monto_total_pagar"         =>  $pedido->total_pagar,
            "data_envio"                =>  null,
            "facturar"                  =>  'SI',
            "productos_tabla"           =>  $productos,
            "sede_id"                   =>  $pedido->sede_id,
            "almacenSeleccionado"       =>  $pedido->almacen_id,
            "pedido_id"                 =>  $pedido->id,
        ];

        $request_base = Request::create(
            route('pedidos.pedido.facturar-store'),
            'POST',
            $additionalData
        );

        //======== GENERANDO DOC VENTA ======
        $docVentaRequest        =   DocVentaStoreRequest::createFrom($request_base);

        $documentoController    =   new DocumentoController();
        $res                    =   $documentoController->store($docVentaRequest);
        $jsonResponse           =   $res->getData();

        //====== MANEJO DE RESPUESTA =========
        $success_store_doc      =   $jsonResponse->success;

        //======= ERROR AL CREAR DOC FACTURACIÓN =======
        if (!$success_store_doc) {
            throw new Exception($jsonResponse->message);
        }

        $doc_venta  =   DB::select(
            'SELECT
                            cd.id,
                            cd.serie,
                            cd.correlativo,
                            cd.total_pagar
                            FROM cotizacion_documento AS cd
                            WHERE cd.id = ?',
            [$jsonResponse->documento_id]
        )[0];

        //======== ACTUALIZANDO PEDIDO =======
        $pedido->facturado                                  =   'SI';
        $pedido->documento_venta_facturacion_id             =   $jsonResponse->documento_id;
        $pedido->documento_venta_facturacion_serie          =   $doc_venta->serie;
        $pedido->documento_venta_facturacion_correlativo    =   $doc_venta->correlativo;
        $pedido->monto_facturado                            =   $doc_venta->total_pagar;
        $pedido->saldo_facturado                            =   $doc_venta->total_pagar;
        $pedido->save();

        return $doc_venta;
    }

    public static function formatearArrayDetalleObjetos($detalles)
    {
        $detalleFormateado = [];
        $productosProcesados = [];
        foreach ($detalles as $detalle) {
            $cod   =   $detalle->producto_id . '-' . $detalle->color_id;
            if (!in_array($cod, $productosProcesados)) {
                $producto = [];
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
                $producto['monto_descuento']        =   $detalle->monto_descuento;
                $producto['precio_venta']           =   $detalle->precio_unitario;
                $producto['precio_venta_nuevo']     =   $detalle->precio_unitario_nuevo;

                $tallas             =   [];
                $subtotal           =   0.0;
                $subtotal_with_desc =   0.0;
                $cantidadTotal = 0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla = [];
                    $talla['talla_id']              =   $producto_color_talla->talla_id;


                    $talla['cantidad']              =   (int)$producto_color_talla->cantidad;
                    $subtotal                       +=  $talla['cantidad'] * $producto['precio_unitario_nuevo'];
                    $cantidadTotal                  +=  $talla['cantidad'];


                    $talla['talla_nombre']          =   $producto_color_talla->talla_nombre;

                    array_push($tallas, (object)$talla);
                }

                $producto['tallas']                 =   $tallas;
                $producto['subtotal']               =   $subtotal;
                $producto['cantidad_total']         =   $cantidadTotal;
                array_push($detalleFormateado, (object)$producto);
                $productosProcesados[] = $detalle->producto_id . '-' . $detalle->color_id;
            }
        }
        return $detalleFormateado;
    }

    public function generarDocumentoVenta(array $datos): int
    {
        $request = new Request($datos);

        $pedido_id              =   $datos['pedido_id'];
        $pedido                 =   Pedido::findOrFail($pedido_id);

        if ($pedido->facturado === 'SI') {

            $doc_anticipo           =   Documento::findOrFail($pedido->documento_venta_facturacion_id);
            $monto_cubierto         =   (float)0;
            $saldo_anticipo         =   (float)$doc_anticipo->saldo_anticipo;
            $monto_pedido           =   (float)$datos['monto_total_pagar'];
            $generar_recibo_caja    =   'NO';

            if ($saldo_anticipo >= $monto_pedido) {
                $monto_cubierto    =   $monto_pedido;
            } else {
                $monto_cubierto         =   $saldo_anticipo;
                $generar_recibo_caja    =   'SI';
            }

            $additionalData = [
                'facturado'             =>  'SI',
                'generar_recibo_caja'   =>  $generar_recibo_caja,
                'excedente'             =>  abs($saldo_anticipo - $monto_pedido),
                'user_id'               =>  $pedido->user_id,
                'cliente_id'            =>  $pedido->cliente_id,
                'pedido_nro'            =>  $pedido->pedido_nro,
            ];

            if ($monto_cubierto > 0) {
                $additionalData =   [
                    'anticipo_consumido_id'     =>  $pedido->documento_venta_facturacion_id,
                    'anticipo_monto_consumido'  =>  $monto_cubierto
                ];
            }

            $request->merge($additionalData);
        }

        $request->merge([
            'sede_id'               => $pedido->sede_id,
            'almacenSeleccionado'   => $pedido->almacen_id,
        ]);

        //======= ACTUALIZANDO CANTIDAD ATENDIDA EN PEDIDO DETALLES ======
        $productosJSON  = $datos['productos_tabla'];
        $productos      = json_decode($productosJSON);

        foreach ($productos as $producto) {
            foreach ($producto->tallas as $talla) {

                DB::table('pedidos_detalles')
                    ->where('pedido_id', $pedido_id)
                    ->where('almacen_id', $pedido->almacen_id)
                    ->where('producto_id', $producto->producto_id)
                    ->where('color_id', $producto->color_id)
                    ->where('talla_id', $talla->talla_id)
                    ->update([
                        'cantidad_pendiente'    => DB::raw('cantidad_pendiente  - ' . $talla->cantidad),
                        'cantidad_atendida'     => DB::raw('cantidad_atendida   + ' . $talla->cantidad)
                    ]);
            }
        }

        //======= CAMBIANDO ESTADO DEL PEDIDO =====
        //===== CANTIDAD DE ITEMS QUE TIENE EL PEDIDO ======
        $cant_items_pendientes_pedido       =   PedidoDetalle::where('pedido_id', $pedido_id)
            ->where('cantidad_pendiente', '>', 0)
            ->count('*');
        $cant_items_atendidos_pedido        =   PedidoDetalle::where('pedido_id', $pedido_id)
            ->where('cantidad_atendida', '>', 0)
            ->count('*');

        $pedido_actualizar                  =   Pedido::findOrFail($pedido_id);

        if ($cant_items_pendientes_pedido === 0) {
            $pedido_actualizar->estado      =   "FINALIZADO";
        }

        if ($cant_items_pendientes_pedido > 0 && $cant_items_atendidos_pedido > 0) {
            $pedido_actualizar->estado      =   "ATENDIENDO";
        }

        if ($cant_items_atendidos_pedido === 0) {
            $pedido_actualizar->estado      =   "PENDIENTE";
        }

        //======== VERIFICANDO SI EL PEDIDO ESTÁ FACTURADO ======
        if ($pedido_actualizar->facturado === "SI") {
            //===== SI EL SALDO FACTURADO ES MAYOR O IGUAL AL MONTO DE LA ATENCIÓN =====
            if ($pedido_actualizar->saldo_facturado >= $datos['monto_total_pagar']) {
                //====== NO GENERAR RECIBOS DE CAJA =======
                //====== DISMINUIR SALDO FACTURADO ========
                $pedido_actualizar->saldo_facturado -=  $datos['monto_total_pagar'];
            } else {
                //====== DISMINUIR SALDO FACTURADO ========
                $pedido_actualizar->saldo_facturado =  0;
            }
        }

        $pedido_actualizar->save();

        //========= DEFINIR TIPO VENTA PARA EL DOC CONSUMO =========
        $tipo_venta = null;
        if ($pedido_actualizar->estado === 'FINALIZADO' && $pedido_actualizar->facturado === "SI") {
            $tipo_venta  = Documento::findOrFail($pedido->documento_venta_facturacion_id)->tipo_venta_id;
        }

        //========== GENERAR DOC VENTA ===========
        $docVentaRequest        =   DocVentaStoreRequest::createFrom($request);
        $documentoController    =   new DocumentoController();
        $res                    =   $documentoController->store($docVentaRequest);
        $jsonResponse           =   $res->getData();

        if (!$jsonResponse->success) {
            throw new Exception($jsonResponse->message . ' en la línea ' . $jsonResponse->line . ' del archivo ' . $jsonResponse->file);
        }

        //========= GENERAR DOCUMENTO VENTA DE CONSUMO EN CASO EL PEDIDO SE COMPLETÓ DE ATENDER =======
        if ($pedido_actualizar->estado === "FINALIZADO" && $pedido_actualizar->facturado === "SI") {

            $doc_anticipo           =   Documento::findOrFail($pedido->documento_venta_facturacion_id);

            if ($doc_anticipo->es_anticipo == '1') {

                $docs_antenciones   =   Documento::where('pedido_id', $pedido_id)
                    ->where('tipo_doc_venta_pedido', 'ATENCION')
                    ->get();

                $monto_sub_total    =   0;
                $monto_total_igv    =   0;
                $monto_total        =   0;
                $monto_embalaje     =   0;
                $monto_envio        =   0;
                $monto_total_pagar  =   0;
                $monto_descuento    =   0;

                $productos_tabla    =   [];

                foreach ($docs_antenciones as $doc_atencion) {
                    $monto_sub_total    +=  $doc_atencion->sub_total;
                    $monto_total_igv    +=  $doc_atencion->total_igv;
                    $monto_total        +=  $doc_atencion->total;
                    $monto_embalaje     +=  $doc_atencion->monto_embalaje;
                    $monto_envio        +=  $doc_atencion->monto_envio;
                    $monto_total_pagar  +=  $doc_atencion->total_pagar;
                    $monto_descuento    +=  $doc_atencion->monto_descuento;

                    $detalles   =   Detalle::where('documento_id', $doc_atencion->id)->get();

                    foreach ($detalles as $d_item) {
                        $item   =   (object)[
                            'producto_id'           =>  $d_item->producto_id,
                            'color_id'              =>  $d_item->color_id,
                            'talla_id'              =>  $d_item->talla_id,
                            'producto_nombre'       =>  $d_item->nombre_producto,
                            'color_nombre'          =>  $d_item->nombre_color,
                            'talla_nombre'          =>  $d_item->nombre_talla,
                            'cantidad'              =>  $d_item->cantidad,
                            'precio_venta'          =>  $d_item->precio_unitario,
                            'monto_descuento'       =>  $d_item->monto_descuento,
                            'porcentaje_descuento'  =>  $d_item->porcentaje_descuento,
                            'precio_venta_nuevo'    =>  $d_item->precio_unitario_nuevo,
                            'subtotal_nuevo'        =>  $d_item->importe_nuevo,
                            'subtotal'              =>  $d_item->importe,
                        ];
                        $productos_tabla[] = $item;
                    }
                }

                $productos_tabla = $this->agruparProductosConTallas($productos_tabla);

                $datos_consumo  =   [
                    'monto_sub_total'           =>  $monto_sub_total,
                    'monto_embalaje'            =>  $monto_embalaje,
                    'monto_envio'               =>  $monto_envio,
                    'monto_total_igv'           =>  $monto_total_igv,
                    'monto_descuento'           =>  $monto_descuento,
                    'monto_total'               =>  $monto_total,
                    'monto_total_pagar'         =>  $monto_total_pagar,
                    'productos_tabla'           =>  json_encode($productos_tabla),
                    'sede_id'                   =>  $pedido->sede_id,
                    'almacenSeleccionado'       =>  $pedido->almacen_id,
                    'tipo_doc_venta_pedido'     =>  'CONSUMO',
                    'modo'                      =>  'CONSUMO',
                    'tipo_venta'                =>  $tipo_venta,
                    'cliente_id'                =>  $pedido->cliente_id,
                    'condicion_id'              =>  "1-CONTADO",
                    'pedido_id'                 =>  $pedido->id,
                ];

                $request_consumo        =   new Request($datos_consumo);
                $request_venta_consumo  =   DocVentaStoreRequest::createFrom($request_consumo);

                $documentoController    =   new DocumentoController();
                $res_consumo            =   $documentoController->store($request_venta_consumo);
                $res_json_consumo       =   $res_consumo->getData();
            }
        }


        return $jsonResponse->documento_id;
    }


    function agruparProductosConTallas(array $items)
    {
        $resultado = [];
        $indice = 0;

        // Usamos una clave temporal para agrupar internamente por producto_id y color_id
        $agrupados = [];

        foreach ($items as $item) {
            $clave = $item->producto_id . '_' . $item->color_id;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'producto_id'           => $item->producto_id,
                    'color_id'              => $item->color_id,
                    'producto_nombre'       => $item->producto_nombre,
                    'color_nombre'          => $item->color_nombre,
                    'precio_venta'          => $item->precio_venta,
                    'monto_descuento'       => $item->monto_descuento,
                    'porcentaje_descuento'  => $item->porcentaje_descuento,
                    'precio_venta_nuevo'    => $item->precio_venta_nuevo,
                    'subtotal_nuevo'        => 0,
                    'tallas'                => [],
                    'subtotal'              => 0,
                ];
            }

            $agrupados[$clave]['tallas'][] = [
                'talla_id'      => $item->talla_id,
                'talla_nombre'  => $item->talla_nombre,
                'cantidad'      => $item->cantidad,
            ];

            $agrupados[$clave]['subtotal_nuevo'] += (float) $item->subtotal_nuevo;
            $agrupados[$clave]['subtotal']       += (float) $item->subtotal;
        }

        // Reindexamos numéricamente
        foreach ($agrupados as $itemAgrupado) {
            $resultado[$indice++] = $itemAgrupado;
        }

        return $resultado;
    }
}
