<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Cuentas\Cliente\CuentaClienteComprobanteRequest;
use App\Http\Requests\Cuentas\Cliente\CuentaClientePagarRequest;
use App\Http\Services\Cuentas\Cliente\CuentaManager;
use App\Mantenimiento\Cuenta\Cuenta;
use App\Mantenimiento\Empresa\Empresa;
use App\Ventas\Cliente;
use App\Ventas\CuentaCliente;
use App\Ventas\DetalleCuentaCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Throwable;

class CuentaClienteController extends Controller
{

    private CuentaManager $s_cuenta;

    public function __construct()
    {
        $this->s_cuenta =   new CuentaManager();
    }

    public function index()
    {
        $fecha_hoy = Carbon::now()->toDateString();
        $tipo_comprobantes  =   UtilidadesController::getTipoComprobantes();
        return view('ventas.cuentaCliente.index', compact('fecha_hoy', 'tipo_comprobantes'));
    }

    public function getTable(Request $request)
    {

        $cliente_id =   $request->get('cliente');
        $estado     =   $request->get('estado');

        $cuentas    =   DB::table('cuenta_cliente as cc')
            ->join('cotizacion_documento as cd', 'cd.id', 'cc.cotizacion_documento_id')
            ->select(
                'cc.id',
                'cc.numero_doc',
                'cd.cliente',
                'cc.fecha_doc',
                'cc.monto',
                'cc.acta',
                'cc.saldo',
                'cc.estado'
            )
            ->where('cc.estado', '<>', 'ANULADO');

        if ($cliente_id) {
            $cuentas->where('cd.cliente_id', $cliente_id);
        }
        if ($estado) {
            $cuentas->where('cc.estado', $estado);
        }

        return DataTables::of($cuentas)->make(true);
    }
    /*public function getTable()
    {
        $datos = array();
        $cuentaCliente = CuentaCliente::where('estado', '!=', 'ANULADO')->get();
        foreach ($cuentaCliente as $key => $value) {
            $detalle_ultimo = DetalleCuentaCliente::where('cuenta_cliente_id', $value->id)->get()->last();

            $total_pagar = $value->documento->total_pagar - $value->documento->notas->sum("mtoImpVenta");

            $nuevo_monto = $total_pagar - $value->detalles->sum("monto");
            $detalle_ultimo->saldo = $nuevo_monto;
            $detalle_ultimo->update();

            if (!empty($detalle_ultimo)) {
                if ($detalle_ultimo->saldo == 0) {
                    $cuenta = CuentaCliente::find($value->id);
                    $cuenta->saldo = 0;
                    $cuenta->estado = 'PAGADO';
                    $cuenta->update();
                } else {
                    $cuenta = CuentaCliente::find($value->id);
                    $cuenta->saldo = $detalle_ultimo->saldo;
                    $cuenta->estado = 'PENDIENTE';
                    $cuenta->update();
                }
            }

            $acta =  $value->detalles->sum("monto");
            if ($acta < $value->monto) {
                $cuenta = CuentaCliente::find($value->id);
                $cuenta->estado = 'PENDIENTE';
                $cuenta->update();
            } else {
                $cuenta = CuentaCliente::find($value->id);
                $cuenta->estado = 'PAGADO';
                $cuenta->update();
            }

            $cuenta_cliente = CuentaCliente::find($value->id);

            array_push($datos, array(
                "id" => $cuenta_cliente->id,
                "cliente" => $cuenta_cliente->documento->clienteEntidad->nombre,
                "numero_doc" => $cuenta_cliente->documento->serie . ' - ' . $cuenta_cliente->documento->correlativo,
                "fecha_doc" => $cuenta_cliente->fecha_doc,
                "monto" => $cuenta_cliente->documento->total_pagar - $cuenta_cliente->documento->notas->sum("mtoImpVenta"),
                "acta" => number_format(round($acta, 2), 2),
                "saldo" => $cuenta_cliente->saldo,
                "estado" => $cuenta_cliente->estado
            ));
        }
        return DataTables::of($datos)->toJson();
    }*/

