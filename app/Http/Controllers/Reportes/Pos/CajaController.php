<?php

namespace App\Http\Controllers\Reportes\Pos;

use App\Exports\Reportes\Pos\CajaExport;
use App\Http\Controllers\Controller;
use App\Pos\Caja;
use App\Pos\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::where('estado','ACTIVO')->get();
        return view('reportes.pos.cajadiaria',compact('cajas'));
    }

    public function getTable(Request $request)
    {
        $caja = $request->caja_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        if($caja != '' && $fecha_ini != '' && $fecha_fin != '')
        {
            return datatables()->query(
                DB::table('movimiento_caja')
                ->join('caja','movimiento_caja.caja_id','=','caja.id')
                ->select(
                    'movimiento_caja.id',
                    'caja.nombre as caja',
                    'movimiento_caja.fecha_apertura',
                    'movimiento_caja.monto_inicial as inicio',
                    DB::raw('ifnull((select sum(cv.total) from  detalle_movimiento_venta  dmv inner join cotizacion_documento cv on dmv.cdocumento_id = cv.id where dmv.mcaja_id = movimiento_caja.id and cv.condicion_id = 1 and cv.estado_pago = "PAGADA"), 0) as ventas'),
                    DB::raw('ifnull((select sum(dcc.monto) from  detalle_cuenta_cliente dcc where dcc.mcaja_id = movimiento_caja.id), 0) as cobranzas'),
                    DB::raw('ifnull((select sum(dcp.importe + dcp.efectivo) from  detalle_cuenta_proveedor dcp where dcp.mcaja_id = movimiento_caja.id), 0) as pagos'),
                    DB::raw('ifnull((select sum(e.importe) from  detalle_movimiento_egresos dme inner join egreso e on dme.egreso_id = e.id where dme.mcaja_id = movimiento_caja.id), 0) as egresos'),
                    'movimiento_caja.monto_final as saldo'
                )
                ->where('movimiento_caja.estado_movimiento','CIERRE')
                ->where('movimiento_caja.caja_id',$caja)
                ->whereBetween('movimiento_caja.fecha',[$fecha_ini,$fecha_fin])
            )->toJson();
        }
        else if($caja != '' && $fecha_ini == '' && $fecha_fin == '')
        {
            return datatables()->query(
                DB::table('movimiento_caja')
                ->join('caja','movimiento_caja.caja_id','=','caja.id')
                ->select(
                    'movimiento_caja.id',
                    'caja.nombre as caja',
                    'movimiento_caja.fecha_apertura',
                    'movimiento_caja.monto_inicial as inicio',
                    DB::raw('ifnull((select sum(cv.total) from  detalle_movimiento_venta  dmv inner join cotizacion_documento cv on dmv.cdocumento_id = cv.id where dmv.mcaja_id = movimiento_caja.id and cv.condicion_id = 1 and cv.estado_pago = "PAGADA"), 0) as ventas'),
                    DB::raw('ifnull((select sum(dcc.monto) from  detalle_cuenta_cliente dcc where dcc.mcaja_id = movimiento_caja.id), 0) as cobranzas'),
                    DB::raw('ifnull((select sum(dcp.importe + dcp.efectivo) from  detalle_cuenta_proveedor dcp where dcp.mcaja_id = movimiento_caja.id), 0) as pagos'),
                    DB::raw('ifnull((select sum(e.importe) from  detalle_movimiento_egresos dme inner join egreso e on dme.egreso_id = e.id where dme.mcaja_id = movimiento_caja.id), 0) as egresos'),
                    'movimiento_caja.monto_final as saldo'
                )
                ->where('movimiento_caja.estado_movimiento','CIERRE')
                ->where('movimiento_caja.caja_id',$caja)
            )->toJson();
        }
        else if($caja == '' && $fecha_ini != '' && $fecha_fin != '')
        {
            return datatables()->query(
                DB::table('movimiento_caja')
                ->join('caja','movimiento_caja.caja_id','=','caja.id')
                ->select(
                    'movimiento_caja.id',
                    'caja.nombre as caja',
                    'movimiento_caja.fecha_apertura',
                    'movimiento_caja.monto_inicial as inicio',
                    DB::raw('ifnull((select sum(cv.total) from  detalle_movimiento_venta  dmv inner join cotizacion_documento cv on dmv.cdocumento_id = cv.id where dmv.mcaja_id = movimiento_caja.id and cv.condicion_id = 1 and cv.estado_pago = "PAGADA"), 0) as ventas'),
                    DB::raw('ifnull((select sum(dcc.monto) from  detalle_cuenta_cliente dcc where dcc.mcaja_id = movimiento_caja.id), 0) as cobranzas'),
                    DB::raw('ifnull((select sum(dcp.importe + dcp.efectivo) from  detalle_cuenta_proveedor dcp where dcp.mcaja_id = movimiento_caja.id), 0) as pagos'),
                    DB::raw('ifnull((select sum(e.importe) from  detalle_movimiento_egresos dme inner join egreso e on dme.egreso_id = e.id where dme.mcaja_id = movimiento_caja.id), 0) as egresos'),
                    'movimiento_caja.monto_final as saldo'
                )
                ->where('movimiento_caja.estado_movimiento','CIERRE')
                ->whereBetween('movimiento_caja.fecha',[$fecha_ini,$fecha_fin])
            )->toJson();
        }
        else{
            return datatables()->query(
                DB::table('movimiento_caja')
                ->join('caja','movimiento_caja.caja_id','=','caja.id')
                ->select(
                    'movimiento_caja.id',
                    'caja.nombre as caja',
                    'movimiento_caja.fecha_apertura',
                    'movimiento_caja.monto_inicial as inicio',
                    DB::raw('ifnull((select sum(cv.total) from  detalle_movimiento_venta  dmv inner join cotizacion_documento cv on dmv.cdocumento_id = cv.id where dmv.mcaja_id = movimiento_caja.id and cv.condicion_id = 1 and cv.estado_pago = "PAGADA"), 0) as ventas'),
                    DB::raw('ifnull((select sum(dcc.monto) from  detalle_cuenta_cliente dcc where dcc.mcaja_id = movimiento_caja.id), 0) as cobranzas'),
                    DB::raw('ifnull((select sum(dcp.importe + dcp.efectivo) from  detalle_cuenta_proveedor dcp where dcp.mcaja_id = movimiento_caja.id), 0) as pagos'),
                    DB::raw('ifnull((select sum(e.importe) from  detalle_movimiento_egresos dme inner join egreso e on dme.egreso_id = e.id where dme.mcaja_id = movimiento_caja.id), 0) as egresos'),
                    'movimiento_caja.monto_final as saldo'
                )
                ->where('movimiento_caja.estado_movimiento','CIERRE')
            )->toJson();
        }

        //nuevo atributo de tipo date llamado fecha en movimiento_caja
        // update moviemiento_caja fecha = DATE_FORMAT(fecha_apertura, "%Y-%m-d")
    }

    public function getExcel(Request $request)
    {
        ob_end_clean();
        ob_start();
        $caja = $request->caja_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        return  Excel::download(new CajaExport($caja,$fecha_ini,$fecha_fin), 'CAJA '.$fecha_ini.'-'.$fecha_fin.'.xlsx');
    }
}
