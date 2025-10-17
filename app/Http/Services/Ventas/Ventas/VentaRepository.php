<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Almacenes\Almacen;
use App\Almacenes\Color;
use App\Almacenes\Kardex;
use App\Almacenes\Modelo;
use App\Almacenes\Producto;
use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use App\Mantenimiento\MetodoEntrega\EmpresaEnvioSede;
use App\Mantenimiento\MetodoEntrega\MetodoEntrega;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
use App\Pos\DetalleMovimientoVentaCaja;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\EnvioVenta;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class VentaRepository
{
    public function insertarVenta(object $datos_validados, object $montos, object $datos_correlativo, $legenda): Documento
    {
        //====== GRABAR MAESTRO VENTA =====
        $documento                      =   new Documento();

        $documento->caja_id             =   $datos_validados->caja_movimiento->caja_id;
        $documento->caja_nombre         =   $datos_validados->caja_movimiento->caja_nombre;
        $documento->caja_movimiento_id  =   $datos_validados->caja_movimiento->movimiento_id;

        //========= FECHAS ========
        $documento->fecha_documento     = Carbon::now()->toDateString();
        $documento->fecha_atencion      = Carbon::now()->toDateString();

        if ($datos_validados->condicion->id != 1) {
            $nro_dias                       = $datos_validados->condicion->dias;
            $documento->fecha_vencimiento   = Carbon::now()->addDays($nro_dias)->toDateString();
        } else {
            $documento->fecha_vencimiento   = Carbon::now()->toDateString();
        }

        //======= PAGO SI SE PROPORCIONA EFECTIVO,MONTO Y FECHA PAGO =======
        if ($datos_validados->tipo_doc_venta_pedido != 'ATENCION') {

            if ($datos_validados->metodoPagoId == 1 && $datos_validados->montoPago && $datos_validados->fechaOperacionPago) {
                if (floatval($datos_validados->montoPago) != floatval($montos->monto_total_pagar)) {
                    throw new Exception("EL MONTO DE PAGO NO COINCIDE CON EL TOTAL DE LA VENTA");
                }

                $documento->pago_1_monto            =   $datos_validados->montoPago;
                $documento->pago_1_fecha_operacion  =   $datos_validados->fechaOperacionPago;
                $documento->importe                 =   $datos_validados->montoPago;
                $documento->pago_1_tipo_pago_nombre =   $datos_validados->tipo_pago_1->descripcion;
                $documento->pago_1_tipo_pago_id     =   $datos_validados->metodoPagoId;
                $documento->tipo_pago_id            =   $datos_validados->metodoPagoId;
                $documento->estado_pago             =   'PAGADA';
            }

            //======== SI SE PROPORCIONA OTRO MÉTODO,CUENTA,MONTO,NRO OP,FECHA ========
            if ($datos_validados->metodoPagoId != 1 && $datos_validados->cuentaPagoId && $datos_validados->montoPago && $datos_validados->nroOperacionPago && $datos_validados->fechaOperacionPago) {
                if (floatval($datos_validados->montoPago) != floatval($montos->monto_total_pagar)) {
                    throw new Exception("EL MONTO DE PAGO NO COINCIDE CON EL TOTAL DE LA VENTA");
                }
                $documento->pago_1_monto            =   $datos_validados->montoPago;
                $documento->pago_1_fecha_operacion  =   $datos_validados->fechaOperacionPago;
                $documento->importe                 =   $datos_validados->montoPago;
                $documento->pago_1_tipo_pago_nombre =   $datos_validados->tipo_pago_1->descripcion;
                $documento->pago_1_tipo_pago_id     =   $datos_validados->metodoPagoId;
                $documento->tipo_pago_id            =   $datos_validados->metodoPagoId;
                $documento->estado_pago             =   'PAGADA';
                if ($datos_validados->cuenta_pago_1) {
                    $documento->pago_1_banco_nombre         =   $datos_validados->cuenta_pago_1->banco_nombre;
                    $documento->pago_1_nro_cuenta           =   $datos_validados->cuenta_pago_1->nro_cuenta;
                    $documento->pago_1_cci                  =   $datos_validados->cuenta_pago_1->cci;
                    $documento->pago_1_celular              =   $datos_validados->cuenta_pago_1->celular;
                    $documento->pago_1_titular              =   $datos_validados->cuenta_pago_1->titular;
                    $documento->pago_1_moneda               =   $datos_validados->cuenta_pago_1->moneda;
                    $documento->pago_1_cuenta_id            =   $datos_validados->cuentaPagoId;
                    $documento->pago_1_nro_operacion        =   $datos_validados->nroOperacionPago;
                }
            }

        }

        //======== EMPRESA ========
        $documento->ruc_empresa                 =   $datos_validados->empresa->ruc;
        $documento->empresa                     =   $datos_validados->empresa->razon_social;
        $documento->direccion_fiscal_empresa    =   $datos_validados->empresa->direccion_fiscal;
        $documento->empresa_id                  =   $datos_validados->empresa->id;

        //========= CLIENTE =======
        $documento->tipo_documento_cliente  = $datos_validados->cliente->tipo_documento;
        $documento->documento_cliente       = $datos_validados->cliente->documento;
        $documento->direccion_cliente       = $datos_validados->cliente->direccion;
        $documento->cliente                 = $datos_validados->cliente->nombre;
        $documento->cliente_id              = $datos_validados->cliente->id;

        //======== TIPO VENTA ======
        $documento->tipo_venta_id           = $datos_validados->tipo_venta->id;   //boleta,factura,nota_venta
        $documento->tipo_venta_nombre       = $datos_validados->tipo_venta->descripcion;

        //========= CONDICIÓN PAGO ======
        $documento->condicion_id            =   $datos_validados->condicion->id;
        $documento->condicion_pago_nombre   =   $datos_validados->condicion->descripcion;

        $documento->observacion         =   mb_strtoupper($datos_validados->observacion, 'UTF-8');
        $documento->user_id             =   $datos_validados->usuario->id;
        $documento->registrador_nombre  =   $datos_validados->usuario->usuario;

        //========= MONTOS Y MONEDA ========
        $documento->sub_total               =   $montos->monto_subtotal;
        $documento->monto_embalaje          =   $montos->monto_embalaje;
        $documento->monto_envio             =   $montos->monto_envio;
        $documento->total                   =   $montos->monto_total;
        $documento->total_igv               =   $montos->monto_igv;
        $documento->total_pagar             =   $montos->monto_total_pagar;
        $documento->igv                     =   $datos_validados->empresa->igv;
        $documento->monto_descuento         =   $montos->monto_descuento;
        $documento->porcentaje_descuento    =   $montos->porcentaje_descuento;
        $documento->moneda                  =   1;

        //======== SUNAT ========
        $documento->mto_oper_gravadas_sunat =   $montos->mtoOperGravadasSunat;
        $documento->mto_igv_sunat           =   $montos->mtoIgvSunat;
        $documento->total_impuestos_sunat   =   $montos->totalImpuestosSunat;
        $documento->valor_venta_sunat       =   $montos->valorVentaSunat;
        $documento->sub_total_sunat         =   $montos->subTotalSunat;
        $documento->mto_imp_venta_sunat     =   $montos->mtoImpVentaSunat;

        //======= SERIE Y CORRELATIVO ======
        $documento->serie       =   $datos_correlativo->serie;
        $documento->correlativo =   $datos_correlativo->correlativo;

        $documento->legenda     =   $legenda;

        $documento->sede_id         =   $datos_validados->sede_id;

        $documento->almacen_id      =   $datos_validados->almacen->id;
        $documento->almacen_nombre  =   $datos_validados->almacen->descripcion;

        //======== DOCS VENTA DE TIPO ANTICIPO ========
        if ($datos_validados->facturar && $datos_validados->pedido_id) {
            $documento->pedido_id               =   $datos_validados->pedido_id;
            $documento->es_anticipo             =   true;
            $documento->tipo_doc_venta_pedido   =   "FACTURACIÓN";
            $documento->pedido_id               =   $datos_validados->pedido_id;
            $documento->saldo_anticipo          =   $montos->monto_total_pagar;
        }

        //======= TICKET PEDIDO CRÉDITO =======
        if ($datos_validados->ticket_credito && $datos_validados->pedido_id) {
            $documento->pedido_id               =   $datos_validados->pedido_id;
            $documento->tipo_doc_venta_pedido   =   "CREDITO";
            $documento->pedido_id               =   $datos_validados->pedido_id;
        }

        if ($datos_validados->modo === 'CONSUMO') {
            $documento->pedido_id               =   $datos_validados->pedido_id;
        }

        //========= DOCS VENTA PAGADAS CON ANTICIPO PARCIAL O TOTAL ========
        if ($datos_validados->facturado === 'SI' || $datos_validados->modo === 'CONSUMO') {
            $documento->estado_pago  =   'PAGADA';
        }

        if ($datos_validados->anticipo_consumido_id) {
            $documento->anticipo_consumido_id               =   $datos_validados->anticipo_consumido_id;
            $documento->anticipo_monto_consumido            =   $datos_validados->anticipo_monto_consumido;
            $documento->anticipo_monto_consumido_sin_igv    =   $datos_validados->anticipo_monto_consumido / (($datos_validados->porcentaje_igv + 100) / 100);
            $documento->anticipo_consumido_serie            =   $datos_validados->doc_anticipo_serie;
            $documento->anticipo_consumido_correlativo      =   $datos_validados->doc_anticipo_correlativo;
        }

        //======== EN CASO DE CONVERSIÓN DE DOCUMENTO =====
        if ($datos_validados->documento_convertido) {
            $documento_convertido           =   Documento::find($datos_validados->documento_convertido);
            $documento->convert_de_id       =   $datos_validados->documento_convertido;
            $documento->convert_de_serie    =   $documento_convertido->serie . '-' . $documento_convertido->correlativo;
            $documento->estado_pago         =   'PAGADA';
        }

        //======= EN CASO DE REGULARIZACIÓN =====
        if ($datos_validados->doc_regularizar_id) {
            $doc_regularizar                    =   Documento::find($datos_validados->doc_regularizar_id);
            $documento->regularizado_de_id      =   $doc_regularizar->id;
            $documento->regularizado_de_serie   =   $doc_regularizar->serie . '-' . $doc_regularizar->correlativo;
            $documento->estado_pago             =   'PAGADA';
        }

        $documento->modo    =   $datos_validados->modo ?? 'VENTA';

        if ($datos_validados->modo) {
            $documento->tipo_doc_venta_pedido   =   "ATENCION";
            $documento->pedido_id               =   $datos_validados->pedido_id ?? null;
        }

        if ($datos_validados->tipo_doc_venta_pedido) {
            $documento->tipo_doc_venta_pedido   =   $datos_validados->tipo_doc_venta_pedido;
        }

        $documento->telefono    =   $datos_validados->telefono;
        $documento->save();

        return $documento;
    }

    public function insertarDetalleVenta(object $datos_validados, Documento $documento)
    {
        foreach ($datos_validados->lstVenta as $item) {
            foreach ($item->tallas as $talla) {

                /*======== EN CASO SEA UNA FACTURACIÓN DE PEDIDO Y EL PRODUCTO NO EXISTA ========
                    ========= CREAMOS EL PRODUCTO COLOR TALLA CON STOCKS EN CERO ========*/
                if ($datos_validados->facturar || $datos_validados->ticket_credito === 'SI') {

                    $item_pedido   =    DB::select(
                        'SELECT
                                            pct.producto_id
                                            from producto_color_tallas as pct
                                            where
                                            pct.almacen_id = ?
                                            AND pct.producto_id = ?
                                            AND pct.color_id = ?
                                            AND pct.talla_id = ?',
                        [
                            $documento->almacen_id,
                            $item->producto_id,
                            $item->color_id,
                            $talla->talla_id
                        ]
                    );

                    if (count($item_pedido) === 0) {
                        $nuevo_producto                 =   new ProductoColorTalla();
                        $nuevo_producto->almacen_id     =   $documento->almacen_id;
                        $nuevo_producto->producto_id    =   $item->producto_id;
                        $nuevo_producto->color_id       =   $item->color_id;
                        $nuevo_producto->talla_id       =   $talla->talla_id;
                        $nuevo_producto->stock          =   0;
                        $nuevo_producto->stock_logico   =   0;
                        $nuevo_producto->save();
                    }
                }

                //====== COMPROBAR SI EXISTE EL PRODUCTO COLOR TALLA EN EL ALMACÉN =====
                $existe =   DB::select(
                    'SELECT
                                pct.*,
                                p.nombre AS producto_nombre,
                                p.codigo AS producto_codigo,
                                c.descripcion AS color_nombre,
                                t.descripcion AS talla_nombre,
                                m.descripcion AS modelo_nombre
                                FROM producto_color_tallas AS pct
                                INNER JOIN productos AS p ON p.id = pct.producto_id
                                INNER JOIN colores AS c ON c.id = pct.color_id
                                INNER JOIN tallas AS t ON t.id = pct.talla_id
                                INNER JOIN modelos AS m ON m.id = p.modelo_id
                                WHERE
                                pct.almacen_id = ?
                                AND pct.producto_id = ?
                                AND pct.color_id = ?
                                AND pct.talla_id = ?',
                    [
                        $datos_validados->almacen->id,
                        $item->producto_id,
                        $item->color_id,
                        $talla->talla_id
                    ]
                );

                if (count($existe) === 0) {
                    throw new Exception($item->producto_nombre . '-' . $item->color_nombre . '-' . $talla->talla_nombre . ', NO EXISTE EN EL ALMACÉN!!!');
                }

                //========= VALIDACIÓN STOCK ========
                if (
                    !$datos_validados->ticket_credito &&
                    (
                        floatval($talla->cantidad) > floatval($existe[0]->stock)
                    )
                ) {
                    throw new Exception($item->producto_nombre . '-' . $item->color_nombre . '-' . $talla->talla_nombre . ', STOCK INSUFICIENTE' . ' (' . $existe[0]->stock . ')');
                }

                //========== GRABAR DETALLE VENTA =====
                $importe                =   floatval($talla->cantidad) * floatval($item->precio_venta);
                $precio_unitario        =   $item->porcentaje_descuento == 0 ? $item->precio_venta : $item->precio_venta_nuevo;

                $detalle                            =   new Detalle();
                $detalle->documento_id              =   $documento->id;
                $detalle->almacen_id                =   $datos_validados->almacen->id;
                $detalle->producto_id               =   $item->producto_id;
                $detalle->color_id                  =   $item->color_id;
                $detalle->talla_id                  =   $talla->talla_id;
                $detalle->almacen_nombre            =   $datos_validados->almacen->descripcion;
                $detalle->codigo_producto           =   $existe[0]->producto_codigo;
                $detalle->nombre_producto           =   $existe[0]->producto_nombre;
                $detalle->nombre_color              =   $existe[0]->color_nombre;
                $detalle->nombre_talla              =   $existe[0]->talla_nombre;
                $detalle->nombre_modelo             =   $existe[0]->modelo_nombre;
                $detalle->cantidad                  =   floatval($talla->cantidad);
                $detalle->precio_unitario           =   floatval($item->precio_venta);
                $detalle->importe                   =   $importe;
                $detalle->precio_unitario_nuevo     =   floatval($precio_unitario);
                $detalle->porcentaje_descuento      =   floatval($item->porcentaje_descuento);
                $detalle->monto_descuento           =   floatval($importe) * floatval($item->porcentaje_descuento) / 100;
                $detalle->importe_nuevo             =   floatval($precio_unitario) * floatval($talla->cantidad);
                $detalle->cantidad_sin_cambio       =   (int) $talla->cantidad;
                $detalle->save();

                //====== RESTAR STOCK SI NO ES CONVERSIÓN NI REGULARIZACIÓN NI TICKET CRÉDITO =======
                if (
                    !$datos_validados->documento_convertido
                    && !$datos_validados->regularizar
                    && !$datos_validados->facturar
                    && $datos_validados->modo !== 'CONSUMO'
                    && !$datos_validados->ticket_credito
                ) {

                    //===== ACTUALIZANDO STOCK ===========
                    DB::update(
                        'UPDATE producto_color_tallas
                        SET stock = stock - ?
                        WHERE
                        almacen_id = ?
                        AND producto_id = ?
                        AND color_id = ?
                        AND talla_id = ?',
                        [
                            $talla->cantidad,
                            $datos_validados->almacen->id,
                            $item->producto_id,
                            $item->color_id,
                            $talla->talla_id
                        ]
                    );

                    $nuevo_stock    =    DB::table('producto_color_tallas')
                        ->where('almacen_id', $datos_validados->almacen->id)
                        ->where('producto_id', $item->producto_id)
                        ->where('color_id', $item->color_id)
                        ->where('talla_id', $talla->talla_id)
                        ->value('stock');

                    //======= KARDEX CON STOCK YA MODIFICADO =======
                    $kardex                     =   new Kardex();
                    $kardex->sede_id            =   $datos_validados->sede_id;
                    $kardex->almacen_id         =   $datos_validados->almacen->id;
                    $kardex->producto_id        =   $item->producto_id;
                    $kardex->color_id           =   $item->color_id;
                    $kardex->talla_id           =   $talla->talla_id;
                    $kardex->almacen_nombre     =   $datos_validados->almacen->descripcion;
                    $kardex->producto_nombre    =   $existe[0]->producto_nombre;
                    $kardex->color_nombre       =   $existe[0]->color_nombre;
                    $kardex->talla_nombre       =   $existe[0]->talla_nombre;
                    $kardex->cantidad           =   $talla->cantidad;
                    $kardex->precio             =   $precio_unitario;
                    $kardex->importe            =   $detalle->importe_nuevo;
                    $kardex->accion             =   'VENTA';
                    $kardex->stock              =   $nuevo_stock;
                    $kardex->numero_doc         =   $documento->serie . '-' . $documento->correlativo;
                    $kardex->documento_id       =   $documento->id;
                    $kardex->registrador_id     =   $documento->user_id;
                    $kardex->registrador_nombre =   $datos_validados->usuario->usuario;
                    $kardex->fecha              =   Carbon::today()->toDateString();
                    $kardex->descripcion        =   mb_strtoupper($datos_validados->observacion, 'UTF-8');
                    $kardex->save();
                }
            }
        }

        if ($documento->monto_embalaje != 0 && $documento->monto_embalaje) {
            $producto_embalaje                  =   Producto::where('tipo', 'FICTICIO')->where('nombre', 'EMBALAJE')->first();
            $color_ficticio                     =   Color::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $talla_ficticio                     =   Talla::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $modelo_ficticio                    =   Modelo::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();

            $detalle                            =   new Detalle();
            $detalle->documento_id              =   $documento->id;
            $detalle->almacen_id                =   $datos_validados->almacen->id;
            $detalle->producto_id               =   $producto_embalaje->id;
            $detalle->color_id                  =   $color_ficticio->id;
            $detalle->talla_id                  =   $talla_ficticio->id;
            $detalle->almacen_nombre            =   $datos_validados->almacen->descripcion;
            $detalle->codigo_producto           =   'EMBALAJE';
            $detalle->nombre_producto           =   $producto_embalaje->nombre;
            $detalle->nombre_color              =   $color_ficticio->descripcion;
            $detalle->nombre_talla              =   $talla_ficticio->descripcion;
            $detalle->nombre_modelo             =   $modelo_ficticio->descripcion;
            $detalle->cantidad                  =   1;
            $detalle->precio_unitario           =   $documento->monto_embalaje;
            $detalle->importe                   =   $documento->monto_embalaje;
            $detalle->precio_unitario_nuevo     =   $documento->monto_embalaje;
            $detalle->porcentaje_descuento      =   0;
            $detalle->monto_descuento           =   0;
            $detalle->importe_nuevo             =   $documento->monto_embalaje;
            $detalle->cantidad_sin_cambio       =   1;
            $detalle->tipo                      =   'SERVICIO';
            $detalle->save();
        }

        if ($documento->monto_envio != 0 && $documento->monto_envio) {
            $producto_embalaje                  =   Producto::where('tipo', 'FICTICIO')->where('nombre', 'ENVIO')->first();
            $color_ficticio                     =   Color::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $talla_ficticio                     =   Talla::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();
            $modelo_ficticio                    =   Modelo::where('tipo', 'FICTICIO')->where('descripcion', 'SERVICIO')->first();

            $detalle                            =   new Detalle();
            $detalle->documento_id              =   $documento->id;
            $detalle->almacen_id                =   $datos_validados->almacen->id;
            $detalle->producto_id               =   $producto_embalaje->id;
            $detalle->color_id                  =   $color_ficticio->id;
            $detalle->talla_id                  =   $talla_ficticio->id;
            $detalle->almacen_nombre            =   $datos_validados->almacen->descripcion;
            $detalle->codigo_producto           =   'ENVIO';
            $detalle->nombre_producto           =   $producto_embalaje->nombre;
            $detalle->nombre_color              =   $color_ficticio->descripcion;
            $detalle->nombre_talla              =   $talla_ficticio->descripcion;
            $detalle->nombre_modelo             =   $modelo_ficticio->descripcion;
            $detalle->cantidad                  =   1;
            $detalle->precio_unitario           =   $documento->monto_envio;
            $detalle->importe                   =   $documento->monto_envio;
            $detalle->precio_unitario_nuevo     =   $documento->monto_envio;
            $detalle->porcentaje_descuento      =   0;
            $detalle->monto_descuento           =   0;
            $detalle->importe_nuevo             =   $documento->monto_envio;
            $detalle->cantidad_sin_cambio       =   1;
            $detalle->tipo                      =   'SERVICIO';
            $detalle->save();
        }
    }

    public function insertarDespacho(Documento $venta, object $data_envio, object $datos_validados)
    {
        //========= HAY DATOS DE DESPACHO ======
        if (
            !empty((array)$data_envio)
            && !$datos_validados->facturar
            && !$datos_validados->documento_convertido
            && $datos_validados->tipo_doc_venta_pedido !== 'CONSUMO'
        ) {

            $departamento_id = str_pad($data_envio->departamento, 2, '0', STR_PAD_LEFT);
            $provincia_id    = str_pad($data_envio->provincia, 4, '0', STR_PAD_LEFT);
            $distrito_id     = str_pad($data_envio->distrito, 6, '0', STR_PAD_LEFT);

            $departamento   = Departamento::findOrFail($departamento_id);
            $provincia      = Provincia::findOrFail($provincia_id);
            $distrito       = Distrito::findOrFail($distrito_id);

            $empresa_envio          =   MetodoEntrega::findOrFail($data_envio->empresa_envio);
            $sede_envio             =   EmpresaEnvioSede::findOrFail($data_envio->sede_envio);
            $tipo_envio             =   TablaDetalle::findOrFail($data_envio->tipo_envio);
            $tipo_pago_envio        =   TablaDetalle::findOrFail($data_envio->tipo_pago_envio);
            $origen_venta           =   TablaDetalle::findOrFail($data_envio->origen_venta);
            $almacen                =   Almacen::findOrFail($venta->almacen_id);

            $envio                          =   new EnvioVenta();
            $envio->documento_id            =   $venta->id;
            $envio->departamento            =   $departamento->nombre;
            $envio->provincia               =   $provincia->nombre;
            $envio->distrito                =   $distrito->nombre;
            $envio->departamento_id         =   $departamento->id;
            $envio->provincia_id            =   $provincia->id;
            $envio->distrito_id             =   $distrito->id;
            $envio->empresa_envio_id        =   $empresa_envio->id;
            $envio->empresa_envio_nombre    =   $empresa_envio->empresa;
            $envio->sede_envio_id           =   $sede_envio->id;
            $envio->sede_envio_nombre       =   $sede_envio->direccion;
            $envio->tipo_envio_id           =   $tipo_envio->id;
            $envio->tipo_envio              =   $tipo_envio->descripcion;

            $envio->destinatario_tipo_doc   =   $data_envio->destinatario->tipo_documento;
            $envio->destinatario_nro_doc    =   $data_envio->destinatario->nro_documento;
            $envio->destinatario_nombre     =   $data_envio->destinatario->nombres;

            $envio->cliente_id              =   $venta->cliente_id;
            $envio->cliente_nombre          =   $venta->cliente;
            $envio->cliente_celular         =   $venta->clienteEntidad->telefono_movil;

            $envio->tipo_pago_envio         =   $tipo_pago_envio->descripcion;
            $envio->tipo_pago_envio_id      =   $tipo_pago_envio->id;

            $envio->monto_envio             =   $venta->monto_envio;
            $envio->entrega_domicilio       =   $data_envio->entrega_domicilio ? 'SI' : 'NO';
            $envio->direccion_entrega       =   $data_envio->direccion_entrega;
            $envio->documento_nro           =   $venta->serie . '-' . $venta->correlativo;
            $envio->fecha_envio_propuesta   =   $data_envio->fecha_envio_propuesta;

            $envio->origen_venta            =   $origen_venta->descripcion;
            $envio->origen_venta_id         =   $origen_venta->id;

            $envio->obs_rotulo              =   mb_strtoupper($data_envio->obs_rotulo, 'UTF-8');
            $envio->obs_despacho            =   mb_strtoupper($data_envio->obs_despacho, 'UTF-8');

            $envio->usuario_nombre          =   Auth::user()->usuario;
            $envio->user_vendedor_id        =   $venta->user_id;
            $envio->user_vendedor_nombre    =   $venta->registrador_nombre;
            $envio->user_despachador_id     =   null;
            $envio->user_despachador_nombre =   null;

            $envio->almacen_id            =   $venta->almacen_id;
            $envio->almacen_nombre        =   $venta->almacen_nombre;
            $envio->sede_id               =   $venta->sede_id;
            $envio->sede_despachadora_id  =   $almacen->sede_id;
            $envio->modo                  =   'VENTA';
            $envio->save();

            $venta->despacho_id     =   $envio->id;
            $venta->estado_despacho =   'PENDIENTE';
            $venta->update();
        }

        //======= SIN DESPACHO ======
        /*if (
            empty((array)$data_envio)
            && !$datos_validados->facturar
            && !$datos_validados->documento_convertido
            && $datos_validados->tipo_doc_venta_pedido !== 'CONSUMO'
        ) {

            //======== OBTENER EMPRESA ENVÍO =======
            $empresa_envio                      =   DB::select('SELECT
                                                    ee.id,
                                                    ee.empresa,
                                                    ee.tipo_envio
                                                    FROM empresas_envio AS ee
                                                    WHERE predeterminado = 1')[0];

            $sede_envio                         =   DB::select(
                'SELECT
                                                    ees.id,
                                                    ees.direccion
                                                    FROM empresa_envio_sedes AS ees
                                                    WHERE ees.empresa_envio_id=?',
                [$empresa_envio->id]
            )[0];

            $envio_venta                        =   new EnvioVenta();
            $envio_venta->documento_id          =   $documento->id;
            $envio_venta->departamento          =   $sede_envio->departamento;
            $envio_venta->provincia             =   $sede_envio->provincia;
            $envio_venta->distrito              =   $sede_envio->distrito;
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
            $envio_venta->documento_nro         =   $documento->serie . '-' . $documento->correlativo;
            $envio_venta->fecha_envio_propuesta =   null;
            $envio_venta->origen_venta          =   "WHATSAPP";
            $envio_venta->obs_despacho          =   null;
            $envio_venta->obs_rotulo            =   null;
            $envio_venta->estado                =   'PENDIENTE';
            $envio_venta->cliente_celular       =   $documento->clienteEntidad->telefono_movil;
            $envio_venta->user_vendedor_id      =   $documento->user_id;
            $envio_venta->user_vendedor_nombre  =   $documento->user->usuario;
            $envio_venta->user_despachador_id   =   $documento->user_id;
            $envio_venta->user_despachador_nombre   =   $documento->user->usuario;
            $envio_venta->almacen_id            =   $documento->almacen_id;
            $envio_venta->almacen_nombre        =   $documento->almacen_nombre;
            $envio_venta->sede_id               =   $documento->sede_id;
            $envio_venta->sede_despachadora_id  =   $datos_validados->almacen->sede_id;
            $envio_venta->modo                  =   $datos_validados->modo ?? 'VENTA';
            $envio_venta->save();
        }*/
    }

    public function asociarVentaCaja(Documento $venta, object $datos_validados)
    {
        $movimiento_venta                   =   new DetalleMovimientoVentaCaja();
        $movimiento_venta->cdocumento_id    =   $venta->id;
        $movimiento_venta->mcaja_id         =   $datos_validados->caja_movimiento->movimiento_id;
        if (
            $datos_validados->facturado === 'SI'
            || $datos_validados->tipo_doc_venta_pedido == 'CONSUMO'
        ) {
            $movimiento_venta->cobrar       =   'NO';
        }
        $movimiento_venta->save();
    }

    public function asociarDocConvertido(object $datos_validados, Documento $documento)
    {
        if ($datos_validados->documento_convertido) {
            $documento_convertido                       =   Documento::find($datos_validados->documento_convertido);
            $documento_convertido->convert_en_id        =   $documento->id;
            $documento_convertido->convert_en_serie     =   $documento->serie . '-' . $documento->correlativo;
            $documento_convertido->update();
        }
    }

    public function actualizarVenta(object $datos_validados, Documento $documento): Documento
    {
        //====== GRABAR MAESTRO VENTA =====
        $documento                      = $datos_validados->documento;

        //========= FECHAS ========
        //$documento->fecha_documento     = $documento->fecha_documento;
        //$documento->fecha_atencion      = $documento->fecha_documento;

        if ($datos_validados->condicion->id != 1) {
            $nro_dias                       = $datos_validados->condicion->dias;
            $documento->fecha_vencimiento   = Carbon::parse($documento->fecha_documento)->addDays($nro_dias)->toDateString();
        } else {
            $documento->fecha_vencimiento   = $documento->fecha_documento;
        }


        //======= PAGO =======
        //======== SI SE PROPORCIONA EFECTIVO,MONTO Y FECHA PAGO ========
        if ($datos_validados->metodoPagoId == 1 && $datos_validados->montoPago && $datos_validados->fechaOperacionPago) {
            if (floatval($datos_validados->montoPago) != floatval($datos_validados->montos->monto_total_pagar)) {
                throw new Exception("EL MONTO DE PAGO NO COINCIDE CON EL TOTAL DE LA VENTA");
            }

            $documento->pago_1_monto            =   $datos_validados->montoPago;
            $documento->pago_1_fecha_operacion  =   $datos_validados->fechaOperacionPago;
            $documento->importe                 =   $datos_validados->montoPago;

            $documento->pago_1_tipo_pago_nombre =   $datos_validados->tipo_pago_1->descripcion;
            $documento->pago_1_tipo_pago_id     =   $datos_validados->metodoPagoId;
            $documento->tipo_pago_id            =   $datos_validados->metodoPagoId;

            $documento->estado_pago             =   'PAGADA';


            if (isset($datos_validados->imgPago) && $datos_validados->imgPago instanceof UploadedFile) {
                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'))) {
                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'));
                }
                $extension              =   $datos_validados->imgPago->getClientOriginalExtension();
                $nombreImagenPago       =   $documento->serie . '-' . $documento->correlativo . '.' . $extension;
                $documento->ruta_pago   =   $datos_validados->imgPago->storeAs('public/pagos', $nombreImagenPago);
            }
        }

        //======== SI SE PROPORCIONA OTRO MÉTODO,CUENTA,MONTO,NRO OP,FECHA ========
        if ($datos_validados->metodoPagoId != 1 && $datos_validados->cuentaPagoId && $datos_validados->montoPago && $datos_validados->nroOperacionPago && $datos_validados->fechaOperacionPago) {
            if (floatval($datos_validados->montoPago) != floatval($datos_validados->montos->monto_total_pagar)) {
                throw new Exception("EL MONTO DE PAGO NO COINCIDE CON EL TOTAL DE LA VENTA");
            }
            $documento->pago_1_monto            =   $datos_validados->montoPago;
            $documento->pago_1_fecha_operacion  =   $datos_validados->fechaOperacionPago;
            $documento->importe                 =   $datos_validados->montoPago;
            $documento->pago_1_tipo_pago_nombre =   $datos_validados->tipo_pago_1->descripcion;
            $documento->pago_1_tipo_pago_id     =   $datos_validados->metodoPagoId;
            $documento->tipo_pago_id            =   $datos_validados->metodoPagoId;
            $documento->estado_pago             =   'PAGADA';
            if ($datos_validados->cuenta_pago_1) {
                $documento->pago_1_banco_nombre         =   $datos_validados->cuenta_pago_1->banco_nombre;
                $documento->pago_1_nro_cuenta           =   $datos_validados->cuenta_pago_1->nro_cuenta;
                $documento->pago_1_cci                  =   $datos_validados->cuenta_pago_1->cci;
                $documento->pago_1_celular              =   $datos_validados->cuenta_pago_1->celular;
                $documento->pago_1_titular              =   $datos_validados->cuenta_pago_1->titular;
                $documento->pago_1_moneda               =   $datos_validados->cuenta_pago_1->moneda;
                $documento->pago_1_cuenta_id            =   $datos_validados->cuentaPagoId;
                $documento->pago_1_nro_operacion        =   $datos_validados->nroOperacionPago;
            }
        }

        //========= CLIENTE =======
        $documento->tipo_documento_cliente  = $datos_validados->cliente->tipo_documento;
        $documento->documento_cliente       = $datos_validados->cliente->documento;
        $documento->direccion_cliente       = $datos_validados->cliente->direccion;
        $documento->cliente                 = $datos_validados->cliente->nombre;
        $documento->cliente_id              = $datos_validados->cliente->id;

        $documento->telefono                = $datos_validados->telefono;

        //======== TIPO VENTA ======
        $documento->tipo_venta_id           = $datos_validados->tipo_venta->id;   //boleta,factura,nota_venta
        $documento->tipo_venta_nombre       = $datos_validados->tipo_venta->descripcion;

        //========= CONDICIÓN PAGO ======
        $documento->condicion_id            = $datos_validados->condicion->id;

        $documento->observacion             = $datos_validados->observacion;

        //========= MONTOS Y MONEDA ========
        $documento->sub_total               =   $datos_validados->montos->monto_subtotal;
        $documento->monto_embalaje          =   $datos_validados->montos->monto_embalaje;
        $documento->monto_envio             =   $datos_validados->montos->monto_envio;
        $documento->total                   =   $datos_validados->montos->monto_total;
        $documento->total_igv               =   $datos_validados->montos->monto_igv;
        $documento->total_pagar             =   $datos_validados->montos->monto_total_pagar;
        //$documento->igv                   =   $datos_validados->empresa->igv;
        $documento->monto_descuento         =   $datos_validados->montos->monto_descuento;
        $documento->porcentaje_descuento    =   $datos_validados->montos->porcentaje_descuento;
        //$documento->moneda                =   1;

        //======== SUNAT ========
        $documento->mto_oper_gravadas_sunat =   $datos_validados->montos->mtoOperGravadasSunat;
        $documento->mto_igv_sunat           =   $datos_validados->montos->mtoIgvSunat;
        $documento->total_impuestos_sunat   =   $datos_validados->montos->totalImpuestosSunat;
        $documento->valor_venta_sunat       =   $datos_validados->montos->valorVentaSunat;
        $documento->sub_total_sunat         =   $datos_validados->montos->subTotalSunat;
        $documento->mto_imp_venta_sunat     =   $datos_validados->montos->mtoImpVentaSunat;

        //======= LEGENDA ======
        $documento->legenda     =   $datos_validados->legenda;

        //$documento->sede_id         =   $datos_validados->sede_id;
        //======= ALMACÉN ======
        $documento->almacen_id      =   $datos_validados->almacen->id;
        $documento->almacen_nombre  =   $datos_validados->almacen->descripcion;

        $documento->telefono        =   $datos_validados->telefono;
        $documento->update();

        return $documento;
    }

    public function eliminarDetalle(int $venta_id)
    {
        DB::delete(
            "DELETE FROM cotizacion_documento_detalles WHERE documento_id = ?",
            [$venta_id]
        );
    }

    public function insertarVentaAnticipo(array $dto): Documento
    {
        return Documento::create($dto);
    }

    public function insertarDetalleVentaAnticipo(array $dto)
    {
        Detalle::insert($dto);
    }
}