    public function getDatos(int $id)
    {
        try {

            $cuenta =   DB::table('cuenta_cliente as cc')
                ->select(
                    'cc.id',
                    'cc.numero_doc',
                    'cd.cliente',
                    'cc.monto',
                    'cc.saldo',
                    'cc.estado',
                    'cd.pedido_id',
                    'cd.cliente_id'
                )
                ->join('cotizacion_documento as cd', 'cd.id', 'cc.cotizacion_documento_id')
                ->where('cc.id', $id)
                ->first();

            if (!$cuenta) {
                throw new Exception("CUENTA CLIENTE NO EXISTE EN LA BD");
            }

            $detalle    =   DetalleCuentaCliente::where('cuenta_cliente_id', $id)
                ->orderByDesc('id')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'CUENTA CLIENTE OBTENIDA',
                'data' => [
                    'cuenta' => $cuenta,
                    'detalle' => $detalle
                ]
            ]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function consulta(Request $request)
    {
        $cuentas = DB::table('cuenta_cliente')
            ->join('cotizacion_documento', 'cotizacion_documento.id', '=', 'cuenta_cliente.cotizacion_documento_id')
            ->join('clientes', 'clientes.id', '=', 'cotizacion_documento.cliente_id')
            ->when($request->get('cliente'), function ($query, $request) {
                return $query->where('clientes.id', $request);
            })
            ->when($request->get('estado'), function ($query, $request) {
                return $query->where('cuenta_cliente.estado', $request);
            })
            ->select(
                'cuenta_cliente.*',
                'clientes.nombre as cliente',
                'cotizacion_documento.numero_doc as numero_doc',
                'cotizacion_documento.created_at as fecha_doc',
                'cotizacion_documento.total_pagar as monto'
            )->get();
        return $cuentas;
    }

    /*
array:10 [▼
  "_token" => "5vHd7fo0eYMRYlOb0WBlabZmvvZDBLI3a2prGN3P"
  "pago" => "TODO"
  "fecha" => "2025-08-15"
  "cantidad" => "23.00"
  "observacion" => "test"
  "efectivo_venta" => "23.00"
  "modo_pago" => "4"
  "cuenta" => "6"
  "importe_venta" => "0"
  "url_imagen" => null
  "nro_operacion" => null
  "modo_despacho" => "RESERVAR"
]
*/
    public function detallePago(CuentaClientePagarRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->s_cuenta->pagar($request->toArray(), $request->get('id'));

            DB::commit();
            return response()->json(['success' => true, 'message' => "PAGO REGISTRADO CON ÉXITO"]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()]);
        }
    }

    public function reporte($id)
    {
        $cuenta = CuentaCliente::findOrFail($id);
        $cliente = Cliente::find($cuenta->documento->cliente_id);
        $empresa = Empresa::first();
        $cuentas_bancarias  =   Cuenta::where('estado', 'ACTIVO')->get();
        $mostrar_cuentas    =   DB::select('SELECT
                                c.propiedad
                                FROM configuracion AS c
                                WHERE c.slug = "MCB"')[0]->propiedad;

        $pdf = PDF::loadview('ventas.documentos.impresion.detalle_cuenta', [
            'cuenta' => $cuenta,
            'detalles' => $cuenta->detalles,
            'cliente' => $cliente,
            'empresa' => $empresa,
            'cuentas_bancarias' =>  $cuentas_bancarias,
            'mostrar_cuentas'   =>  $mostrar_cuentas
        ])->setPaper('a4');
        return $pdf->stream('CUENTA-' . $cuenta->id . '.pdf');
    }

    public function detalle(Request $request)
    {
        $estado = $request->estado;
        $id = $request->id;
        //$cuentas = CuentaCliente::where('cliente_id',$request->id)->where('estado', $request->estado);
        $cuentas = DB::table('cuenta_cliente')
            ->join('cotizacion_documento', 'cotizacion_documento.id', '=', 'cuenta_cliente.cotizacion_documento_id')
            ->join('clientes', 'clientes.id', '=', 'cotizacion_documento.cliente_id')
            ->select(
                'cuenta_cliente.*',
            )
            ->where('cotizacion_documento.cliente_id', $id)
            ->where('cuenta_cliente.estado', $estado)
            ->get();
        $cliente = Cliente::find($request->id);
        $empresa = Empresa::first();
        $cuentas_bancarias  =   Cuenta::where('estado', 'ACTIVO')->get();
        $mostrar_cuentas    =   DB::select('SELECT
                                c.propiedad
                                FROM configuracion AS c
                                WHERE c.slug = "MCB"')[0]->propiedad;

        $pdf = PDF::loadview('ventas.documentos.impresion.detalle_cuenta_cliente', [
            'cuentas' => $cuentas,
            'cliente' => $cliente,
            'empresa' => $empresa,
            'cuentas_bancarias' =>  $cuentas_bancarias,
            'mostrar_cuentas'   =>  $mostrar_cuentas
        ])->setPaper('a4');
        return $pdf->stream('CUENTAS-' . $cliente->nombre_comercial . '.pdf');
    }

    public function imagen($id)
    {
        $detalle = DetalleCuentaCliente::find($id);
        $ruta = storage_path() . '/app/' . $detalle->ruta_imagen;
        return response()->download($ruta);
    }

    /*
array:4 [
  "cliente" => "1281"
  "tipo_comprobante" => "127"
  "observacion" => "test"
  "pago_id" => "null"
]
*/
    public function generarComprobantePago(CuentaClienteComprobanteRequest $request)
    {
        DB::beginTransaction();
        try {

            $venta  =   $this->s_cuenta->generarComprobantePago($request->toArray());

            $url_pdf = route('ventas.documento.comprobante', [
                'id' => $venta->id,
                'size' => 80
            ]);

            //====== REGISTRO DE ACTIVIDAD ========
            $descripcion = "SE GENERÓ EL COMPROBANTE DEL PAGO: " . Carbon::parse($venta->created_at)->format('d/m/y');
            $gestion = "COMPROBANTE PAGO CLIENTE";
            crearRegistro($venta, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'COMPROBANTE ANTICIPO GENERADO CON ÉXITO', 'url_pdf' => $url_pdf]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()]);
        }
    }
}
