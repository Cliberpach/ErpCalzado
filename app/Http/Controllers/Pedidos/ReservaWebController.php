<?php

namespace App\Http\Controllers\Pedidos;

use App\Almacenes\Almacen;
use App\Almacenes\Kardex;
use App\Almacenes\Traslado;
use App\Almacenes\TrasladoDetalle;
use App\Configuracion\Configuracion;
use App\Events\ReservaWebResueltaEvent;
use App\Http\Controllers\Controller;
use App\Http\Services\Ventas\Ventas\VentaService;
use App\Jobs\Ventas\EnviarComprobanteEmailJob;
use App\Models\Ventas\ReservaWeb\ReservaWeb;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\EnvioVenta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

/**
 * Submódulo Reservas Web, dentro del módulo Pedidos — carrito Fase F
 * (docs/PLANIFICATIONS/2026-07-11-carrito-plan.md §7). Un pedido
 * confirmado en el checkout de ecommerceMerris crea acá una reserva
 * (Api\ReservasWeb\ReservaWebController::store(), separa stock al
 * instante). Este controller es el mantenedor: el usuario de ErpCalzado
 * revisa manualmente que el pago llegó y Confirma o Anula.
 *
 * Recojo en tienda (docs/PLANIFICATIONS/2026-07-15-plan-recojo-tienda.md
 * §2.3): si la sede elegida por el cliente no tenía stock completo al
 * crear la reserva, el detalle queda con `cantidad_pendiente > 0`.
 * `cubrirStock()` cubre ese faltante con un traslado directo desde otra
 * sede (elegida a mano por el staff, sin sugerencia automática) hacia la
 * sede del cliente — nunca pasa por un almacén intermedio fijo.
 */
class ReservaWebController extends Controller
{
    public function index()
    {
        return view('pedidos.reservas-web.index');
    }

    public function getModo()
    {
        $modo = optional(Configuracion::where('slug', 'RWM')->first())->propiedad ?: 'PRODUCCION';

        return response()->json(['success' => true, 'modo' => $modo]);
    }

    /**
     * Switch DEMO/PRODUCCION — mismo patrón que
     * ConfiguracionController::setGreenterModo(). En DEMO, confirmar()
     * emite NOTA DE VENTA (129) en vez de boleta/factura real.
     */
    public function setModo(Request $request)
    {
        $data = $request->validate([
            'modo' => ['required', 'in:DEMO,PRODUCCION'],
        ]);

        $config = Configuracion::firstOrNew(['slug' => 'RWM']);
        $config->descripcion = $config->descripcion ?: 'Modo Reservas Web (DEMO emite Nota de Venta / PRODUCCION emite Boleta o Factura)';
        $config->propiedad = $data['modo'];
        $config->save();

        return response()->json(['success' => true, 'message' => 'Modo cambiado a ' . $data['modo'], 'modo' => $data['modo']]);
    }

    /**
     * Página dedicada de detalle/acciones de una reserva (reemplaza el
     * modal "Ver" — Confirmar/Cubrir stock/Anular viven acá, no en la
     * tabla). Datos se cargan por AJAX contra getDetalle(), igual que
     * hacía el modal.
     */
    public function show(int $id)
    {
        ReservaWeb::findOrFail($id);

        return view('pedidos.reservas-web.show', ['id' => $id]);
    }

    public function getTable(Request $request)
    {
        $query = ReservaWeb::with('detalle', 'sedeRecojo', 'envioVenta')->orderBy('fecha_reserva', 'desc');

        return DataTables::of($query)
            ->addColumn('detalle_count', fn ($r) => $r->detalle->count())
            ->addColumn('tiene_pendiente', fn ($r) => $r->detalle->sum('cantidad_pendiente') > 0)
            ->addColumn('sede_recojo_nombre', fn ($r) => optional($r->sedeRecojo)->nombre)
            ->addColumn('despacho_estado', fn ($r) => $this->calcularDespachoEstado($r))
            ->make(true);
    }

