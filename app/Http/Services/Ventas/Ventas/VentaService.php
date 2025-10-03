<?php

namespace App\Http\Services\Ventas\Ventas;

use App\Http\Controllers\UtilidadesController;
use App\Http\Services\Almacen\ProductoColorTalla\ProductoColorTallaService;
use App\Http\Services\Kardex\Cuenta\KardexCuentaService;
use App\Http\Services\Produccion\Orden\OrdenProduccionService;
use App\Http\Services\Ventas\Despacho\DespachoService;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Sedes\Sede;
use App\Mantenimiento\TipoPago\TipoPago;
use App\Ventas\CuentaCliente;
use App\Ventas\DetalleCuentaCliente;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\EnvioVenta;
use Carbon\Carbon;
use Exception;
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

    public function __construct()
    {
        $this->s_validacion     =   new VentaValidacion();
        $this->s_correlativo    =   new CorrelativoService();
        $this->s_calculos       =   new CalculosService();
        $this->s_repository     =   new VentaRepository();
        $this->s_despacho       =   new DespachoService();
        $this->s_pct            =   new ProductoColorTallaService();
        $this->s_orden_produccion   =   new OrdenProduccionService();
        $this->s_kardex_cuenta      =   new KardexCuentaService();
    }

    public function registrar(array $datos): Documento
    {
        //========= VALIDACIONES COMPLEJAS ======
        $datos_validados    =   $this->s_validacion->validacionStore($datos);

        $this->s_validacion->comprobanteActivo($datos_validados->sede_id, $datos_validados->tipo_venta);

        //======== OBTENER CORRELATIVO Y SERIE ======
        $datos_correlativo  =   $this->s_correlativo->getCorrelativo($datos_validados->tipo_venta, $datos_validados->sede_id);

        //========== CALCULAR MONTOS ======
        $montos =   $this->s_calculos->calcularMontos($datos_validados->lstVenta, $datos_validados);

        //======== OBTENIENDO LEYENDA ======
        $legenda                =   UtilidadesController::convertNumeroLetras($montos->monto_total_pagar);

        //======= INSERTAR VENTA =======
        $venta  =   $this->s_repository->insertarVenta($datos_validados, $montos, $datos_correlativo, $legenda);

        $this->s_repository->insertarDetalleVenta($datos_validados, $venta);

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
        if ($venta->condicion_id == 1 && $venta->tipo_pago_id != 1 && $venta->estado_pago == 'PAGADA') {
            $this->s_kardex_cuenta->registrarDesdeVenta($venta);
        }

        //======= DESPACHO ======
        $data_envio =   $datos['data_envio']??null;
        if ($data_envio) {
            $data_envio = json_decode($datos['data_envio']);

            $this->s_repository->insertarDespacho($venta, $data_envio, $datos_validados);
        }

        return $venta;
    }

    public function storePago(array $datos)
    {
        $cuenta_id      =   $datos['cuenta_id'] ?? null;
        $tipo_pago_id   =   $datos['tipo_pago_id'] ?? null;
        $tipo_pago      =   TipoPago::findOrFail($tipo_pago_id);

        $validacion =   DB::selectOne('SELECT
                            c.banco_nombre,
                            c.nro_cuenta,
                            c.cci,
                            c.celular,
                            c.titular,
                            c.moneda
                            FROM tipo_pago_cuentas as tpc
                            INNER JOIN cuentas as c ON c.id = tpc.cuenta_id
                            INNER JOIN tipos_pago as tp ON tp.id = tpc.tipo_pago_id
                            WHERE tpc.cuenta_id = ?
                            AND tpc.tipo_pago_id = ?
                            AND c.estado = "ACTIVO"
                            AND tp.estado = "ACTIVO"
                            LIMIT 1', [$cuenta_id, $tipo_pago_id]);

        if (!$validacion && $cuenta_id && $tipo_pago_id != 1) {
            throw new Exception('NO EXISTE EL TIPO DE PAGO ASOCIADO CON LA CUENTA BANCARIA SELECCIONADA');
        }

        $documento                      = Documento::find($datos['venta_id']);

        $documento->tipo_pago_id            = $datos['tipo_pago_id'] ?? null;
        $documento->importe                 = $datos['importe'] ?? 0;
        $documento->efectivo                = $datos['efectivo'] ?? 0;
        $documento->estado_pago             = 'PAGADA';
        $documento->pago_1_cuenta_id        = $datos['cuenta_id'] ?? null;
        $documento->pago_1_tipo_pago_nombre = $tipo_pago->descripcion;
        $documento->pago_1_tipo_pago_id     = $datos['tipo_pago_id'];
        $documento->pago_1_monto            = $datos['importe'] ?? 0;

        if ($validacion) {
            $documento->pago_1_banco_nombre     = $validacion->banco_nombre;
            $documento->pago_1_nro_cuenta       = $validacion->nro_cuenta;
            $documento->pago_1_cci              = $validacion->cci;
            $documento->pago_1_celular          = $validacion->celular;
            $documento->pago_1_titular          = $validacion->titular;
            $documento->pago_1_moneda           = $validacion->moneda;
            $documento->pago_1_fecha_operacion  = $datos['fecha_pago'] ?? null;
            $documento->pago_1_hora_operacion   = $datos['hora_pago'] ?? null;
            $documento->pago_1_nro_operacion    = $datos['nro_operacion'] ?? null;
        }

        if (isset($datos['imagen']) && $datos['imagen'] instanceof UploadedFile) {
            if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'))) {
                mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'));
            }
            $extension              =   $datos['imagen']->getClientOriginalExtension();
            $nombreImagenPago       =   $documento->serie . '-' . $documento->correlativo . '.' . $extension;
            $documento->ruta_pago   =   $datos['imagen']->storeAs('public/pagos', $nombreImagenPago);
        }

        if (isset($datos['imagen2']) && $datos['imagen2'] instanceof UploadedFile) {
            if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'))) {
                mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'));
            }
            $extension              =   $datos['imagen2']->getClientOriginalExtension();
            $nombreImagenPago       =   $documento->serie . '-' . $documento->correlativo . '-2' . '.' . $extension;
            $documento->ruta_pago_2 =   $datos['imagen2']->storeAs('public/pagos', $nombreImagenPago);
        }

        $documento->update();

        if ($documento->convertir) {
            $doc_convertido                     = Documento::find($documento->convertir);
            $doc_convertido->estado_pago        = $documento->estado_pago;
            $doc_convertido->importe            = $documento->importe;
            $doc_convertido->efectivo           = $documento->efectivo;
            $doc_convertido->tipo_pago_id       = $documento->tipo_pago_id;
            $doc_convertido->banco_empresa_id   = $documento->banco_empresa_id;
            $doc_convertido->ruta_pago          = $documento->ruta_pago;
            $doc_convertido->update();
        }

        //======== EN CASO VENTA CONTADO Y PAGADA ELECTRÓNICO, VA AL KARDEX ===========
        if ($documento->condicion_id == 1 && $documento->tipo_pago_id != 1 && $documento->estado_pago == 'PAGADA') {
            $this->s_kardex_cuenta->registrarDesdeVenta($documento);
        }

        //========= DOCUMENTO CONTADO PAGADO =======
        /*
        if ($documento->condicion_id == 1 && $documento->estado_pago === 'PAGADA') {
            $this->s_despacho->generarDespachoDefecto($documento->id, "VENTA");
        }
        */

        //========== CANJEANDO RECIBOS DE CAJA ========
        /*if ($request->get('modo_pago') === "4-RECIBO DE CAJA") {
                //======== OBTENEMOS TODOS LOS RECIBOS DE CAJA DEL CLIENTE ==========
                $recibos_caja_cliente   =   DB::select(
                    'SELECT * FROM recibos_caja AS rc
                                            WHERE rc.cliente_id=?
                                            AND rc.saldo>0
                                            AND rc.estado="ACTIVO"
                                            AND (rc.estado_servicio="LIBRE" OR rc.estado_servicio="USANDO")
                                            ORDER BY rc.created_at',
                    [$documento->cliente_id]
                );

                $total_pendiente    =   $documento->total_pagar;

                //========= RESTAMOS SALDO EN ORDEN ASC POR FECHA DE CREACIÓN =========
                foreach ($recibos_caja_cliente as $recibo) {

                    $saldo_recibo       =   $recibo->saldo;

                    //======= SI EL TOTAL PENDIENTE >= SALDO DEL RECIBO CAJA ========
                    if ($total_pendiente >= $saldo_recibo) {
                        //======= GUARDAMOS SALDO ANTERIOR DEL RECIBO =======
                        $saldo_anterior_recibo          =       $recibo->saldo;
                        //======= CONSUMIR TODO EL SALDO DEL RECIBO ========
                        $nuevo_saldo_recibo             =       0;
                        //======= NUEVO ESTADO DEL RECIBO ========
                        $nuevo_estado_servicio_recibo   =   'CANJEADO';
                        //========= TOTAL PENDIENTE BAJA SEGÚN EL SALDO DEL RECIBO =========
                        $total_pendiente                -=      $saldo_recibo;

                        //======= ACTUALIZAMOS EL RECIBO ========
                        DB::table('recibos_caja')
                            ->where('id', $recibo->id)
                            ->update([
                                'saldo' => $nuevo_saldo_recibo,
                                'estado_servicio' => $nuevo_estado_servicio_recibo,
                                'updated_at' => now()
                            ]);

                        //========= GRABAMOS EL DETALLE DE USO DEL RECIBO ======
                        DB::table('recibos_caja_detalle')
                            ->insert([
                                'recibo_id'    => $recibo->id,
                                'documento_id' => $documento->id,
                                'saldo_antes'  => $saldo_anterior_recibo,
                                'monto_usado'  => $saldo_recibo,
                                'saldo_despues' => $nuevo_saldo_recibo,
                                'created_at'   => now(),
                                'updated_at'   => now()
                            ]);
                    } else {
                        //======= SI EL TOTAL PENDIENTE ES MENOR AL SALDO DEL RECIBO ========
                        //======== SALDO ANTERIOR RECIBO =========
                        $saldo_anterior_recibo          =   $recibo->saldo;
                        //======== MONTO USADO ===========
                        $monto_usado                    =   $total_pendiente;
                        //======== CONSUMIR UNA PARTE DEL SALDO DEL RECIBO =======
                        $nuevo_saldo_recibo             =   $recibo->saldo  -   $total_pendiente;
                        //======== TOTAL PENDIENTE BAJA A 0 =======
                        $total_pendiente                =   0;
                        //======== ACTUALIZAR ESTADO DEL RECIBO =========
                        $nuevo_estado_servicio_recibo   =   'USANDO';

                        //======= ACTUALIZAMOS EL RECIBO ========
                        DB::table('recibos_caja')
                            ->where('id', $recibo->id)
                            ->update([
                                'saldo' => $nuevo_saldo_recibo,
                                'estado_servicio' => $nuevo_estado_servicio_recibo,
                                'updated_at' => now()
                            ]);

                        //========= GRABAMOS EL DETALLE DE USO DEL RECIBO ======
                        DB::table('recibos_caja_detalle')
                            ->insert([
                                'recibo_id'   =>  $recibo->id,
                                'documento_id'          =>  $documento->id,
                                'saldo_antes'           =>  $saldo_anterior_recibo,
                                'monto_usado'           =>  $monto_usado,
                                'saldo_despues'         =>  $nuevo_saldo_recibo,
                                'created_at'            =>  now(),
                                'updated_at'            =>  now()
                            ]);
                    }

                    //======== DETENER EL BUCLE SI EL TOTAL PENDIENTE ES 0 ========
                    if ($total_pendiente === 0) {
                        break;
                    }
                }
            }*/
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

        $documento  =   $this->s_repository->actualizarVenta($datos_validados, $datos_validados->documento);

        $detalle_anterior   =   Detalle::where('documento_id', $id)->get();

        //======== DEVOLVER STOCKS =======
        foreach ($detalle_anterior as $da) {
            $this->s_pct->incrementarStocks($da->almacen_id, $da->producto_id, $da->color_id, $da->talla_id, $da->cantidad);
        }

        //========= ELIMINAR DETALLE ANTERIOR =========
        $this->s_repository->eliminarDetalle($id);
        $this->s_repository->insertarDetalleVenta($datos_validados, $documento);

        //======== EN CASO VENTA CONTADO Y PAGADA ELECTRÓNICO, VA AL KARDEX ===========
        if ($documento->condicion_id == 1 && $documento->tipo_pago_id != 1 && $documento->estado_pago == 'PAGADA') {
            $this->s_kardex_cuenta->actualizarDesdeVenta($documento);
        }

        $datos_envio    =   json_decode($datos['data_envio']);
        $tiene_envio    =   EnvioVenta::where('documento_id', $id)->select('id')->first();

        if (!$datos_envio) {
            return $documento;
        }

        //========= SI YA TENÍA ENVÍO ACTUALIZAMOS ========
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

}
