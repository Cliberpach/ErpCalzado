<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Almacenes\Almacen;
use App\Http\Controllers\UtilidadesController;
use App\Http\Services\Almacen\ProductoColorTalla\ProductoColorTallaService;
use App\Http\Services\Almacen\Productos\ProductoService;
use App\Http\Services\Kardex\Cuenta\KardexCuentaService;
use App\Http\Services\Produccion\Orden\OrdenProduccionService;
use App\Http\Services\Ventas\Despacho\DespachoService;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\MetodoEntrega\EmpresaEnvioSede;
use App\Mantenimiento\MetodoEntrega\MetodoEntrega;
use App\Mantenimiento\Sedes\Sede;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;
use App\Mantenimiento\TipoPago\TipoPago;
use App\Models\Ventas\ReservaWeb\ReservaWeb;
use App\Models\Ventas\TipoCliente\TipoCliente;
use App\Ventas\Cliente;
use App\Ventas\CuentaCliente;
use App\Ventas\DetalleCuentaCliente;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\EnvioVenta;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade as PDF;

class VentaService
{
    private VentaValidacion $s_validacion;
    private CorrelativoService $s_correlativo;
    private CalculosService $s_calculos;
    private VentaRepository $s_repository;
    private DespachoService $s_despacho;
    private ProductoColorTallaService $s_pct;
    private OrdenProduccionService $s_orden_produccion;
    private KardexCuentaService $s_kardex_cuenta;
    private VentaDto $s_dto;

    public function __construct()
    {
        $this->s_validacion       = new VentaValidacion();
        $this->s_correlativo      = new CorrelativoService();
        $this->s_calculos         = new CalculosService();
        $this->s_repository       = new VentaRepository();
        $this->s_despacho         = new DespachoService();
        $this->s_pct              = new ProductoColorTallaService();
        $this->s_orden_produccion = new OrdenProduccionService();
        $this->s_kardex_cuenta    = new KardexCuentaService();
        $this->s_dto              = new VentaDto();
    }

    public function store(array $datos): Documento
    {
        //========= VALIDACIONES COMPLEJAS ======
        $datos_validados    =   $this->s_validacion->validacionStore($datos);

        $this->s_validacion->comprobanteActivo($datos_validados->sede_id, $datos_validados->tipo_venta);

        //======== OBTENER CORRELATIVO Y SERIE ======
        $datos_correlativo  =   $this->s_correlativo->getCorrelativo($datos_validados->tipo_venta, $datos_validados->sede_id);

        //========== CALCULAR MONTOS ======
        $montos =   $this->s_calculos->calcularMontos($datos_validados->lstVenta, $datos_validados);

        if ($datos_validados->condicion->id == 1 && $datos_validados->isPay) {
            $this->s_validacion->validacionPagos($datos_validados->lstPagos, $montos->monto_total_pagar);
        }

        //======== OBTENIENDO LEYENDA ======
        $legenda    =   UtilidadesController::convertNumeroLetras($montos->monto_total_pagar);

        //======= INSERTAR VENTA =======
        $dto    =   $this->s_dto->dtoStore($datos_validados, $montos, $datos_correlativo, $legenda);
        $venta  =   $this->s_repository->store($dto);

        $this->s_repository->insertarDetalleVenta($datos_validados, $venta, 'STORE');

        if ($datos_validados->condicion->id == 1) {
            $this->saveImgsPago($datos_validados->lstImgsPagos, $venta);
        }

        //======== ASOCIAR LA VENTA CON EL MOVIMIENTO CAJA DEL COLABORADOR ====
        $this->s_repository->asociarVentaCaja($venta, $datos_validados);

        //======== EN CASO DE CONVERSIÓN DE DOCUMENTO =====
        $this->s_repository->asociarDocConvertido($datos_validados, $venta);

        //========== ACTUALIZAR ESTADO FACTURACIÓN A INICIADA ======
        DB::table('empresa_numeracion_facturaciones')
            ->where('empresa_id', Empresa::find(1)->id)
            ->where('sede_id', $datos_validados->sede_id)
            ->where('tipo_comprobante', $datos_validados->tipo_venta->id)
            ->where('emision_iniciada', '0')
            ->where('estado', 'ACTIVO')
            ->update([
                'emision_iniciada'       => '1',
                'updated_at'             => Carbon::now()
            ]);

        //======== EN CASO VENTA CONTADO Y PAGADA ELECTRÓNICO, VA AL KARDEX ===========
        if (
            $venta->condicion_id == 1
            && $venta->estado_pago == 'PAGADA'
            && !$datos_validados->atencion
            && !$datos_validados->documento_convertido
        ) {
            $this->s_kardex_cuenta->registrarDesdeVenta($venta);
        }

        //======= DESPACHO ======
        $data_envio =   $datos['data_envio'] ?? null;
        if ($data_envio) {
            $data_envio = json_decode($datos['data_envio']);

            $this->s_repository->insertarDespacho($venta, $data_envio, $datos_validados);
        }

        return $venta;
    }

