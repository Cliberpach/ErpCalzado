<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Persona\Persona;
use App\Pos\Caja;
use App\Pos\MovimientoCaja;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;

class CajaController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'caja.index');

        return view('pos.Cajas.index');
    }
    public function getCajas()
    {
        $datos = [];
        $cajas = Caja::where('estado', 'ACTIVO')->get();
        foreach ($cajas as $key => $value) {
            array_push($datos, [
                'id' => $value->id,
                'nombre' => $value->nombre,
                'created_at' => Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $value->created_at
                )->format('Y-m-d h:i:s'),
            ]);
        }
        return DataTables::of($datos)->toJson();
    }
    public function store(Request $request)
    {
        $this->authorize('haveaccess', 'caja.index');
        $caja = new Caja();
        $caja->nombre = $request->nombre;
        $caja->save();
        return redirect()->route('Caja.index');
    }
    public function update(Request $request, $id)
    {
        $caja = Caja::findOrFail($id);
        $caja->nombre = $request->nombre_editar;
        $caja->save();
        return redirect()->route('Caja.index');
    }
    public function destroy($id)
    {
        $caja = Caja::findOrFail($id);
        $caja->estado = 'ANULADO';
        $caja->save();
        return redirect()->route('Caja.index');
    }
    public function indexMovimiento()
    {
        $this->authorize('haveaccess', 'movimiento_caja.index');
        $lstAniosDB = DB::table('lote_productos')
            ->select(DB::raw('year(created_at) as value'))
            ->distinct()
            ->orderBy('value', 'desc')
            ->get();
        $lstAnios = collect();
        foreach($lstAniosDB as $item){
            $lstAnios->push([
                "value" =>$item->value
            ]);
        }
        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy, 'm');
        $anio_ = date_format($fecha_hoy, 'Y');
        $search = array_search("$anio_",array_column($lstAnios->values()->all(),"value"));
        if($search === false){
            $lstAnios->push([
                "value" =>(int)$anio_
            ]);
        }
        
        return view('pos.MovimientoCaja.indexMovimiento',[
            'lstAnios'=>json_decode(json_encode($lstAnios->sortByDesc("value")->values())), 
            'mes'=>$mes, 
            'anio_'=>$anio_
        ]);
    }
    public function getMovimientosCajas(Request $request)
    {
        $datos = [];
        $consulta = null;
        if ($request->filter=="ACTIVO") {
            $consulta = MovimientoCaja::where(DB::raw("CONVERT(fecha_apertura,date)"),">=",$request->desde)
            ->where(DB::raw("CONVERT(fecha_apertura,date)"),"<=",$request->hasta);
        } else {
            $consulta = MovimientoCaja::whereMonth(
                'fecha_apertura',
                $request->mes
            )->whereYear('fecha_apertura', $request->anio);
        }
        $movimientos = $consulta
            ->where('estado', 'ACTIVO')
            ->get([
                'id',
                'caja_id',
                'colaborador_id',
                'monto_inicial',
                'monto_final',
                'fecha_apertura',
                'fecha_cierre',
                'estado_movimiento',
                'fecha',
                'estado',
            ]);
        foreach ($movimientos as $key => $movimiento) {
            array_push($datos, [
                'id' => $movimiento->id,
                'caja' => $movimiento->caja->nombre,
                'cantidad_inicial' => $movimiento->monto_inicial,
                'cantidad_final' =>
                    $movimiento->monto_final == null
                        ? '-'
                        : $movimiento->monto_final,
                'fecha_Inicio' => $movimiento->fecha_apertura,
                'fecha_Cierre' =>
                    $movimiento->fecha_cierre == null
                        ? '-'
                        : $movimiento->fecha_cierre,
                'totales' => $this->ObtenerTotales($movimiento->id),
            ]);
        }
        return DataTables::of($datos)->toJson();
    }

    public function estadoCaja(Request $request)
    {
        $caja = Caja::findOrFail($request->id);
        return $caja->estado_caja == 'ABIERTA' ? 'true' : 'false';
    }

    public function aperturaCaja(Request $request)
    {
        $movimiento = new MovimientoCaja();
        $movimiento->caja_id = $request->caja;
        $movimiento->colaborador_id = $request->colaborador_id;
        $movimiento->monto_inicial = $request->saldo_inicial;
        $movimiento->estado_movimiento = 'APERTURA';
        $movimiento->fecha_apertura = date('Y-m-d h:i:s');
        $movimiento->fecha = date('Y-m-d');
        $movimiento->save();
        $caja = Caja::findOrFail($request->caja);
        $caja->estado_caja = 'ABIERTA';
        $caja->save();
        return redirect()->route('Caja.Movimiento.index');
    }

    public function cerrarCaja(Request $request)
    {
        $movimiento = MovimientoCaja::findOrFail($request->movimiento_id);
        $movimiento->estado_movimiento = 'CIERRE';
        $movimiento->fecha_cierre = date('Y-m-d h:i:s');
        $movimiento->monto_final = (float) $request->saldo;
        $movimiento->save();
        $caja = $movimiento->caja;
        $caja->estado_caja = 'CERRADA';
        $caja->save();
        return redirect()->route('Caja.Movimiento.index');
    }

    public function cajaDatosCierre(Request $request)
    {
        $movimiento = MovimientoCaja::findOrFail($request->id);
        $colaborador = $movimiento->colaborador;
        $ingresos =
            cuadreMovimientoCajaIngresosVentaResum($movimiento, 1) -
            cuadreMovimientoDevolucionesResum($movimiento, 1) +
            cuadreMovimientoCajaIngresosCobranzaResum($movimiento, 1);
        $egresos =
            cuadreMovimientoCajaEgresosEgresoResum($movimiento, 1) -
            cuadreMovimientoDevolucionesResum($movimiento, 1) +
            cuadreMovimientoCajaEgresosPagoResum($movimiento, 1);
        return [
            'caja' => $movimiento->caja->nombre,
            'monto_inicial' => $movimiento->monto_inicial,
            'colaborador' =>
                $colaborador->persona->apellido_paterno .
                ' ' .
                $colaborador->persona->apellido_paterno .
                ' ' .
                $colaborador->persona->nombre,
            'egresos' => $egresos,
            'ingresos' => $ingresos,
            'saldo' => $movimiento->monto_inicial + $ingresos - $egresos,
        ];
    }

    public function verificarEstadoUser()
    {
        try {
            if (
                MovimientoCaja::where(
                    'estado_movimiento',
                    'APERTURA'
                )->count() != 0
            ) {
                if (FullAccess() || PuntoVenta()) {
                    return response()->json([
                        'success' => true,
                    ]);
                } else {
                    if (Auth::user()->user->persona->colaborador) {
                        if (
                            MovimientoCaja::where(
                                'colaborador_id',
                                Auth::user()->user->persona->colaborador->id
                            )
                                ->where('estado_movimiento', 'APERTURA')
                                ->count() != 0
                        ) {
                            return response()->json([
                                'success' => true,
                            ]);
                        } else {
                            return response()->json([
                                'success' => false,
                                'mensaje' =>
                                    'No tienes ninguna apertura de caja disponible',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'mensaje' =>
                                'No eres un colaborador por favor registrarte como colaborador',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No hay ninguna apertura de caja disponible',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ]);
        }
    }

    public function reporteMovimiento($id)
    {
        $movimiento = MovimientoCaja::findOrFail($id);
        $empresa = Empresa::first();
        $fecha = Carbon::now()->toDateString();

        $pdf = PDF::loadview('pos.MovimientoCaja.Reportes.movimientocaja', [
            'movimiento' => $movimiento,
            'empresa' => $empresa,
            'fecha' => $fecha,
        ])
            ->setPaper('a4')
            ->setWarnings(false);
        return $pdf->stream();
    }
    private function ObtenerTotales($id)
    {
        $movimiento = MovimientoCaja::findOrFail($id);
        $TotalVentaDelDia =
            (float) cuadreMovimientoCajaIngresosVenta($movimiento) -
            (float) cuadreMovimientoDevoluciones($movimiento);
        return [
            'TotalVentaDelDia' => $TotalVentaDelDia,
        ];
    }
}
