<?php

namespace App\Http\Controllers\Pos;

use App\DetallesMovimientoCaja;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\MovimientoCaja\MovimientoCajaAperturaRequest;
use App\Http\Services\Caja\Caja\CajaManager;
use App\Http\Services\Caja\CajaMovimiento\CajaMovimientoManager;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Sedes\Sede;
use App\Pos\Caja;
use App\Pos\MovimientoCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Exception;


class CajaController extends Controller
{
    private CajaMovimientoManager $s_caja;
    private CajaManager $s_cajaManager;

    public function __construct()
    {
        $this->s_caja        = new CajaMovimientoManager();
        $this->s_cajaManager = new CajaManager();
    }

    public function index()
    {
        $this->authorize('haveaccess', 'caja.movimiento_caja.index');

        $sede_id    =   Auth::user()->sede_id;

        return view('pos.Cajas.index', compact('sede_id'));
    }

    public function retirarColaborades(Request $request)
    {
        // return $request;
        $idMovimiento = $request->movimiento;
        $colaboradores = $request->colaboradores;
        $usuarios = DetallesMovimientoCaja::select('*')
            ->where('detalles_movimiento_caja.movimiento_id', '=', $idMovimiento)
            ->whereIn('detalles_movimiento_caja.usuario_id', $colaboradores)
            ->distinct()
            ->get();


        foreach ($usuarios as $u) {

            $colaborador =  DetallesMovimientoCaja::find($u->id);

            $colaborador->fecha_salida = date('Y-m-d h:i:s');
            $colaborador->save();
        }


        return redirect()->route('Caja.Movimiento.index');
    }