    public function saveImgsPago(array $lstImgsPagos, Documento $sale)
    {
        if (array_key_exists(0, $lstImgsPagos) && $lstImgsPagos[0] instanceof UploadedFile) {
            $img1      = $lstImgsPagos[0];
            $img1_name = $sale->serie . '_' . $sale->correlativo . '_' . 'img_pago_1.' . $img1->getClientOriginalExtension();
            $sale->pago_1_img_nombre = $img1_name;
            $sale->pago_1_img_ruta   = 'ventas/pagos/' . $img1_name;
            $sale->save();
            UtilidadesController::saveFile($img1, $img1_name, 'ventas/pagos');
        }
        if (array_key_exists(1, $lstImgsPagos) && $lstImgsPagos[1] instanceof UploadedFile) {
            $img2      = $lstImgsPagos[1];
            $img2_name = $sale->serie . '_' . $sale->correlativo . '_' . 'img_pago_2.' . $img2->getClientOriginalExtension();
            $sale->pago_2_img_nombre = $img2_name;
            $sale->pago_2_img_ruta   = 'ventas/pagos/' . $img2_name;
            $sale->save();
            UtilidadesController::saveFile($img2, $img2_name, 'ventas/pagos');
        }
    }

    public function storePago(array $datos)
    {
        $lstPagos = is_string($datos['lstPagos'])
            ? json_decode($datos['lstPagos'], true)
            : $datos['lstPagos'];

        if (empty($lstPagos)) {
            throw new Exception('NO SE ENVIARON DATOS DE PAGO');
        }

        $hora_pago = $datos['hora_pago'] ?? null;
        $documento = Documento::find($datos['venta_id']);

        // ===== PAGO 1 =====
        $p1          = $lstPagos[0];
        $tipo_pago_1 = TipoPago::findOrFail($p1['metodoPagoId']);
        $cuenta_1    = null;

        if (!empty($p1['cuentaPagoId']) && $p1['metodoPagoId'] != 1) {
            $cuenta_1 = DB::selectOne('SELECT c.banco_nombre, c.nro_cuenta, c.cci, c.celular, c.titular, c.moneda
                            FROM tipo_pago_cuentas as tpc
                            INNER JOIN cuentas as c ON c.id = tpc.cuenta_id
                            INNER JOIN tipos_pago as tp ON tp.id = tpc.tipo_pago_id
                            WHERE tpc.cuenta_id = ? AND tpc.tipo_pago_id = ?
                            AND c.estado = "ACTIVO" AND tp.estado = "ACTIVO"
                            LIMIT 1', [$p1['cuentaPagoId'], $p1['metodoPagoId']]);

            if (!$cuenta_1) {
                throw new Exception('PAGO 1: NO EXISTE EL TIPO DE PAGO ASOCIADO CON LA CUENTA BANCARIA');
            }
        }

        // Calcular totales importe (no-efectivo) / efectivo
        $efectivo = 0.0;
        $importe  = 0.0;
        foreach ($lstPagos as $p) {
            if (intval($p['metodoPagoId']) === 1) {
                $efectivo += floatval($p['montoPago']);
            } else {
                $importe  += floatval($p['montoPago']);
            }
        }

        $documento->tipo_pago_id = $p1['metodoPagoId'];
        $documento->estado_pago  = 'PAGADA';
        $documento->importe      = $importe;
        $documento->efectivo     = $efectivo;

        $documento->pago_1_tipo_pago_id     = $p1['metodoPagoId'];
        $documento->pago_1_tipo_pago_nombre = $tipo_pago_1->descripcion;
        $documento->pago_1_monto            = floatval($p1['montoPago']);
        $documento->pago_1_fecha_operacion  = $p1['fechaOperacionPago'] ?? null;
        //$documento->pago_1_hora_operacion   = $hora_pago;
        $documento->pago_1_nro_operacion    = $p1['nroOperacionPago']   ?? null;
        $documento->pago_1_cuenta_id        = $p1['cuentaPagoId']       ?? null;

        if ($cuenta_1) {
            $documento->pago_1_banco_nombre = $cuenta_1->banco_nombre;
            $documento->pago_1_nro_cuenta   = $cuenta_1->nro_cuenta;
            $documento->pago_1_cci          = $cuenta_1->cci;
            $documento->pago_1_celular      = $cuenta_1->celular;
            $documento->pago_1_titular      = $cuenta_1->titular;
            $documento->pago_1_moneda       = $cuenta_1->moneda;
        }

        // ===== PAGO 2 (si existe) =====
        if (isset($lstPagos[1])) {
            $p2          = $lstPagos[1];
            $tipo_pago_2 = TipoPago::findOrFail($p2['metodoPagoId']);
            $cuenta_2    = null;

            if (!empty($p2['cuentaPagoId']) && $p2['metodoPagoId'] != 1) {
                $cuenta_2 = DB::selectOne('SELECT c.banco_nombre, c.nro_cuenta, c.cci, c.celular, c.titular, c.moneda
                                FROM tipo_pago_cuentas as tpc
                                INNER JOIN cuentas as c ON c.id = tpc.cuenta_id
                                INNER JOIN tipos_pago as tp ON tp.id = tpc.tipo_pago_id
                                WHERE tpc.cuenta_id = ? AND tpc.tipo_pago_id = ?
                                AND c.estado = "ACTIVO" AND tp.estado = "ACTIVO"
                                LIMIT 1', [$p2['cuentaPagoId'], $p2['metodoPagoId']]);

                if (!$cuenta_2) {
                    throw new Exception('PAGO 2: NO EXISTE EL TIPO DE PAGO ASOCIADO CON LA CUENTA BANCARIA');
                }
            }

            // Si pago 2 es no-efectivo pasa a ser el tipo_pago_id principal
            if ($p2['metodoPagoId'] != 1) {
                $documento->tipo_pago_id = $p2['metodoPagoId'];
            }

            $documento->pago_2_tipo_pago_id     = $p2['metodoPagoId'];
            $documento->pago_2_tipo_pago_nombre = $tipo_pago_2->descripcion;
            $documento->pago_2_monto            = floatval($p2['montoPago']);
            $documento->pago_2_fecha_operacion  = $p2['fechaOperacionPago'] ?? null;
            //$documento->pago_2_hora_operacion   = $hora_pago;
            $documento->pago_2_nro_operacion    = $p2['nroOperacionPago']   ?? null;
            $documento->pago_2_cuenta_id        = $p2['cuentaPagoId']       ?? null;

            if ($cuenta_2) {
                $documento->pago_2_banco_nombre = $cuenta_2->banco_nombre;
                $documento->pago_2_nro_cuenta   = $cuenta_2->nro_cuenta;
                $documento->pago_2_cci          = $cuenta_2->cci;
                $documento->pago_2_celular      = $cuenta_2->celular;
                $documento->pago_2_titular      = $cuenta_2->titular;
                $documento->pago_2_moneda       = $cuenta_2->moneda;
            }
        }

        $documento->save();

        // ===== IMÁGENES via saveImgsPago (columnas pago_N_img_*) =====
        $lstImgs = [];
        foreach (array_keys($lstPagos) as $i) {
            $key = "imagen_{$i}";
            if (isset($datos[$key]) && $datos[$key] instanceof UploadedFile) {
                $lstImgs[$i] = $datos[$key];
            }
        }
        if (!empty($lstImgs)) {
            $this->saveImgsPago($lstImgs, $documento);
        }

        // ===== PROPAGACIÓN A DOCUMENTO CONVERTIDO =====
        if ($documento->convert_en_id) {
            $doc_convertido                   = Documento::find($documento->convert_en_id);
            $doc_convertido->estado_pago      = $documento->estado_pago;
            $doc_convertido->update();
        }

        // ===== KARDEX (pago electrónico en venta contado) =====
        if ($documento->condicion_id == 1 && $documento->estado_pago == 'PAGADA') {
            $this->s_kardex_cuenta->registrarDesdeVenta($documento);
        }
    }

    public function operarVentaCreditoPagada(int $venta_id, $modo_despacho)
    {
        $lst_validado           =   $this->s_pct->analizarStockVenta($venta_id);
        $collect_validado       =   collect($lst_validado);
        $no_validos             =   $collect_validado->where('valido', false)->count();

        //======== GENERAR ORDEN DE PRODUCCIÓN ========
        if ($no_validos > 0) {
            $venta  =   Documento::findOrFail($venta_id);
            $this->s_orden_produccion->registrar($lst_validado, $venta->pedido_id);
        } else {
            //========= CONSUMIR STOCK =========
            foreach ($lst_validado as $item) {
                $this->s_pct->decrementarStocks($item->almacen_id, $item->producto_id, $item->color_id, $item->talla_id, $item->cantidad);
            }
            $this->s_despacho->generarDespachoDefecto($venta_id, $modo_despacho);
        }
    }

    public function update(array $datos, int $id): Documento
    {
        $datos_validados        =   $this->s_validacion->validacionUpdate($datos, $id);

        //========== CALCULAR MONTOS ======
        $montos                     =   $this->s_calculos->calcularMontos($datos_validados->lstVenta, $datos_validados);
        $datos_validados->montos    =   $montos;

        //======== OBTENIENDO LEYENDA ======
        $legenda                    =   UtilidadesController::convertNumeroLetras($montos->monto_total_pagar);
        $datos_validados->legenda   =   $legenda;

        $documento          =   $this->s_repository->actualizarVenta($datos_validados, $datos_validados->documento);

        $detalle_anterior   =   Detalle::where('documento_id', $id)->get();

        //======== DEVOLVER STOCKS =======
        foreach ($detalle_anterior as $da) {
            $this->s_pct->incrementarStocks($da->almacen_id, $da->producto_id, $da->color_id, $da->talla_id, $da->cantidad);
        }

        //========= ELIMINAR DETALLE ANTERIOR =========
        $this->s_repository->eliminarDetalle($id);
        $this->s_repository->insertarDetalleVenta($datos_validados, $documento, 'UPDATE');

        //======== EN CASO VENTA CONTADO Y PAGADA ELECTRÓNICO, VA AL KARDEX ===========
        if ($documento->condicion_id == 1 && $documento->tipo_pago_id != 1 && $documento->estado_pago == 'PAGADA') {
            $this->s_kardex_cuenta->actualizarDesdeVenta($documento);
        }

        $datos_envio    =   $datos['data_envio'] ?? null;
        $tiene_envio    =   EnvioVenta::where('documento_id', $id)->select('id', 'estado')->first();
        if (!$datos_envio) {
            return $documento;
        }

        //========= SI YA TENÍA ENVÍO ACTUALIZAMOS ========
        $datos_envio                    =   json_decode($datos_envio);
        $datos_envio                    =   (array)$datos_envio;
        $datos_envio['documento_id']    =   $id;
        $datos_envio['destinatario']    =   (array)$datos_envio['destinatario'];
        if ($tiene_envio) {
            if ($tiene_envio->estado == 'PENDIENTE') {
                $this->s_despacho->update($datos_envio);
            }
        } else {
            //======= NUEVO ENVÍO ========
            $this->s_despacho->store($datos_envio);
        }

        return $documento;
    }

    public function getVoucherPdf(int $id, int $size): array
    {
        $documento  =   Documento::findOrFail($id);

        $this->qr_code($id);

        $detalles           =   Detalle::where('documento_id', $id)->where('eliminado', '0')->get();

        $mostrar_cuentas    =   DB::select('SELECT
                                c.propiedad
                                FROM configuracion AS c
                                WHERE c.slug = "MCB"')[0]->propiedad;

        $cuenta             =   CuentaCliente::where('cotizacion_documento_id', $id)->first();
        $detalle_pago       =   [];
        if ($cuenta) {
            $detalle_pago = DetalleCuentaCliente::from('detalle_cuenta_cliente as dcc')
                ->join('tipos_pago as tp', 'tp.id', '=', 'dcc.tipo_pago_id')
                ->where('dcc.cuenta_cliente_id', $cuenta->id)
                ->select(
                    'dcc.*',
                    'tp.descripcion as tipo_pago_nombre'
                )
                ->orderBy('dcc.created_at')
                ->get();
        }

        $empresa            =   Empresa::find(1);
        $sede               =   Sede::find($documento->sede_id);
        $despacho           =   EnvioVenta::where('documento_id', $id)->first();

        $pdf    =   Pdf::loadview('ventas.documentos.impresion.comprobante_ticket', [
            'documento'         =>  $documento,
            'detalles'          =>  $detalles,
            'empresa'           =>  $empresa,
            'mostrar_cuentas'   =>  $mostrar_cuentas,
            'sede'              =>  $sede,
            'despacho'          =>  $despacho,
            'cuenta'            =>  $cuenta,
            'detalle_pago'      =>  $detalle_pago
        ])->setPaper([0, 0, 226.772, 651.95]);

        if ($size == 80) {
            $pdf    =   $pdf->setPaper([0, 0, 226.772, 651.95]);
        }
        if ($size == 100) {
            $pdf    =   $pdf->setPaper('a4')->setWarnings(false);
        }

        return ['pdf' => $pdf, 'nombre' => $documento->serie . '-' . $documento->correlativo . '.pdf'];
    }

    public function qr_code(int $id): array
    {

        $documento = Documento::findOrFail($id);
        $name_qr = '';

        if ($documento->contingencia == '0') {
            $name_qr = $documento->serie . "-" . $documento->correlativo . '.svg';
        } else {
            $name_qr = $documento->serie_contingencia . "-" . $documento->correlativo . '.svg';
        }

        //======= NOTA DE VENTA =====
        if ($documento->tipo_venta_id == 129) {
            $data_qr = $documento->ruc_empresa . '|' .        // RUC
                '04' . '|' .                           // Tipo de Documento (04 para Nota de Venta)
                ($documento->contingencia == '0' ? $documento->serie : $documento->serie_contingencia) . '|' . // SERIE
                $documento->correlativo . '|' .        // NUMERO
                (float) $documento->total_pagar . '|' .      // MTO TOTAL DEL COMPROBANTE
                $documento->created_at; // FECHA DE EMISION
        } else {

            //========= BOLETA O FACTURA =======
            $data_qr =  $documento->ruc_empresa . '|' .                // RUC
                $documento->tipoDocumento() . '|' .            // TIPO DE DOCUMENTO
                ($documento->contingencia == '0' ? $documento->serie : $documento->serie_contingencia) . '|' . // SERIE
                $documento->correlativo . '|' .                // NUMERO
                (float) $documento->total_igv . '|' .                                     // MTO TOTAL IGV
                (float) $documento->total_pagar . '|' .              // MTO TOTAL DEL COMPROBANTE
                $documento->created_at . '|' .  // FECHA DE EMISION
                $documento->tipoDocumentoCliente() . '|' .     // TIPO DE DOCUMENTO ADQUIRENTE
                $documento->documento_cliente;                 // NUMERO DE DOCUMENTO ADQUIRENTE

        }


        $miQr = QrCode::format('svg')
            ->size(130)
            ->backgroundColor(0, 0, 0)
            ->color(255, 255, 255)
            ->margin(1)
            ->generate($data_qr);

        $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . $name_qr);

        // Crea el directorio si no existe
        if (!file_exists(dirname($pathToFile_qr))) {
            mkdir(dirname($pathToFile_qr), 0755, true);
        }

        // Guarda el QR en el archivo
        file_put_contents($pathToFile_qr, $miQr);

        // Actualiza la ruta del QR en la base de datos
        $documento->ruta_qr = 'public/qrs/' . $name_qr;
        $documento->update();

        return array('success' => true, 'mensaje' => 'QR creado exitosamente');
    }

    public function getColoresTallas($almacen_id, $producto_id): object
    {
        $s_producto     =   new ProductoService();
        $precios_venta  =   $s_producto->getPreciosVenta($producto_id);
        $stocks         =   $s_producto->getProductoStocks($almacen_id, $producto_id);
        $colores        =   $s_producto->getProductoColores($almacen_id, $producto_id);

        return (object)[
            'stocks'    =>  $stocks,
            'producto_colores'  =>  $colores,
            'precios_venta_array'   =>  $precios_venta
        ];
    }

    public function queryStockDisponible(array $filters): \Illuminate\Database\Query\Builder
    {
        return $this->s_repository->queryStockDisponible($filters);
    }

    public function getStocksMatriz(array $params): object
    {
        $almacen_id   = $params['almacen_id']   ?? null;
        $categoria_id = $params['categoria_id'] ?? null;
        $marca_id     = $params['marca_id']     ?? null;
        $modelo_id    = $params['modelo_id']    ?? null;
        $color_id     = $params['color_id']     ?? null;
        $talla_id     = $params['talla_id']     ?? null;
        $producto_id  = $params['producto_id']  ?? null;

        $s_producto     =   new ProductoService();
        $precios_venta  =   $s_producto->getPreciosVenta($producto_id);
        $stocks         =   $s_producto->getVariantsConStock($almacen_id, $producto_id);
        $colores        =   $s_producto->getProductoColores($almacen_id, $producto_id);

        return (object)[
            'stocks'           => $stocks,
            'producto_colores' => $colores,
            'precios_venta'    => $precios_venta
        ];
    }

    /**
     * Genera el comprobante fiscal (y, para recojo en tienda, el
     * despacho) de una reserva_web confirmada.
     * (docs/PLANIFICATIONS/2026-07-15-plan-despacho-web-auto.md,
     * docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md Fase 2)
     *
     * `facturar=true` evita que store()/insertarDetalleVenta() vuelva a
     * descontar stock (ya se descontó en
     * Api\ReservasWeb\ReservaWebController::store()) — verificado leyendo
     * VentaRepository::insertarDetalleVenta() líneas 100-143. Ese mismo
     * flag hace que VentaRepository::insertarDespacho() NO cree el
     * EnvioVenta (se salta cuando facturar=true) — por eso el EnvioVenta
     * se arma acá aparte, en crearEnvioRecojoTienda(), en vez de mandar
     * `data_envio` en $datos. No se toca insertarDespacho() ni su
     * condición: es código compartido con el flujo interno de Pedidos,
     * que también depende de facturar=true + sin despacho ahí.
     *
     * Alcance actual (decidido 2026-07-15): solo recojo en tienda genera
     * despacho automático — el mapeo sede→empresa de envío ya existe en
     * el catálogo real. Envío a domicilio genera el comprobante pero el
     * despacho lo arma el staff a mano, como hoy (sin transportista por
     * defecto definido todavía).
     */
    public function storeFromEcommerce(ReservaWeb $reserva, string $modo = 'PRODUCCION'): Documento
    {
        $reserva->loadMissing('detalle');

        $esFactura = (bool) $reserva->desea_factura;

        // ===== 1. Cliente: buscar o crear/actualizar por documento, nunca duplicar =====
        // tabladetalles tabla_id=3 (tipo de documento): 6=DNI, 7=CARNET EXT., 8=RUC
        $tipoDocumentoId = $esFactura ? 8 : ($reserva->doc_tipo === 'DNI' ? 6 : 7);
        $numeroDocumento = $esFactura ? $reserva->ruc : $reserva->doc_numero;
        $tipoDocumentoDetalle = TablaDetalle::findOrFail($tipoDocumentoId);
        $nombreCliente = $esFactura ? $reserva->razon_social : $reserva->cliente_nombre;

        $cliente = Cliente::where('tipo_documento', $tipoDocumentoDetalle->simbolo)
            ->where('documento', $numeroDocumento)
            ->where('estado', 'ACTIVO')
            ->first();

        if ($cliente) {
            // Decidido 2026-07-15: actualizar siempre con lo más reciente.
            $cliente->nombre = mb_strtoupper($nombreCliente, 'UTF-8');
            $cliente->correo_electronico = $reserva->cliente_email;
            $cliente->telefono_movil = $reserva->cliente_telefono;
            $cliente->direccion = $reserva->cliente_direccion ?: 'S/D'; // direccion_cliente es NOT NULL en cotizacion_documento
            $cliente->save();
        } else {
            $tipoCliente = TipoCliente::findOrFail(3); // UNIDAD (decidido 2026-07-15)
            $cliente = new Cliente();
            $cliente->tipo_documento_id = $tipoDocumentoDetalle->id;
            $cliente->tipo_documento = $tipoDocumentoDetalle->simbolo;
            $cliente->documento = $numeroDocumento;
            $cliente->nombre = mb_strtoupper($nombreCliente, 'UTF-8');
            $cliente->correo_electronico = $reserva->cliente_email;
            $cliente->telefono_movil = $reserva->cliente_telefono;
            $cliente->direccion = $reserva->cliente_direccion ?: 'S/D'; // direccion_cliente es NOT NULL en cotizacion_documento
            $cliente->tipo_cliente_id = $tipoCliente->id;
            $cliente->tipo_cliente_nombre = $tipoCliente->nombre;
            $cliente->estado = 'ACTIVO';
            $cliente->save();
        }

        // ===== 2. Detalle de venta, agrupado por producto+color (shape que espera insertarDetalleVenta()) =====
        $almacen = Almacen::findOrFail($reserva->almacen_id);

        $grupos = $reserva->detalle->groupBy(fn ($d) => $d->producto_id . '-' . $d->color_id);
        $lstVenta = [];
        foreach ($grupos as $lineas) {
            $first = $lineas->first();
            $lstVenta[] = [
                'producto_id'          => $first->producto_id,
                'color_id'             => $first->color_id,
                'producto_nombre'      => DB::table('productos')->where('id', $first->producto_id)->value('nombre'),
                'color_nombre'         => DB::table('colores')->where('id', $first->color_id)->value('descripcion'),
                'precio_venta'         => (float) $first->precio_venta_1,
                'precio_venta_nuevo'   => (float) $first->precio_venta_1,
                'porcentaje_descuento' => 0,
                'tallas'               => $lineas->map(fn ($d) => [
                    'talla_id'     => $d->talla_id,
                    'talla_nombre' => DB::table('tallas')->where('id', $d->talla_id)->value('descripcion'),
                    'cantidad'     => (int) $d->cantidad,
                ])->values()->all(),
            ];
        }

        // ===== 3. Medio de pago =====
        // Decidido 2026-07-15: 'card'→TRANSFERENCIA (sin pasarela real,
        // hoy en la práctica es transferencia/depósito), 'yape'→YAPE
        // directo. cuenta_id=1 (BCP "MERRIS CALZADO EIRL") es la única
        // cuenta activa ligada a ambos tipos de pago.
        $metodoPagoIdPorMedio = ['card' => 2, 'yape' => 3];
        $metodoPagoId = $metodoPagoIdPorMedio[$reserva->metodo_pago] ?? 1;
        $lstPagos = [[
            'metodoPagoId'       => $metodoPagoId,
            'cuentaPagoId'       => $metodoPagoId === 1 ? null : 1,
            'montoPago'          => (float) $reserva->total,
            'nroOperacionPago'   => $reserva->pago_referencia ?: ('WEB-' . $reserva->codigo_pedido_ecommerce),
            'fechaOperacionPago' => now()->toDateString(),
        ]];

        // ===== 4. Generar el Documento (comprobante) =====
        $datos = [
            'sede_id'             => $almacen->sede_id,
            'productos_tabla'     => json_encode($lstVenta),
            // DEMO (docs 2026-07-16): siempre NOTA DE VENTA (129) — nunca va a
            // SUNAT, no consume numeración de boleta/factura real.
            'tipo_venta'          => $modo === 'DEMO' ? 129 : ($esFactura ? 127 : 128),
            'cliente_id'          => $cliente->id,
            'almacenSeleccionado' => $almacen->id,
            'condicion_id'        => '1', // CONTADO
            'isPay'               => true,
            'lstPagos'            => $lstPagos,
            'facturar'            => true,
            'modo'                => 'CONSUMO',
            'observacion'         => 'VENTA ECOMMERCE WEB - PEDIDO ' . $reserva->codigo_pedido_ecommerce,
            'monto_embalaje'      => 0,
            'monto_envio'         => 0,
        ];

        $documento = $this->store($datos);

        // ===== 5. Despacho — solo recojo en tienda por ahora (§ arriba) =====
        if ($reserva->sede_recojo_id) {
            $this->crearEnvioRecojoTienda($documento, $reserva);
        }

        return $documento;
    }

    /**
     * Mapa sede→empresa de envío "RECOJO EN TIENDA"/"RECOJO EN ALMACEN"
     * ya existente en el catálogo real (`empresas_envio` 40/41/42,
     * `empresa_envio_sedes` 696/697/698). Si se agregan más sedes con
     * recojo, sumarlas acá (o migrar a una columna en `empresa_sedes`
     * más adelante si esto crece).
     */
    private function crearEnvioRecojoTienda(Documento $documento, ReservaWeb $reserva): void
    {
        $mapaEmpresaEnvio = [
            1 => ['empresa_envio_id' => 42, 'sede_envio_id' => 698], // SEDE CENTRAL
            2 => ['empresa_envio_id' => 40, 'sede_envio_id' => 696], // TIENDA TRUJILLO
            3 => ['empresa_envio_id' => 41, 'sede_envio_id' => 697], // SEDE CHICLAYO
        ];

        $mapa = $mapaEmpresaEnvio[$reserva->sede_recojo_id] ?? null;
        if (!$mapa) {
            return; // sede de recojo sin mapeo conocido — despacho queda manual, no falla la venta
        }

        $empresaEnvio  = MetodoEntrega::findOrFail($mapa['empresa_envio_id']);
        $sedeEnvio     = EmpresaEnvioSede::findOrFail($mapa['sede_envio_id']);
        $tipoEnvio     = TablaDetalle::findOrFail(189); // RECOJO EN TIENDA
        $tipoPagoEnvio = TablaDetalle::findOrFail(195); // ENVÍO GRATIS
        $origenVenta   = TablaDetalle::where('descripcion', 'ECOMMERCE WEB')->firstOrFail();

        EnvioVenta::create([
            'documento_id'          => $documento->id,
            'departamento'          => $sedeEnvio->departamento,
            'provincia'             => $sedeEnvio->provincia,
            'distrito'              => $sedeEnvio->distrito,
            'empresa_envio_id'      => $empresaEnvio->id,
            'empresa_envio_nombre'  => $empresaEnvio->empresa,
            'sede_envio_id'         => $sedeEnvio->id,
            'sede_envio_nombre'     => $sedeEnvio->direccion,
            'tipo_envio'            => $tipoEnvio->descripcion,
            'tipo_envio_id'         => $tipoEnvio->id,
            'destinatario_tipo_doc' => $reserva->doc_tipo,
            'destinatario_nro_doc'  => $reserva->doc_numero,
            'destinatario_nombre'   => $reserva->cliente_nombre,
            'cliente_id'            => $documento->cliente_id,
            'cliente_nombre'        => $documento->cliente,
            'cliente_celular'       => $reserva->cliente_telefono,
            'tipo_pago_envio'       => $tipoPagoEnvio->descripcion,
            'tipo_pago_envio_id'    => $tipoPagoEnvio->id,
            'monto_envio'           => 0,
            'entrega_domicilio'     => 'NO',
            'direccion_entrega'     => $sedeEnvio->direccion,
            'documento_nro'         => $documento->serie . '-' . $documento->correlativo,
            'fecha_envio_propuesta' => now()->toDateString(),
            'origen_venta'          => $origenVenta->descripcion,
            'origen_venta_id'       => $origenVenta->id,
            'obs_rotulo'            => null,
            'obs_despacho'          => 'RECOJO EN TIENDA - RESERVA WEB ' . $reserva->codigo_pedido_ecommerce,
            'usuario_nombre'        => Auth::user()->usuario,
            'user_vendedor_id'      => $documento->user_id,
            'user_vendedor_nombre'  => $documento->registrador_nombre,
            'almacen_id'            => $documento->almacen_id,
            'almacen_nombre'        => $documento->almacen_nombre,
            'sede_id'               => $documento->sede_id,
            'sede_despachadora_id'  => $reserva->sede_recojo_id,
            'estado'                => 'PENDIENTE',
        ]);
    }
}
