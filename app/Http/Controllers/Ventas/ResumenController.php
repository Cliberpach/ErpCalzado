<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Ventas\Resumen;
use App\Ventas\DetalleResumen;

class ResumenController extends Controller
{
    public function index(){
        return view('ventas.resumenes.index');
    }

    public function getComprobantes($fechaComprobantes){
        $comprobantes   =   DB::select('select cd.id as documento_id, cd.serie as documento_serie,
                            cd.correlativo as documento_correlativo, td.parametro as documento_moneda,
                            cd.total_pagar as documento_total,cd.total_igv as documento_igv,
                            cd.total as documento_subtotal
                            from cotizacion_documento as cd
                            inner join tabladetalles as td on td.id=cd.moneda
                            where cd.fecha_documento=? and cd.serie="B001" 
                            and td.tabla_id=1',[$fechaComprobantes]);   

        return response()->json(['success'=>$comprobantes , 'fecha' => $fechaComprobantes]);
    }

    public function store(Request $request){
        $comprobantes   =   $request->get('comprobantes');
        //===== BUSCANDO CORRELATIVO DEL COMPROBANTE =====
        $correlativo    =   $this->getCorrelativo();

        //==== GUARDANDO RESUMEN EN LA BD ====
        $resumen                =   new Resumen();
        $resumen->serie         =   'R001';
        $resumen->correlativo   =   $correlativo;
        $resumen->save();

        return response()->json(['success'=>'RESUMEN REGISTRADO COMO'.'R001'.$correlativo]);
    }

    public function isActive(){
        $resumenActive  = DB::table('empresa_numeracion_facturaciones')->where('tipo_comprobante', 190)->exists();
        return response()->json(['resumenActive'=>$resumenActive]);
    }

    public function getCorrelativo(){
        //===== OBTENIENDO EL ÚLTIMO RESUMEN DE LA TABLA RESÚMENES =======
        $ultimoResumen    =  DB::table('resumenes')->latest()->first();

        //======== EN CASO YA EXISTAN RESÚMENES GENERADOS =====
        if($ultimoResumen){
            $correlativo    =   $ultimoResumen->correlativo+1;
            return $correlativo;
        }

        //===== BUSCAMOS EL REGISTRO DEL COMPROBANTE RESÚMENES =======
        $correlativo   =   DB::select('select enf.numero_iniciar 
                                    from empresa_numeracion_facturaciones as enf
                                    where enf.tipo_comprobante=190')[0]->numero_iniciar;

        return $correlativo;
    }
}