    /**
     * Fase 2 de docs/PLANIFICATIONS/2026-07-17-flujo-envio-domicilio.md:
     * "Falta despacho" cubre el caso normal de domicilio (nunca se
     * genera EnvioVenta automático, ver VentaService::storeFromEcommerce()
     * §5) y también el caso borde de recojo cuando la sede no tiene mapeo
     * de empresa de envío (crearEnvioRecojoTienda() se sale sin crear
     * nada, ver VentaService.php:654-657) — no distingue método de envío
     * a propósito, ambos casos son igual de reales.
     * "Despacho estancado" cubre la anécdota del usuario con Bata: un
     * EnvioVenta que se generó pero nadie marcó DESPACHADO después de
     * varios días — mitigación del lado staff, no promesa de SLA al
     * cliente (ver ese mismo plan §4 Fase 2 punto 4).
     */
    private function calcularDespachoEstado(ReservaWeb $reserva): ?string
    {
        if ($reserva->estado !== 'CONFIRMADO') {
            return null;
        }

        if (!$reserva->envioVenta) {
            return 'FALTA_DESPACHO';
        }

        $estancado = $reserva->envioVenta->estado !== 'DESPACHADO'
            && $reserva->envioVenta->created_at->lt(now()->subDays(3));

        return $estancado ? 'ESTANCADO' : null;
    }

