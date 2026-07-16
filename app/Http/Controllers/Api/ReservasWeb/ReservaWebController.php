<?php

namespace App\Http\Controllers\Api\ReservasWeb;

use App\Almacenes\Almacen;
use App\Events\ReservaWebCreadaEvent;
use App\Http\Controllers\Controller;
use App\Models\Ventas\ReservaWeb\ReservaWeb;
use App\Models\Ventas\ReservaWeb\ReservaWebDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Carrito Fase F (docs/PLANIFICATIONS/2026-07-11-carrito-plan.md §7): al
 * confirmar checkout, ecommerceMerris llama a store() acá — crea la
 * reserva y separa stock (stock + stock_logico) en la MISMA transacción,
 * con lockForUpdate() por línea para que una venta física simultánea no
 * pueda pisarse con esto (§7.3 del plan). Confirmar/Anular vive en el
 * mantenedor (Pedidos\ReservaWebController).
 *
 * Recojo en tienda (docs/PLANIFICATIONS/2026-07-15-plan-recojo-tienda.md):
 * cuando el cliente elige recojo, `sede_recojo_id` manda sobre
 * `almacen_id` — la reserva SIEMPRE descuenta del almacén PRINCIPAL de
 * la sede elegida por el cliente, nunca de un almacén fijo. Si esa sede
 * no alcanza, la reserva se crea IGUAL: se descuenta solo hasta 0 (stock
 * y stock_logico son unsignedBigInteger, no admiten negativos) y el
 * faltante queda en `ReservaWebDetalle.cantidad_pendiente`, para que el
 * staff lo cubra por traslado antes de poder Confirmar (ver
 * Pedidos\ReservaWebController::confirmar()/cubrirStock()).
 */
