<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Sedes\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompanyController extends Controller
{
    /**
     * Usado por ecommerceMerris para el selector de "recojo en tienda"
     * del checkout (routes/api.php `company/sedes`). Recojo en tienda
     * (docs/PLANIFICATIONS/2026-07-15-plan-recojo-tienda.md §2.1/§5):
     * solo se listan sedes con un almacén PRINCIPAL activo — es el que
     * usa la reserva para descontar stock (Api\ReservasWeb\
     * ReservaWebController::store()). Una sede sin almacén PRINCIPAL
     * (ej. un stand sin bodega propia) no puede recibir una reserva, así
     * que no tiene sentido ofrecerla como punto de recojo.
     */
    public function getCompanyLocations()
    {
        try {

            $sedes = Sede::where('estado', 'ACTIVO')
                ->whereHas('almacenes', function ($q) {
                    $q->where('tipo_almacen', 'PRINCIPAL')->where('estado', 'ACTIVO');
                })
                ->get()
                ->map(function ($sede) {

                    return [
                        'id' => $sede->id,
                        'nombre' => $sede->nombre,
                        'razon_social' => $sede->razon_social,
                        'ruc' => $sede->ruc,
                        'direccion' => $sede->direccion,
                        'urbanizacion' => $sede->urbanizacion,
                        'telefono' => $sede->telefono,
                        'correo' => $sede->correo,

                        'departamento' => $sede->departamento_nombre,
                        'provincia' => $sede->provincia_nombre,
                        'distrito' => $sede->distrito_nombre,

                        'tipo_sede' => $sede->tipo_sede,
                        'codigo_local' => $sede->codigo_local,

                        'logo_nombre' => $sede->logo_nombre,
                        'logo_ruta' => $sede->logo_ruta,

                        'logo_url' => $sede->logo_ruta
                            ? asset('storage/' . $sede->logo_ruta)
                            : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'SEDES OBTENIDAS',
                'data' => $sedes
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ], 500);
        }
    }

    /**
     * Fase 4.4 (docs/PLANIFICATIONS/2026-07-15-plan-pendientes.md): a
     * diferencia de getTallasByColor() (Fase 1.2, suma toda la red para la
     * vitrina general), esto es por sede puntual — para cada sede con
     * almacén PRINCIPAL activo, ¿tiene stock suficiente para CADA línea
     * del carrito? Reemplaza el texto fijo "Listo en 1 a 8 días hábiles"
     * del selector de recojo en tienda del checkout.
     */
    public function stockPorSede(Request $request)
    {
        $data = $request->validate([
            'items'                => ['required', 'array', 'min:1'],
            'items.*.producto_id'  => ['required', 'integer'],
            'items.*.color_id'     => ['required', 'integer'],
            'items.*.talla_id'     => ['required', 'integer'],
            'items.*.cantidad'     => ['required', 'integer', 'min:1'],
        ]);

        try {
            $sedes = Sede::where('estado', 'ACTIVO')
                ->whereHas('almacenes', function ($q) {
                    $q->where('tipo_almacen', 'PRINCIPAL')->where('estado', 'ACTIVO');
                })
                ->with(['almacenes' => function ($q) {
                    $q->where('tipo_almacen', 'PRINCIPAL')->where('estado', 'ACTIVO');
                }])
                ->get();

            $almacenIds  = $sedes->pluck('almacenes')->flatten()->pluck('id')->all();
            $productoIds = collect($data['items'])->pluck('producto_id')->unique()->all();
            $colorIds    = collect($data['items'])->pluck('color_id')->unique()->all();
            $tallaIds    = collect($data['items'])->pluck('talla_id')->unique()->all();

            $filas = DB::table('producto_color_tallas')
                ->whereIn('almacen_id', $almacenIds)
                ->whereIn('producto_id', $productoIds)
                ->whereIn('color_id', $colorIds)
                ->whereIn('talla_id', $tallaIds)
                ->get(['almacen_id', 'producto_id', 'color_id', 'talla_id', 'stock_logico']);

            $stockPorClave = [];
            foreach ($filas as $fila) {
                $clave = "{$fila->almacen_id}-{$fila->producto_id}-{$fila->color_id}-{$fila->talla_id}";
                $stockPorClave[$clave] = (int) $fila->stock_logico;
            }

            $resultado = $sedes->map(function ($sede) use ($data, $stockPorClave) {
                $almacen    = $sede->almacenes->first();
                $disponible = $almacen !== null;

                if ($almacen) {
                    foreach ($data['items'] as $item) {
                        $clave = "{$almacen->id}-{$item['producto_id']}-{$item['color_id']}-{$item['talla_id']}";
                        if (($stockPorClave[$clave] ?? 0) < $item['cantidad']) {
                            $disponible = false;
                            break;
                        }
                    }
                }

                return [
                    'sede_id'    => $sede->id,
                    'disponible' => $disponible,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $resultado,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ], 500);
        }
    }
}
