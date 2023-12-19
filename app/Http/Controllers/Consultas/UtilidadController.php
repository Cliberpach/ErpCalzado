<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtilidadController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'utilidad_mensual.index');

        $lstAnios = DB::table('lote_productos')
            ->select(DB::raw('year(created_at) as value'))
            ->distinct()
            ->orderBy('value', 'desc')
            ->get();

        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy, 'm');
        $anio = date_format($fecha_hoy, 'Y');

        return view('consultas.utilidad.index', [
            'lstAnios' => $lstAnios,
            'mes' => $mes,
            'anio_' => $anio,
        ]);
    }

    public function getDatos($mes, $anio)
    {
        $fecini = $anio . '-' . $mes . '-01';
        $fecini = date('Y-m-d', strtotime($fecini));
        $fecfin = date('Y-m-d', strtotime($fecini . '+ 1 month'));

        $ventas = Documento::where('estado','!=','ANULADO')->whereBetween('fecha_documento' , [$fecini, $fecfin])->orderBy('id', 'desc')->get();
        
        $inversion_mensual = $this->InversionCompleja($fecini,$fecfin);
        $ventas_mensual = ventas_mensual_random($mes, $anio);

        $utilidad_mensual = utilidad_mensual_random($mes,$anio);
        $porcentaje = 0;
        if ($ventas_mensual > 0) {
            $porcentaje = ($utilidad_mensual * 100) / $ventas_mensual;
        }

        $dolar_aux = json_encode(precio_dolar(), true);
        $dolar_aux = json_decode($dolar_aux, true);

        $dolar = (float) $dolar_aux['original']['venta'];

        $inversion_mensual_dolares = $inversion_mensual / $dolar;
        $ventas_mensual_dolares = $ventas_mensual / $dolar;
        $utilidad_mensual_dolares = $utilidad_mensual / $dolar;

        return response()->json([
            'inversion_mensual' => $inversion_mensual,
            'ventas_mensual' => $ventas_mensual,
            'utilidad_mensual' => $utilidad_mensual,
            'inversion_mensual_dolares' => $inversion_mensual_dolares,
            'ventas_mensual_dolares' => $ventas_mensual_dolares,
            'utilidad_mensual_dolares' => $utilidad_mensual_dolares,
            'porcentaje' => $porcentaje,
        ]);
    }
    private function Inversion1($fecini){
       
        $inversion = DB::table("productos as p")
        ->join("compra_documento_detalles as ddc","ddc.producto_id","=","p.id")
        ->join("compra_documentos as dc","dc.id","=","ddc.documento_id")
        ->select(DB::raw("SUM(ddc.cantidad * ddc.precio_soles) as total"))
        ->where("dc.fecha_emision","<",$fecini)
        ->where("dc.estado","<>","ANULADO")
        ->first();
        return $inversion->total ? (float)$inversion->total : 0;
    }

    private function Inversion2($fecini){
        
        $inversion = DB::table("productos as p")
        ->join("detalle_nota_ingreso as dni","dni.producto_id","=","p.id")
        ->join("nota_ingreso as ni","ni.id","=","dni.nota_ingreso_id")
        ->select(DB::raw("SUM(dni.cantidad * dni.costo_soles) as total"))
        ->where("ni.fecha","<",$fecini)
        ->where("ni.estado","<>","ANULADO")
        ->first();
        return $inversion->total ? (float)$inversion->total : 0;
        
    }
    private function Inversion3($fecini){
       
        $inversion = DB::table("productos as p")
        ->join("lote_productos as lp","lp.producto_id","=","p.id")
        ->join("cotizacion_documento_detalles as ddv","ddv.lote_id","=","lp.id")
        ->join("cotizacion_documento as dv","dv.id","=","ddv.documento_id")
        ->select(DB::raw("SUM(ddv.cantidad * ddv.precio_nuevo) as total"))
        ->where("dv.fecha_documento","<",$fecini)
        ->where("dv.estado","<>","ANULADO")
        ->where("ddv.eliminado","=",'0')
        ->first();
        return $inversion->total ? (float)$inversion->total : 0;

    }
    private function Inversion4($fecini){
       
        $inversion = DB::table("productos as p")
        ->join("lote_productos as lp","lp.producto_id","=","p.id")
        ->join("cotizacion_documento_detalles as ddv","ddv.lote_id","=","lp.id")
        ->join("cotizacion_documento as dv","dv.id","=","ddv.documento_id")
        ->select(DB::raw("SUM(ddv.cantidad * ddv.precio_nuevo) as total"))
        ->where("dv.fecha_documento","<",$fecini)
        ->where("dv.estado","<>","ANULADO")
        ->where("ddv.eliminado","=",'0')
        ->where("dv.tipo_venta","<>",'129')
        ->where("dv.convertir","<>",'')
        ->first();
        return $inversion->total ? (float)$inversion->total : 0;
    }
    private function Inversion5($fecini){
        
        $inversion = DB::table("productos as p")
        ->join("detalle_nota_salidad as dns","dns.producto_id","=","p.id")
        ->join("nota_salidad as ns","ns.id","=","dns.nota_salidad_id")
        ->select(DB::raw("SUM(dns.cantidad * p.precio_compra) as total"))
        ->where("ns.fecha","<",$fecini)
        ->where("ns.estado","<>","ANULADO")
        ->first();
        return $inversion->total ? (float)$inversion->total : 0;

    }
    private function Inversion6($fecini){
       
        $inversion = DB::table("productos as p")
        ->join("lote_productos as ltp","ltp.producto_id","=","p.id")
        ->join("cotizacion_documento_detalles as cdd","cdd.lote_id","=","ltp.id")
        ->join("nota_electronica_detalle as ned","ned.detalle_id","=","cdd.id")
        ->join("nota_electronica as ne","ne.id","=","ned.nota_id")
        ->select(DB::raw("SUM(ned.cantidad * p.precio_compra) as total"))
        ->where("ne.fechaEmision","<",$fecini)
        ->where("ne.estado","<>","ANULADO")
        ->first();
        return $inversion->total ? (float)$inversion->total : 0;
       
    }
    private function Inversion7($fecini,$fecfin){
        $inversion = DB::table("productos as p")
        ->join("lote_productos as ltp","ltp.producto_id","=","p.id")
        ->join("cotizacion_documento_detalles as vdd","vdd.lote_id","=","ltp.id")
        ->join("cotizacion_documento as vd","vd.id","=","vdd.documento_id")
        ->select(DB::raw("SUM(vdd.cantidad * vdd.precio_nuevo) as total"))
        ->where("vd.fecha_documento",">=",$fecini)
        ->where("vd.fecha_documento","<=",$fecfin)
        ->where("vd.estado","<>","ANULADO")
        ->where("vdd.eliminado","=",'0')
        ->first();

        return $inversion->total ? (float)$inversion->total : 0;
        // $inversion =DB::table("productos")
        // ->select(DB::raw("ifnull((SELECT SUM(vdd.cantidad * vdd.precio_nuevo) from 
        // cotizacion_documento_detalles vdd 
        // INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id 
        // INNER JOIN lote_productos lp ON vdd.lote_id = lp.id 
        // WHERE vd.fecha_documento >= '{$fecini}' and vd.fecha_documento <= '{$fecfin}' AND vd.estado != 'ANULADO' AND lp.producto_id = productos.id AND vdd.eliminado = '0'),0) as inversion"))
        // ->get();
        // $inversion_mensual = $inversion
        // ->where('inversion', '>', 0)
        // ->sum('inversion');
        // return $inversion_mensual;
    }
    private function Inversion8($fecini,$fecfin){

        $inversion = DB::table("productos as p")
        ->join("lote_productos as ltp","ltp.producto_id","=","p.id")
        ->join("cotizacion_documento_detalles as vdd","vdd.lote_id","=","ltp.id")
        ->join("cotizacion_documento as vd","vd.id","=","vdd.documento_id")
        ->select(DB::raw("SUM(vdd.cantidad * vdd.precio_nuevo) as total"))
        ->where("vd.fecha_documento",">=",$fecini)
        ->where("vd.fecha_documento","<=",$fecfin)
        ->where("vd.estado","<>","ANULADO")
        ->where("vd.tipo_venta","<>",'129')
        ->where("vd.convertir","<>",'')
        ->where("vdd.eliminado","=",'0')
        ->first();

        return $inversion->total ? (float)$inversion->total : 0;
    }
    private function Inversion9($fecini,$fecfin){
      
        $inversion = DB::table("productos as p")
        ->join("detalle_nota_salidad as dns","dns.producto_id","=","p.id")
        ->join("nota_salidad as ns","ns.id","=","dns.nota_salidad_id")
        ->select(DB::raw("SUM(dns.cantidad * p.precio_compra) as total"))
        ->where("ns.fecha",">=",$fecini)
        ->where("ns.fecha","<=",$fecfin)
        ->where("ns.estado","<>","ANULADO")
        ->first();

        return $inversion->total ? (float)$inversion->total : 0;
    }
    private function Inversion10($fecini,$fecfin){
        $inversion = DB::table("productos as p")
        ->join("compra_documento_detalles as cdd","cdd.producto_id","=","p.id")
        ->join("compra_documentos as cd","cd.id","=","cdd.documento_id")
        ->select(DB::raw("SUM(cdd.cantidad * cdd.precio_soles) as total"))
        ->where("cd.fecha_emision",">=",$fecini)
        ->where("cd.fecha_emision","<=",$fecfin)
        ->where("cd.estado","<>","ANULADO")
        ->first();

        return $inversion->total ? (float)$inversion->total : 0;
    }
    private function Inversion11($fecini,$fecfin){
        $inversion = DB::table("productos as p")
        ->join("detalle_nota_ingreso as dni","dni.producto_id","=","p.id")
        ->join("nota_ingreso as ni","ni.id","=","dni.nota_ingreso_id")
        ->select(DB::raw("SUM(dni.cantidad * dni.costo_soles) as total"))
        ->where("ni.fecha",">=",$fecini)
        ->where("ni.fecha","<=",$fecfin)
        ->where("ni.estado","<>","ANULADO")
        ->first();

        return $inversion->total ? (float)$inversion->total : 0;
    }
    private function Inversion12($fecini,$fecfin){
        $inversion = DB::table("productos as p")
        ->join("lote_productos as ltp","ltp.producto_id","=","p.id")
        ->join("cotizacion_documento_detalles as cdd","cdd.lote_id","=","ltp.id")
        ->join("nota_electronica_detalle as ned","ned.detalle_id","=","cdd.id")
        ->join("nota_electronica as ne","ne.id","=","ned.nota_id")
        ->select(DB::raw("SUM(ned.cantidad * p.precio_compra) as total"))
        ->where("ne.fechaEmision",">=",$fecini)
        ->where("ne.fechaEmision","<=",$fecfin)
        ->where("ne.estado","<>","ANULADO")
        ->first();
        return $inversion->total ? (float)$inversion->total : 0;
        
    }
    private function InversionCompleja($fecini,$fecfin){

        $inver1 = $this->Inversion1($fecini);
        $inver2 = $this->Inversion2($fecini);
        $inver3 = $this->Inversion3($fecini);
        $inver4 = $this->Inversion4($fecini);
        $inver5 = $this->Inversion5($fecini);
        $inver6 = $this->Inversion6($fecini);

        $inver7 = $this->Inversion7($fecini,$fecfin);
        $inver8 = $this->Inversion8($fecini,$fecfin);
        $inver9 = $this->Inversion9($fecini,$fecfin);

        $inver10 = $this->Inversion10($fecini,$fecfin);
        $inver11 = $this->Inversion11($fecini,$fecfin);
        $inver12 = $this->Inversion12($fecini,$fecfin);


        $inversion_compleja = ( ($inver1 + $inver2 - $inver3 - $inver4 - $inver5 + $inver6) - ($inver7 - $inver8 + $inver9) + $inver10 + $inver11 + $inver12);
        return $inversion_compleja;
    }
    private function QueryInversionComplejaAntigua($fecini,$fecfin){
        $inversion_compleja = DB::table('productos')
            ->select(
                DB::raw("(
                    (
                        ifnull((SELECT sum(ddc.cantidad * ddc.precio_soles) from compra_documento_detalles ddc INNER JOIN compra_documentos dc ON ddc.documento_id = dc.id WHERE dc.fecha_emision < '{$fecini}' AND ddc.producto_id = productos.id AND dc.estado != 'ANULADO'),0) +
                        ifnull((SELECT sum(dni.cantidad * dni.costo_soles) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha < '{$fecini}' AND dni.producto_id = productos.id AND ni.estado != 'ANULADO'),0) -
                        ifnull((SELECT SUM(ddv.cantidad * ddv.precio_nuevo) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < '{$fecini}' AND dv.estado != 'ANULADO' AND lp.producto_id = productos.id AND ddv.eliminado = '0'),0) -
                        ifnull((SELECT SUM(ddv.cantidad * ddv.precio_nuevo) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < '{$fecini}' AND dv.estado != 'ANULADO' AND lp.producto_id = productos.id AND dv.tipo_venta != '129' AND dv.convertir != '' AND ddv.eliminado = '0'),0) -
                        ifnull((SELECT SUM(dns.cantidad * productos.precio_compra) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha < '{$fecini}' AND ns.estado != 'ANULADO' AND dns.producto_id = productos.id),0) +
                        ifnull((SELECT SUM(ned.cantidad * productos.precio_compra) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision < '{$fecini}' AND ne.estado != 'ANULADO' AND lpn.producto_id = productos.id),0)
                    ) -
                    (
                        ifnull((SELECT SUM(vdd.cantidad * vdd.precio_nuevo) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= '{$fecini}' and vd.fecha_documento <= '{$fecfin}' AND vd.estado != 'ANULADO' AND lp.producto_id = productos.id AND vdd.eliminado = '0'),0) -
                        ifnull((SELECT SUM(vdd.cantidad * vdd.precio_nuevo) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= '{$fecini}' and vd.fecha_documento <= '{$fecfin}' AND vd.estado != 'ANULADO' AND lp.producto_id = productos.id AND vd.tipo_venta != '129' AND vd.convertir != '' AND vdd.eliminado = '0'),0) +
                        ifnull((SELECT SUM(dns.cantidad * productos.precio_compra) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha >= '{$fecini}' AND ns.fecha <= '{$fecfin}' AND ns.estado != 'ANULADO' AND dns.producto_id = productos.id),0)
                    ) +
                    ifnull((SELECT SUM(cdd.cantidad * cdd.precio_soles) from compra_documento_detalles cdd INNER JOIN compra_documentos cd ON cdd.documento_id = cd.id WHERE cd.fecha_emision >= '{$fecini}' AND cd.fecha_emision <= '{$fecfin}' AND cd.estado != 'ANULADO' AND cdd.producto_id = productos.id),0) +
                    ifnull((SELECT sum(dni.cantidad * dni.costo_soles) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha >= '{$fecini}' AND ni.fecha <= '{$fecfin}' AND dni.producto_id = productos.id AND ni.estado != 'ANULADO'),0) +
                    ifnull((SELECT SUM(ned.cantidad * productos.precio_compra) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision >= '{$fecini}' AND ne.fechaEmision <= '{$fecfin}' AND  ne.estado != 'ANULADO' AND lpn.producto_id = productos.id),0)
                ) as inversion")
            )
            ->get();
            return $inversion_compleja;
    }
}