class ReservaWebController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_pedido_ecommerce'  => ['required', 'string', 'max:255'],
            'cliente_nombre'           => ['required', 'string', 'max:255'],
            'cliente_email'            => ['required', 'string', 'max:255'],
            'cliente_telefono'         => ['nullable', 'string', 'max:50'],
            'cliente_direccion'        => ['nullable', 'string'],
            'doc_tipo'                 => ['nullable', 'string', 'max:10'],
            'doc_numero'               => ['nullable', 'string', 'max:20'],
            'desea_factura'            => ['nullable', 'boolean'],
            'razon_social'             => ['nullable', 'string', 'max:255'],
            'ruc'                      => ['nullable', 'string', 'max:11'],
            'almacen_id'               => ['nullable', 'integer'],
            'sede_recojo_id'           => ['nullable', 'integer'],
            'metodo_pago'              => ['nullable', 'string', 'max:50'],
            'pago_titular'             => ['nullable', 'string', 'max:150'],
            'pago_tarjeta_last4'       => ['nullable', 'digits:4'],
            'pago_banco'               => ['nullable', 'string', 'max:100'],
            'pago_cuotas'              => ['nullable', 'string', 'max:10'],
            'pago_referencia'          => ['nullable', 'string', 'max:100'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.producto_id'      => ['required', 'integer'],
            'items.*.color_id'         => ['required', 'integer'],
            'items.*.talla_id'         => ['required', 'integer'],
            'items.*.cantidad'         => ['required', 'integer', 'min:1'],
            'items.*.precio_venta_1'   => ['required', 'numeric', 'min:0'],
        ]);

        if (ReservaWeb::where('codigo_pedido_ecommerce', $data['codigo_pedido_ecommerce'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una reserva para este pedido.',
            ], 409);
        }

        $sedeRecojoId = $data['sede_recojo_id'] ?? null;
        $almacenId    = $data['almacen_id'] ?? 1;

        if ($sedeRecojoId) {
            $almacenSede = Almacen::where('sede_id', $sedeRecojoId)
                ->where('tipo_almacen', 'PRINCIPAL')
                ->where('estado', 'ACTIVO')
                ->first();

            if (!$almacenSede) {
                return response()->json([
                    'success' => false,
                    'message' => 'La sede de recojo elegida no tiene un almacén habilitado.',
                ], 422);
            }

            $almacenId = $almacenSede->id;
        }

        try {
            $reserva = DB::transaction(function () use ($data, $almacenId, $sedeRecojoId) {
                $total = collect($data['items'])->sum(fn ($i) => $i['precio_venta_1'] * $i['cantidad']);

                $reserva = ReservaWeb::create([
                    'codigo_pedido_ecommerce' => $data['codigo_pedido_ecommerce'],
                    'cliente_nombre'          => $data['cliente_nombre'],
                    'cliente_email'           => $data['cliente_email'],
                    'cliente_telefono'        => $data['cliente_telefono'] ?? null,
                    'cliente_direccion'       => $data['cliente_direccion'] ?? null,
                    'doc_tipo'                => $data['doc_tipo'] ?? null,
                    'doc_numero'              => $data['doc_numero'] ?? null,
                    'desea_factura'           => $data['desea_factura'] ?? false,
                    'razon_social'            => $data['razon_social'] ?? null,
                    'ruc'                     => $data['ruc'] ?? null,
                    'almacen_id'              => $almacenId,
                    'sede_recojo_id'          => $sedeRecojoId,
                    'total'                   => $total,
                    'metodo_pago'             => $data['metodo_pago'] ?? null,
                    'pago_titular'            => $data['pago_titular'] ?? null,
                    'pago_tarjeta_last4'      => $data['pago_tarjeta_last4'] ?? null,
                    'pago_banco'              => $data['pago_banco'] ?? null,
                    'pago_cuotas'             => $data['pago_cuotas'] ?? null,
                    'pago_referencia'         => $data['pago_referencia'] ?? null,
                    'estado'                  => 'PENDIENTE',
                    'fecha_reserva'           => now(),
                ]);

                // lockForUpdate por línea, en el mismo orden en que llegan —
                // evita deadlocks si dos reservas comparten producto/color/talla
                // y llegan casi al mismo tiempo.
                foreach ($data['items'] as $item) {
                    $fila = DB::table('producto_color_tallas')
                        ->where('producto_id', $item['producto_id'])
                        ->where('color_id', $item['color_id'])
                        ->where('talla_id', $item['talla_id'])
                        ->where('almacen_id', $almacenId)
                        ->lockForUpdate()
                        ->first();

                    $stockDisponible = $fila->stock_logico ?? 0;
                    $aCubrirAhora     = min($stockDisponible, $item['cantidad']);
                    $pendiente        = $item['cantidad'] - $aCubrirAhora;

                    ReservaWebDetalle::create([
                        'reserva_web_id'      => $reserva->id,
                        'producto_id'         => $item['producto_id'],
                        'color_id'            => $item['color_id'],
                        'talla_id'            => $item['talla_id'],
                        'cantidad'            => $item['cantidad'],
                        'cantidad_pendiente'  => $pendiente,
                        'precio_venta_1'      => $item['precio_venta_1'],
                    ]);

                    // Descuenta solo lo que hay — nunca por debajo de 0. El
                    // resto (si $pendiente > 0) lo cubre el staff con un
                    // traslado antes de poder Confirmar.
                    if ($aCubrirAhora > 0) {
                        DB::table('producto_color_tallas')
                            ->where('producto_id', $item['producto_id'])
                            ->where('color_id', $item['color_id'])
                            ->where('talla_id', $item['talla_id'])
                            ->where('almacen_id', $almacenId)
                            ->decrement('stock', $aCubrirAhora);
                        DB::table('producto_color_tallas')
                            ->where('producto_id', $item['producto_id'])
                            ->where('color_id', $item['color_id'])
                            ->where('talla_id', $item['talla_id'])
                            ->where('almacen_id', $almacenId)
                            ->decrement('stock_logico', $aCubrirAhora);
                    }
                }

                return $reserva;
            });
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }

        // Fuera del try/catch (mismo patrón que
        // Pedidos\ReservaWebController::confirmar()): la reserva ya está
        // committeada, un fallo acá no debe reportarse como error 500 al
        // cliente ni esconder el reserva_web_id ya creado.
        ReservaWebCreadaEvent::dispatch($reserva->id);

        return response()->json([
            'success' => true,
            'data' => [
                'reserva_web_id'  => $reserva->id,
                'estado'          => $reserva->estado,
                'tiene_pendiente' => $reserva->fresh('detalle')->tiene_pendiente,
            ],
        ]);
    }
}
