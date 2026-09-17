<?php

namespace App\Http\Controllers\Dashboard;

use App\Almacenes\Color;
use App\Almacenes\Talla;
use App\Http\Controllers\Controller;
use App\Http\Services\Dashboard\DashboardManager;
use App\Mantenimiento\Sedes\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends Controller
{
    private DashboardManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new DashboardManager();
    }

    public function index()
    {
        $sedes  =   Sede::where('estado', 'ACTIVO')->get();
        $colores =   Color::where('estado', 'ACTIVO')->get();
        $tallas     =   Talla::where('estado', 'activo')->get();
        return view('dashboard.index', compact('sedes', 'colores', 'tallas'));
    }

    /*
array:3 [
  "year" => "2026"
  "month" => "1"
  "sede" => "1"
]
*/
    public function getData(Request $request)
    {
        try {
            $data   =   $this->s_manager->getData($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getData');
        }
    }

    public function getSales(Request $request)
    {
        try {
            $data   =   $this->s_manager->getSales($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getSales');
        }
    }

    public function getSalesOrigin(Request $request)
    {
        try {
            $data   =   $this->s_manager->getSalesOrigin($request->toArray());

            // disponible = false cuando el origen de venta no se registra: el
            // widget avisa en vez de pintar una tarta vacía, que se leería como
            // "no hubo ventas".
            return response()->json([
                'success'    => true,
                'message'    => 'Datos obtenidos',
                'data'       => $data,
                'disponible' => $this->s_manager->origenVentaDisponible(),
            ]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getSalesOrigin');
        }
    }

    public function getTopProducts(Request $request)
    {
        try {
            $data   =   $this->s_manager->getDataTopProducts($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getTopProducts');
        }
    }

    public function getConversionRate(Request $request)
    {
        try {
            $data   =   $this->s_manager->getConversionRate($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getConversionRate');
        }
    }

    public function getParesYearMonth(Request $request)
    {
        try {
            $data   =   $this->s_manager->getParesYearMonth($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getParesYearMonth');
        }
    }

    public function getSalesColor(Request $request)
    {
        try {
            $data   =   $this->s_manager->getSalesColor($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getSalesColor');
        }
    }

    public function getCustomersActives(Request $request)
    {
        try {
            $data   =   $this->s_manager->getCustomersActives($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getCustomersActives');
        }
    }

    public function getSalesSizes(Request $request)
    {
        try {
            $data   =   $this->s_manager->getSalesSizes($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getSalesSizes');
        }
    }

    public function getDeliveryTime(Request $request)
    {
        try {
            $data   =   $this->s_manager->getDeliveryTime($request->toArray());

            // disponible = false mientras no existan paquetes_embalados_detalle
            // y repartos_detalle: sin ellas no hay tiempo de entrega que medir.
            return response()->json([
                'success'    => true,
                'message'    => 'Datos obtenidos',
                'data'       => $data,
                'disponible' => $this->s_manager->tiempoEntregaDisponible(),
            ]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getDeliveryTime');
        }
    }

    public function getRankingVendedores(Request $request)
    {
        try {
            $data   =   $this->s_manager->getRankingVendedores($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return $this->errorJson($th, 'getRankingVendedores');
        }
    }

    /**
     * Respuesta de error de los widgets.
     *
     * Nada de getMessage(), line ni file en el JSON: devolvían al navegador el
     * SQL completo y la ruta absoluta del servidor, con APP_DEBUG o sin él.
     * El detalle va al log en una sola línea, sin traza: laravel.log ya crece
     * 300-400 KB al día en este sistema.
     */
    private function errorJson(Throwable $th, string $origen)
    {
        // El mensaje de una QueryException trae el SQL con saltos de línea: sin
        // aplanarlo, una sola incidencia ocupa cinco líneas del log y se pierde
        // al filtrar con grep.
        $detalle = trim(preg_replace('/\s+/', ' ', $th->getMessage()));

        Log::error('dashboard.' . $origen . ': ' . $detalle
            . ' [' . $th->getFile() . ':' . $th->getLine() . ']');

        return response()->json([
            'success' => false,
            'message' => 'No se pudieron obtener los datos.',
        ]);
    }
}