    public function getDetalle(int $id)
    {
        $reserva = ReservaWeb::with('detalle.producto', 'detalle.traslados.traslado', 'sedeRecojo', 'envioVenta')->findOrFail($id);

        $colores      = DB::table('colores')->pluck('descripcion', 'id');
        $tallas       = DB::table('tallas')->pluck('descripcion', 'id');
        $sedePropia   = optional(Almacen::with('sede')->find($reserva->almacen_id)->sede ?? null)->nombre;

        $documentoNumero = null;
        if ($reserva->documento_id) {
            $documento = DB::table('cotizacion_documento')->where('id', $reserva->documento_id)->first();
            $documentoNumero = $documento ? "{$documento->serie}-{$documento->correlativo}" : null;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'reserva' => $reserva,
                'sede_recojo_nombre' => optional($reserva->sedeRecojo)->nombre,
                'documento_numero' => $documentoNumero,
                'despacho_estado' => $this->calcularDespachoEstado($reserva),
                'envio_venta' => $reserva->envioVenta ? [
                    'estado'               => $reserva->envioVenta->estado,
                    'empresa_envio_nombre' => $reserva->envioVenta->empresa_envio_nombre,
                ] : null,
                'detalle' => $reserva->detalle->map(function ($d) use ($colores, $tallas, $sedePropia) {
                    $cubiertoAhora = $d->cantidad - $d->cantidad_pendiente;

                    return [
                        'id'                 => $d->id,
                        'producto_nombre'    => optional($d->producto)->nombre,
                        'color_nombre'       => $colores[$d->color_id] ?? $d->color_id,
                        'talla_nombre'       => $tallas[$d->talla_id] ?? $d->talla_id,
                        'cantidad'           => $d->cantidad,
                        'cantidad_pendiente' => $d->cantidad_pendiente,
                        'precio_venta_1'     => $d->precio_venta_1,
                        // Origen del stock: lo ya disponible en la sede de la
                        // reserva (directo o ya recibido por traslado) + los
                        // traslados en curso que van a cubrir el resto.
                        'origen_directo'     => ['sede' => $sedePropia, 'cantidad' => $cubiertoAhora],
                        'origen_traslados'   => $d->traslados->map(fn ($t) => [
                            'codigo'   => 'TR-' . $t->traslado_id,
                            'sede'     => $t->almacen_nombre,
                            'cantidad' => $t->cantidad,
                            'estado'   => optional($t->traslado)->estado,
                        ]),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Sedes candidatas para cubrir un faltante por traslado: cualquier
     * almacén PRINCIPAL activo distinto al de la reserva. El staff elige
     * cuál usar — no hay sugerencia automática (decisión de negocio,
     * plan-recojo-tienda.md §5).
     */
    public function getAlmacenesOrigen(int $id)
    {
        $reserva = ReservaWeb::findOrFail($id);

        $almacenes = Almacen::with('sede')
            ->where('tipo_almacen', 'PRINCIPAL')
            ->where('estado', 'ACTIVO')
            ->where('id', '!=', $reserva->almacen_id)
            ->get()
            ->map(fn ($a) => [
                'id'     => $a->id,
                'nombre' => optional($a->sede)->nombre ?? $a->descripcion,
            ]);

        return response()->json(['success' => true, 'data' => $almacenes]);
    }

    /**
     * El stock NO se vuelve a tocar acá — ya se descontó al crear la
     * reserva. Confirmar solo cambia el estado; la conversión a documento
     * de venta/despacho real queda fuera de esta pasada (el flujo normal
     * de despachos ya existente se dispara aparte, manualmente, con esta
     * reserva como referencia).
     *
     * Bloqueada mientras haya `cantidad_pendiente` sin cubrir: no tiene
     * sentido confirmar un pedido que físicamente no está completo en la
     * sede del cliente todavía (plan-recojo-tienda.md §2.3).
     */
    /**
     * Genera el comprobante (y, para recojo en tienda, el despacho) vía
     * VentaService::storeFromEcommerce() dentro de la misma transacción
     * que marca CONFIRMADO — si algo falla (ej. usuario sin caja
     * abierta, RUC inválido), todo se revierte y la reserva sigue
     * PENDIENTE, sin perder nada (docs/PLANIFICATIONS/2026-07-15-plan-despacho-web-auto.md §4).
     */
    public function confirmar(Request $request, int $id)
    {
        $reserva = ReservaWeb::with('detalle')->findOrFail($id);

        if ($reserva->estado !== 'PENDIENTE') {
            return response()->json(['success' => false, 'message' => 'Esta reserva ya fue resuelta.'], 409);
        }

        if ($reserva->detalle->sum('cantidad_pendiente') > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cubre el stock faltante (traslado) antes de confirmar.',
            ], 409);
        }

        $modo = optional(Configuracion::where('slug', 'RWM')->first())->propiedad ?: 'PRODUCCION';

        try {
            $documento = DB::transaction(function () use ($reserva, $modo) {
                $documento = (new VentaService())->storeFromEcommerce($reserva, $modo);

                $reserva->update([
                    'estado'              => 'CONFIRMADO',
                    'fecha_resolucion'    => now(),
                    'usuario_id'          => auth()->id(),
                    'documento_id'        => $documento->id,
                    'comprobante_numero'  => "{$documento->serie}-{$documento->correlativo}",
                    'modo_confirmacion'   => $modo,
                ]);

                return $documento;
            });
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo generar el comprobante: ' . $th->getMessage(),
            ], 500);
        }

        // Fase 2 §2.1 (docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md):
        // el comprobante ya está committeado. Encolado (docs 2026-07-16) —
        // Mail::send() es síncrono y tardaba varios segundos bloqueando la
        // respuesta al staff; ahora responde al toque y el correo lo manda
        // queue:work en segundo plano. Un fallo acá no revierte la reserva
        // ya confirmada, solo queda logueado — el staff puede reenviar a
        // mano con el botón "Reenviar comprobante".
        EnviarComprobanteEmailJob::dispatch($documento->id, $reserva->cliente_email, $reserva->codigo_pedido_ecommerce);

        ReservaWebResueltaEvent::dispatch($reserva->codigo_pedido_ecommerce, 'CONFIRMADO');

        return response()->json(['success' => true, 'message' => 'Reserva confirmada, comprobante generado.']);
    }

    /**
     * Devuelve stock y stock_logico en la misma transacción — inverso
     * exacto del descuento que hizo Api\ReservasWeb\ReservaWebController::store().
     * Solo se devuelve lo que realmente se descontó (`cantidad -
     * cantidad_pendiente`) — la porción pendiente nunca se llegó a
     * descontar, devolverla también sería inflar el stock.
     */
    public function anular(Request $request, int $id)
    {
        $data = $request->validate([
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $reserva = ReservaWeb::with('detalle')->findOrFail($id);

        if ($reserva->estado !== 'PENDIENTE') {
            return response()->json(['success' => false, 'message' => 'Esta reserva ya fue resuelta.'], 409);
        }

        try {
            DB::transaction(function () use ($reserva, $data) {
                foreach ($reserva->detalle as $linea) {
                    $aDevolver = $linea->cantidad - $linea->cantidad_pendiente;
                    if ($aDevolver <= 0) {
                        continue;
                    }

                    DB::table('producto_color_tallas')
                        ->where('producto_id', $linea->producto_id)
                        ->where('color_id', $linea->color_id)
                        ->where('talla_id', $linea->talla_id)
                        ->where('almacen_id', $reserva->almacen_id)
                        ->increment('stock', $aDevolver);
                    DB::table('producto_color_tallas')
                        ->where('producto_id', $linea->producto_id)
                        ->where('color_id', $linea->color_id)
                        ->where('talla_id', $linea->talla_id)
                        ->where('almacen_id', $reserva->almacen_id)
                        ->increment('stock_logico', $aDevolver);
                }

                $reserva->update([
                    'estado'           => 'ANULADO',
                    'fecha_resolucion' => now(),
                    'usuario_id'       => auth()->id(),
                    'motivo_anulacion' => $data['motivo'] ?? null,
                ]);
            });

            ReservaWebResueltaEvent::dispatch($reserva->codigo_pedido_ecommerce, 'ANULADO', $data['motivo'] ?? null);

            return response()->json(['success' => true, 'message' => 'Reserva anulada, stock devuelto.']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Crea un traslado directo `almacen_origen` (elegido a mano por el
     * staff) → almacén de la reserva, por el faltante de cada línea con
     * `cantidad_pendiente > 0`. El traslado queda PENDIENTE igual que
     * cualquier otro — se recibe con el flujo ya existente de
     * Almacenes\SolicitudTrasladoController::confirmarStore(), que es
     * donde se aplica el ingreso contra la línea pendiente (ver ese
     * archivo). No se automatiza la elección de sede origen.
     */
    public function cubrirStock(Request $request, int $id)
    {
        $data = $request->validate([
            'almacen_origen_id' => ['required', 'integer', 'exists:almacenes,id'],
            'observacion'       => ['nullable', 'string', 'max:255'],
        ]);

        $reserva = ReservaWeb::with('detalle')->findOrFail($id);

        if ($reserva->estado !== 'PENDIENTE') {
            return response()->json(['success' => false, 'message' => 'Esta reserva ya fue resuelta.'], 409);
        }

        $pendientes = $reserva->detalle->where('cantidad_pendiente', '>', 0);
        if ($pendientes->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Esta reserva no tiene stock pendiente por cubrir.'], 422);
        }

        if ((int) $data['almacen_origen_id'] === (int) $reserva->almacen_id) {
            return response()->json(['success' => false, 'message' => 'El almacén origen no puede ser el mismo de la reserva.'], 422);
        }

        try {
            $traslado = DB::transaction(function () use ($reserva, $data, $pendientes) {
                $almacenOrigen  = Almacen::findOrFail($data['almacen_origen_id']);
                $almacenDestino = Almacen::findOrFail($reserva->almacen_id);

                $traslado                     = new Traslado();
                $traslado->almacen_origen_id  = $almacenOrigen->id;
                $traslado->almacen_destino_id = $almacenDestino->id;
                $traslado->observacion        = $data['observacion'] ?? "Cubrir stock reserva web {$reserva->codigo_pedido_ecommerce}";
                $traslado->sede_origen_id     = $almacenOrigen->sede_id;
                $traslado->sede_destino_id    = $almacenDestino->sede_id;
                $traslado->fecha_traslado     = Carbon::today()->toDateString();
                $traslado->registrador_id     = Auth::id();
                $traslado->registrador_nombre = optional(Auth::user())->usuario ?? optional(Auth::user())->name;
                $traslado->estado             = 'PENDIENTE';
                $traslado->save();

                foreach ($pendientes as $linea) {
                    $filaOrigen = DB::table('producto_color_tallas')
                        ->where('producto_id', $linea->producto_id)
                        ->where('color_id', $linea->color_id)
                        ->where('talla_id', $linea->talla_id)
                        ->where('almacen_id', $almacenOrigen->id)
                        ->lockForUpdate()
                        ->first();

                    $disponible = $filaOrigen->stock_logico ?? 0;
                    $cantidad   = min($disponible, $linea->cantidad_pendiente);

                    if ($cantidad <= 0) {
                        continue;
                    }

                    $nombres = DB::table('productos')->where('id', $linea->producto_id)->value('nombre');
                    $colorNombre = DB::table('colores')->where('id', $linea->color_id)->value('descripcion');
                    $tallaNombre = DB::table('tallas')->where('id', $linea->talla_id)->value('descripcion');

                    TrasladoDetalle::create([
                        'traslado_id'             => $traslado->id,
                        'almacen_id'              => $almacenOrigen->id,
                        'producto_id'             => $linea->producto_id,
                        'color_id'                => $linea->color_id,
                        'talla_id'                => $linea->talla_id,
                        'almacen_nombre'          => $almacenOrigen->descripcion,
                        'producto_nombre'         => $nombres,
                        'color_nombre'            => $colorNombre,
                        'talla_nombre'            => $tallaNombre,
                        'cantidad'                => $cantidad,
                        'reserva_web_detalle_id'  => $linea->id,
                    ]);

                    DB::table('producto_color_tallas')
                        ->where('producto_id', $linea->producto_id)
                        ->where('color_id', $linea->color_id)
                        ->where('talla_id', $linea->talla_id)
                        ->where('almacen_id', $almacenOrigen->id)
                        ->decrement('stock', $cantidad);
                    DB::table('producto_color_tallas')
                        ->where('producto_id', $linea->producto_id)
                        ->where('color_id', $linea->color_id)
                        ->where('talla_id', $linea->talla_id)
                        ->where('almacen_id', $almacenOrigen->id)
                        ->decrement('stock_logico', $cantidad);

                    $stockRestante = DB::table('producto_color_tallas')
                        ->where('producto_id', $linea->producto_id)
                        ->where('color_id', $linea->color_id)
                        ->where('talla_id', $linea->talla_id)
                        ->where('almacen_id', $almacenOrigen->id)
                        ->value('stock');

                    Kardex::create([
                        'sede_id'            => $almacenOrigen->sede_id,
                        'almacen_id'         => $almacenOrigen->id,
                        'producto_id'        => $linea->producto_id,
                        'color_id'           => $linea->color_id,
                        'talla_id'           => $linea->talla_id,
                        'almacen_nombre'     => $almacenOrigen->descripcion,
                        'producto_nombre'    => $nombres,
                        'color_nombre'       => $colorNombre,
                        'talla_nombre'       => $tallaNombre,
                        'cantidad'           => $cantidad,
                        'stock'              => $stockRestante,
                        'accion'             => 'TRASLADO SALIDA',
                        'numero_doc'         => 'TR-' . $traslado->id,
                        'documento_id'       => $traslado->id,
                        'registrador_id'     => Auth::id(),
                        'registrador_nombre' => $traslado->registrador_nombre,
                        'fecha'              => Carbon::today()->toDateString(),
                        'descripcion'        => 'TRASLADO SALIDA (CUBRIR RESERVA WEB)',
                    ]);
                }

                return $traslado;
            });

            return response()->json([
                'success' => true,
                'message' => "Traslado TR-{$traslado->id} creado. Se aplica el faltante cuando el destino lo reciba (Almacenes > Traslados).",
                'data' => ['traslado_id' => $traslado->id],
            ]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Deshace por completo una reserva ya CONFIRMADO: borra el despacho,
     * el documento (venta) y la propia reserva, devolviendo el stock que
     * de verdad se descontó (docs 2026-07-16). Pensado sobre todo para
     * limpiar confirmaciones de prueba (modo DEMO), pero funciona para
     * cualquiera SIEMPRE que el documento no haya sido enviado a SUNAT —
     * si ya se envió, borrar la fila local dejaría a SUNAT con un
     * comprobante fantasma sin respaldo, así que se bloquea y hay que
     * usar el flujo fiscal normal (nota de crédito) en ese caso.
     */
    public function eliminar(int $id)
    {
        $reserva = ReservaWeb::with('detalle')->findOrFail($id);

        if ($reserva->estado !== 'CONFIRMADO') {
            return response()->json(['success' => false, 'message' => 'Solo se puede eliminar una reserva ya confirmada.'], 409);
        }

        $documento = $reserva->documento_id ? Documento::find($reserva->documento_id) : null;

        if ($documento && $documento->sunat == '1') {
            return response()->json([
                'success' => false,
                'message' => 'Este comprobante ya fue enviado a SUNAT — no se puede eliminar acá. Usa el flujo fiscal normal (nota de crédito/anulación).',
            ], 409);
        }

        try {
            DB::transaction(function () use ($reserva, $documento) {
                if ($documento) {
                    // Todas las tablas con FK real hacia cotizacion_documento
                    // que este flujo (VentaService::store + asociarVentaCaja +
                    // registrarDesdeVenta + crearEnvioRecojoTienda) sí puede
                    // llegar a poblar — verificado contra information_schema.
                    // Las demás (guias_remision, nota_electronica,
                    // retenciones, error_venta, pedidos_atenciones,
                    // cuenta_cliente) son de otros flujos (SUNAT guías,
                    // conversión de pedidos) que este camino nunca toca.
                    EnvioVenta::where('documento_id', $documento->id)->delete();
                    DB::table('kardex_cuentas')->where('venta_id', $documento->id)->delete();
                    DB::table('detalle_movimiento_venta')->where('cdocumento_id', $documento->id)->delete();
                    DB::table('recibos_caja_detalle')->where('documento_id', $documento->id)->delete();
                    Detalle::where('documento_id', $documento->id)->delete();
                    $documento->delete();
                }

                // Devuelve solo lo que realmente se descontó — misma lógica
                // que anular() — al almacén de la reserva (mismo almacén
                // donde se descontó al crearla y donde se re-descontó
                // cualquier traslado de "Cubrir stock" ya recibido).
                foreach ($reserva->detalle as $linea) {
                    $aDevolver = $linea->cantidad - $linea->cantidad_pendiente;
                    if ($aDevolver <= 0) {
                        continue;
                    }

                    DB::table('producto_color_tallas')
                        ->where('producto_id', $linea->producto_id)
                        ->where('color_id', $linea->color_id)
                        ->where('talla_id', $linea->talla_id)
                        ->where('almacen_id', $reserva->almacen_id)
                        ->increment('stock', $aDevolver);
                    DB::table('producto_color_tallas')
                        ->where('producto_id', $linea->producto_id)
                        ->where('color_id', $linea->color_id)
                        ->where('talla_id', $linea->talla_id)
                        ->where('almacen_id', $reserva->almacen_id)
                        ->increment('stock_logico', $aDevolver);
                }

                $codigoPedido = $reserva->codigo_pedido_ecommerce;
                $reserva->delete();

                ReservaWebResueltaEvent::dispatch($codigoPedido, 'ANULADO', 'Eliminada por staff (reserva ya confirmada)');
            });

            return response()->json(['success' => true, 'message' => 'Reserva, venta y despacho eliminados. Stock devuelto.']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