    public function getColaborades($id)
    {
        $colaborades_desocupados = DetallesMovimientoCaja::select('*')
            ->join('users as u', 'u.id', '=', 'detalles_movimiento_caja.usuario_id')
            ->where('detalles_movimiento_caja.movimiento_id', '=', $id)
            ->whereNull('detalles_movimiento_caja.fecha_salida')
            ->get();

        return $colaborades_desocupados;
    }
    public function getCajas()
    {
        $datos = [];

        //====== USUARIOS PUEDEN VER CAJAS SOLO DE SU SEDE ======
        $cajas = Caja::where('estado', 'ACTIVO')->where('sede_id', Auth::user()->sede_id)->get();


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


    /*
array:3 [▼
  "_token"      => "uA8DojLYx1bnlHUStJrLNeX1e0TYsd5OkhYgNM2o"
  "sede_id"     => "1"
  "nombre"      => "asdas"
]
*/
    public function store(Request $request)
    {
        try {
            $this->authorize('haveaccess', 'caja.movimiento_caja.index');
            $this->s_cajaManager->store($request->nombre, (int) Auth::user()->sede_id);
            return response()->json(['success' => true, 'message' => 'Caja creada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->s_cajaManager->update((int) $id, $request->nombre);
            return response()->json(['success' => true, 'message' => 'Caja actualizada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $this->s_cajaManager->destroy((int) $id);
            return response()->json(['success' => true, 'message' => 'Caja eliminada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function indexMovimiento()
    {
        $this->authorize('haveaccess', 'caja.movimiento_caja.index');

        $fecha_hoy = Carbon::now();
        $mes   = $fecha_hoy->format('m');
        $anio_ = $fecha_hoy->format('Y');

        $lstAnios = array_map(
            fn($y) => (object)['value' => $y],
            range((int)$anio_, 2020)
        );

        $sede_id = Auth::user()->sede_id;

        $lstCajas = Caja::where('estado', 'ACTIVO')
            ->where('sede_id', $sede_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('pos.MovimientoCaja.indexMovimiento', [
            'lstAnios' => $lstAnios,
            'mes'      => $mes,
            'anio_'    => $anio_,
            'sede_id'  => $sede_id,
            'lstCajas' => $lstCajas,
        ]);
    }

    public function getDatosAperturaCaja(Request $request)
    {
        try {

            $colaborador_id_actual  =   Auth::user()->colaborador_id;
            $sede_id                =   $request->get('sede_id');

            //======= 1 MOVIMIENTO DE CAJA => 1 CAJERO Y VENDEDORES =====
            $cajas_desocupadas  =   Caja::where('estado_caja', 'CERRADA')
                ->where('estado', 'ACTIVO')
                ->where('sede_id', $sede_id)
                ->get();

            //======== CAJEROS DESOCUPADOS DE LA SEDE ======
            $cajeros_desocupados =   DB::select(
                'select
                                        co.id,
                                        co.nombre
                                        from colaboradores as co
                                        inner join users as u on u.colaborador_id = co.id
                                        inner join role_user as ru on ru.user_id = u.id
                                        inner join roles as r on r.id = ru.role_id
                                        where
                                        co.sede_id = ?
                                        AND u.estado = "ACTIVO"
                                        AND co.id  NOT IN
                                        (
                                            select
                                            mc.colaborador_id
                                            from movimiento_caja as mc
                                            where
                                            mc.estado_movimiento = "APERTURA"
                                            AND mc.sede_id = ?
                                        )
                                        AND (r.name LIKE "%CAJER%")
                                        AND co.id = ?',
                [
                    $sede_id,
                    $sede_id,
                    $colaborador_id_actual
                ]
            );



            //======== ID DE VENDEDORES OCUPADOS ======
            $vendedores_desocupados =   DB::select(
                'select
                                            u.colaborador_id,
                                            u.usuario
                                            from colaboradores as co
                                            inner join users as u on u.colaborador_id = co.id
                                            inner join role_user as ru on ru.user_id = u.id
                                            inner join roles as r on r.id = ru.role_id
                                            where
                                            co.sede_id = ?
                                            AND u.colaborador_id  NOT IN
                                            (
                                                select
                                                DISTINCT
                                                dmc.colaborador_id
                                                from movimiento_caja as mc
                                                inner join detalles_movimiento_caja as dmc on dmc.movimiento_id = mc.id
                                                where
                                                mc.estado_movimiento = "APERTURA"
                                                AND mc.sede_id = ?
                                            )
                                            AND (r.name LIKE "%VENDEDOR%")',
                [$sede_id, $sede_id]
            );

            return response()->json([
                'success' => true,
                'cajas_desocupadas'         =>  $cajas_desocupadas,
                'cajeros_desocupados'       =>  $cajeros_desocupados,
                'vendedores_desocupados'    =>  $vendedores_desocupados
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getMovimientosCajas(Request $request)
    {
        $datos      = [];
        $consulta   = null;
        if ($request->filter    ==  "ACTIVO") {
            $consulta = MovimientoCaja::where(DB::raw("CONVERT(fecha_apertura,date)"), ">=", $request->desde)
                ->where(DB::raw("CONVERT(fecha_apertura,date)"), "<=", $request->hasta);
        } else {
            $consulta = MovimientoCaja::whereMonth(
                'fecha_apertura',
                $request->mes
            )->whereYear('fecha_apertura', $request->anio);
        }

        //========= FILTRO POR ROLES ======
        $roles = DB::table('role_user as rl')
            ->join('roles as r', 'r.id', '=', 'rl.role_id')
            ->where('rl.user_id', Auth::user()->id)
            ->pluck('r.name')
            ->toArray();

        //======== ADMIN PUEDE VER TODOS LOS MOVIMIENTOS CAJA DE SU SEDE =====
        if (in_array('ADMIN', $roles)) {
            $consulta->where('sede_id', Auth::user()->sede_id);
        } else {

            //====== USUARIOS PUEDEN VER SOLO SUS PROPIOS MOVIMIENTOS CAJA ======
            $consulta->where('sede_id', Auth::user()->sede_id)
                ->where('colaborador_id', Auth::user()->colaborador_id);
        }

        if ($request->caja_id) {
            $consulta->where('caja_id', $request->caja_id);
        }

        $movimientos = $consulta
            ->where('estado', 'ACTIVO')
            ->get([
                'id',
                'caja_id',
                'colaborador_id',
                'sede_id',
                'monto_inicial',
                'monto_final',
                'fecha_apertura',
                'fecha_cierre',
                'estado_movimiento',
                'fecha',
                'estado',
            ]);
        foreach ($movimientos as $key => $movimiento) {
            $colaborador    =   Colaborador::find($movimiento->colaborador_id);
            $sede           =   Sede::find($movimiento->sede_id);

            array_push($datos, [
                'colaborador_nombre'    =>  $colaborador->nombre,
                'sede_nombre'           =>  $sede->nombre,
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


    /*
array:4 [
  "caja"            => "1"
  "cajero_id"       => "7"
  "turno"           => "Mañana"
  "saldo_inicial"   => "0"
  "sede_id"         =>  1
]
*/
    public function aperturaCaja(MovimientoCajaAperturaRequest $request)
    {
        DB::beginTransaction();
        try {

            //======= VALIDAR CAJERO ====
            $colaborador    =   DB::select('select
                            r.name as rol_nombre
                            from users as u
                            inner join role_user as ru on ru.user_id = u.id
                            inner join roles as r on r.id = ru.role_id
                            where u.colaborador_id = ?', [$request->get('cajero_id')]);

            $roles = array_column($colaborador, 'rol_nombre');

            if (!in_array('CAJERO', $roles)) {
                throw new Exception("EL COLABORADOR MAESTRO NO TIENE ROL DE CAJERO!!!");
            }

            //======== VALIDAR QUE EL COLABORADOR ESTÉ LIBRE =====
            $cajero_libre   =   DB::select(
                'select
                            mc.*
                            from movimiento_caja as mc
                            where
                            mc.colaborador_id = ?
                            AND mc.estado_movimiento = "APERTURA"',
                [$request->get('cajero_id')]
            );

            if (count($cajero_libre) > 0) {
                throw new Exception("EL CAJERO ESTÁ OCUPADO EN UNA CAJA!!!");
            }

            //========= MOVIMIENTO CAJA MAESTRO =======
            $movimiento                     =   new MovimientoCaja();
            $movimiento->caja_id            =   $request->get('caja');
            $movimiento->colaborador_id     =   $request->get('cajero_id');
            $movimiento->monto_inicial      =   $request->get('saldo_inicial');
            $movimiento->estado_movimiento  =   'APERTURA';
            $movimiento->fecha_apertura     =   date('Y-m-d h:i:s');
            $movimiento->fecha              =   date('Y-m-d');
            $movimiento->sede_id            =   $request->get('sede_id');
            $movimiento->save();

            //========= DETALLE DEL MOVIMIENTO =====
            $detallesMovimiento                 =   new DetallesMovimientoCaja();
            $detallesMovimiento->movimiento_id  =   $movimiento->id;
            $detallesMovimiento->colaborador_id =   $request->get('cajero_id');
            $detallesMovimiento->fecha_entrada  =   date('Y-m-d h:i:s');
            $detallesMovimiento->sede_id        =   $request->get('sede_id');
            $detallesMovimiento->save();

            $caja               =   Caja::findOrFail($request->caja);
            $caja->estado_caja  =   'ABIERTA';
            $caja->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CAJA APERTURADA CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine()]);
        }
    }

    public function estadoCajas()
    {
        $cajas = Caja::where('estado', 'ACTIVO')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $result = $cajas->map(function ($caja) {
            $movimiento = MovimientoCaja::where('caja_id', $caja->id)
                ->where('estado_movimiento', 'APERTURA')
                ->orderByDesc('id')
                ->first(['colaborador_id', 'fecha_apertura']);

            $colaborador = null;
            if ($movimiento) {
                $colaborador = Colaborador::find($movimiento->colaborador_id);
            }

            return [
                'nombre'      => $caja->nombre,
                'estado_caja' => $movimiento ? 'ABIERTA' : 'CERRADA',
                'colaborador' => $colaborador ? $colaborador->nombre : '-',
                'desde'       => $movimiento ? $movimiento->fecha_apertura : '-',
            ];
        });

        return response()->json($result);
    }

    public function cerrarCaja(Request $request)
    {
        $this->s_caja->cerrar((int) $request->movimiento_id, (float) $request->saldo);
        return redirect()->route('Caja.Movimiento.index');
    }

    public function verificarVentasNoPagadas($movimiento_id)
    {
        try {
            $docs = $this->s_caja->ventasNoPagadas((int) $movimiento_id);
            return response()->json(['success' => true, 'docs_no_pagados' => $docs]);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => 'Error al consultar documentos pendientes',
                'exception' => $th->getMessage(),
            ]);
        }
    }

    public function cajaDatosCierre(Request $request)
    {
        return response()->json(
            $this->s_caja->datosCierre((int) $request->id)->toArray()
        );
    }

    public function verificarEstadoUser()
    {
        try {

            $caja_movimiento           =   movimientoUser();

            if (count($caja_movimiento) == 0) {
                throw new Exception("DEBES FORMAR PARTE DE UNA CAJA ABIERTA!!!");
            }

            return response()->json(['success' => true, 'message' => 'CAJA VALIDA']);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ]);
        }
    }

    public function reporteMovimiento($id)
    {
        $pdf = $this->s_caja->reporteMovimiento((int) $id);
        return $pdf->stream();
    }
    private function ObtenerTotales($id)
    {
        $movimiento = MovimientoCaja::findOrFail($id);
        // $TotalVentaDelDia =
        //     (float) cuadreMovimientoCajaIngresosVenta($movimiento) -
        //     (float) cuadreMovimientoDevoluciones($movimiento);
        $TotalVentaDelDia =
            (float) cuadreMovimientoCajaIngresosVenta($movimiento) +
            (float) cuadreMovimientoCajaIngresosRecibo($movimiento);


        return [
            'TotalVentaDelDia' => $TotalVentaDelDia,
        ];
    }
}
