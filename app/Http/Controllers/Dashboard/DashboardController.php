<?php

namespace App\Http\Controllers\Dashboard;

use App\Almacenes\Color;
use App\Almacenes\Talla;
use App\Http\Controllers\Controller;
use App\Http\Services\Dashboard\DashboardManager;
use App\Mantenimiento\Sedes\Sede;
use Illuminate\Http\Request;
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
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getSales(Request $request)
    {
        try {
            $data   =   $this->s_manager->getSales($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getSalesOrigin(Request $request)
    {
        try {
            $data   =   $this->s_manager->getSalesOrigin($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getTopProducts(Request $request)
    {
        try {
            $data   =   $this->s_manager->getDataTopProducts($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getConversionRate(Request $request)
    {
        try {
            $data   =   $this->s_manager->getConversionRate($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getParesYearMonth(Request $request)
    {
        try {
            $data   =   $this->s_manager->getParesYearMonth($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getSalesColor(Request $request)
    {
        try {
            $data   =   $this->s_manager->getSalesColor($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getCustomersActives(Request $request)
    {
        try {
            $data   =   $this->s_manager->getCustomersActives($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getSalesSizes(Request $request)
    {
        try {
            $data   =   $this->s_manager->getSalesSizes($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getDeliveryTime(Request $request)
    {
        try {
            $data   =   $this->s_manager->getDeliveryTime($request->toArray());

            return response()->json(['success' => true, 'message' => 'Datos obtenidos', 'data' => $data]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }
}
