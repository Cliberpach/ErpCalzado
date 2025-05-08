<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Almacen;
use App\Almacenes\Kardex;
use App\Almacenes\ProductoColor;
use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use App\Almacenes\Traslado;
use App\Almacenes\TrasladoDetalle;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Mantenimiento\Empresa\Empresa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;

class SolicitudTrasladoController extends Controller
{
    public function index(){
        return view('almacenes.solicitudes_traslado.index');
    }

    public function getSolicitudesTraslado(Request $request){

        $sede_id    =   Auth::user()->sede_id;

        $traslados  =   DB::table('traslados as t')
                        ->join('almacenes as ao', 'ao.id', '=', 't.almacen_origen_id')
                        ->join('almacenes as ad', 'ad.id', '=', 't.almacen_destino_id')
                        ->join('empresa_sedes as eso', 'eso.id', '=', 't.sede_origen_id')
                        ->join('empresa_sedes as esd', 'esd.id', '=', 't.sede_destino_id')
                        ->select(
                            DB::raw('CONCAT("TR-", t.id) as simbolo'),
                            't.id',
                            'ao.descripcion as almacen_origen_nombre',
                            'ad.descripcion as almacen_destino_nombre',
                            't.observacion',
                            'eso.direccion as sede_origen_direccion',
                            'esd.direccion as sede_destino_direccion',
                            't.created_at as fecha_registro',
                            't.fecha_traslado',
                            't.registrador_nombre',
                            't.estado'
                        )
                        ->where('t.sede_destino_id',$sede_id)
                        ->get();

        return DataTables::of($traslados)->make(true);

    }


    public function confirmarShow($id){

        $traslado   =   Traslado::find($id);
        $detalle    =   TrasladoDetalle::where('traslado_id',$id)->get();
        $tallas     =   Talla::where('estado','ACTIVO')->get();
        $almacen_origen     =   Almacen::find($traslado->almacen_origen_id);
        $almacen_destino    =   Almacen::find($traslado->almacen_destino_id);

        return view('almacenes.solicitudes_traslado.confirmar',
        compact('tallas','detalle','traslado','almacen_origen','almacen_destino'));
    }

/*
array:1 [
  "traslado_id" => "1"
]
*/ 
    public function confirmarStore(Request $request){
        
        DB::beginTransaction();
        try {

            //======== VALIDACIONES PREVIAS =====
            if(!$request->get('traslado_id')){
                throw new Exception("FALTA EL PARÁMETRO TRASLADO ID EN LA PETICIÓN!!!");
            }
            $traslado   =   Traslado::find($request->get('traslado_id'));

            if(!$traslado){
                throw new Exception("NO EXISTE EL TRASLADO EN LA BD!!!");
            }
            if($traslado->estado !== 'PENDIENTE'){
                throw new Exception("EL TRASLADO YA SE ENCUENTRA CON ESTADO ".$traslado->estado.'!!!');
            }

            
            //======== INCREMENTANDO STOCK EN ALMACÉN DESTINO ========
            $detalle    =   TrasladoDetalle::where('traslado_id',$traslado->id)->get();
            if(count($detalle) === 0){
                throw new Exception("EL DETALLE DEL TRASLADO ESTÁ VACÍO!!!");
            }

            $almacen_destino    =   Almacen::find($traslado->almacen_destino_id);
            
            foreach ($detalle as  $item) {

                //====== VERIFICAR EXISTENCIA DEL PRODUCTO EN EL ALMACÉN DESTINO =====
                $producto_destino   =   DB::select('select 
                                        pct.* 
                                        from producto_color_tallas as pct
                                        where 
                                        pct.almacen_id  = ?
                                        and pct.producto_id = ? 
                                        and pct.color_id = ? 
                                        and pct.talla_id = ?',
                                        [
                                            $traslado->almacen_destino_id,
                                            $item->producto_id,
                                            $item->color_id,
                                            $item->talla_id
                                        ]);

                //======== TALLA EXISTE, INCREMENTAR STOCK =========
                if (count($producto_destino) > 0) {

                    ProductoColorTalla::where('producto_id', $item->producto_id)
                    ->where('color_id', $item->color_id)
                    ->where('talla_id', $item->talla_id)
                    ->where('almacen_id', $traslado->almacen_destino_id)
                    ->update([
                    'stock'         =>  DB::raw("stock + $item->cantidad"),
                    'stock_logico'  =>  DB::raw("stock_logico + $item->cantidad"),
                    'estado'        =>  '1',  
                    ]);

                } else {

                //========= TALLA NO EXISTE =============

                    //======= VERIFICANDO EXISTENCIA DEL COLOR ======
                    $existeColor    =   ProductoColor::where('producto_id', $item->producto_id)
                                        ->where('color_id', $item->color_id)
                                        ->where('almacen_id',$traslado->almacen_destino_id)
                                        ->exists();

                    //======== COLOR NO EXISTE, REGISTRAR COLOR =======
                    if(!$existeColor){
                        $producto_color                 =   new ProductoColor();
                        $producto_color->producto_id    =   $item->producto_id;
                        $producto_color->color_id       =   $item->color_id;
                        $producto_color->almacen_id     =   $traslado->almacen_destino_id;
                        $producto_color->save(); 
                    }  

                    //====== REGISTRAR TALLA ============
                    $producto                   =   new ProductoColorTalla();
                    $producto->producto_id      =   $item->producto_id;
                    $producto->color_id         =   $item->color_id;
                    $producto->talla_id         =   $item->talla_id;
                    $producto->stock            =   $item->cantidad;
                    $producto->stock_logico     =   $item->cantidad;
                    $producto->almacen_id       =   $traslado->almacen_destino_id;
                    $producto->save();

                }  

                 //========== REGISTRANDO EN KARDEX ========
                //=========== OBTENIENDO PRODUCTO CON STOCK NUEVO ===========
                $producto   =   DB::select('select 
                                pct.* 
                                from producto_color_tallas as pct
                                where 
                                pct.producto_id = ? 
                                and pct.color_id = ? 
                                and pct.talla_id = ?
                                and pct.almacen_id = ?',
                                [$item->producto_id,
                                $item->color_id,
                                $item->talla_id,
                                $traslado->almacen_destino_id]);
                        
                //==================== KARDEX ==================
                $kardex                     =   new Kardex();
                $kardex->sede_id            =   $traslado->sede_destino_id;
                $kardex->almacen_id         =   $traslado->almacen_destino_id;
                $kardex->producto_id        =   $item->producto_id;
                $kardex->color_id           =   $item->color_id;
                $kardex->talla_id           =   $item->talla_id;
                $kardex->almacen_nombre     =   $almacen_destino->descripcion;
                $kardex->producto_nombre    =   $item->producto_nombre;
                $kardex->color_nombre       =   $item->color_nombre;
                $kardex->talla_nombre       =   $item->talla_nombre;
                $kardex->cantidad           =   $item->cantidad;
                $kardex->precio             =   null;
                $kardex->importe            =   null;
                $kardex->accion             =   'TRASLADO INGRESO';
                $kardex->stock              =   $producto[0]->stock;
                $kardex->numero_doc         =   'TR-'.$traslado->id;
                $kardex->documento_id       =   $traslado->id;
                $kardex->registrador_id     =   Auth::user()->id;
                $kardex->registrador_nombre =   Auth::user()->usuario;
                $kardex->fecha              =   Carbon::today()->toDateString();
                $kardex->descripcion        =   mb_strtoupper("TRASLADO INGRESO", 'UTF-8');
                $kardex->save();

            }

            $traslado->estado           =   'RECIBIDO';
            $traslado->aprobador_id     =   Auth::user()->id;
            $traslado->aprobador_nombre =   Auth::user()->usuario;
            $traslado->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>"TRASLADO RECIBIDO CON ÉXITO!!!"]);


        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>true,'message'=>$th->getMessage()]);
        }
         
    }

    public function show($traslado_id){
      
        $detalle            =   TrasladoDetalle::where('traslado_id',$traslado_id)->get();
        $traslado           =   Traslado::find($traslado_id);
        $tallas             =   Talla::where('estado','ACTIVO')->get();
        $almacen_origen     =   Almacen::find($traslado->almacen_origen_id);
        $almacen_destino    =   Almacen::find($traslado->almacen_destino_id);

        return view('almacenes.solicitudes_traslado.show',compact('detalle','traslado','tallas','almacen_origen','almacen_destino'));
    }

    public function generarEtiquetas($id){

        try {
          
            $traslado_detalle   =   DB::select('select 
                                    p.nombre as producto_nombre,
                                    c.descripcion as color_nombre,
                                    t.descripcion as talla_nombre,
                                    m.descripcion as modelo_nombre,
                                    cb.ruta_cod_barras,
                                    td.cantidad,
                                    p.id as producto_id,
                                    c.id as color_id,
                                    t.id as talla_id,
                                    m.id as modelo_id,
                                    ca.descripcion as categoria_nombre
                                    from traslados_detalle as td
                                    inner join productos as p on p.id = td.producto_id
                                    inner join colores as c on c.id = td.color_id
                                    inner join tallas as t on t.id = td.talla_id
                                    inner join modelos as m on m.id = p.modelo_id
                                    inner join categorias as ca on ca.id = p.categoria_id
                                    left join codigos_barra as cb on (cb.producto_id = p.id and cb.color_id = c.id and cb.talla_id = t.id)
                                    where td.traslado_id = ?',
                                    [$id]);

            
            $empresa        =   Empresa::first();
          
            
            $width_in_points    = 300 * 72 / 25.4;  // Ancho en puntos 5cm = 50 mm
            $height_in_points   = 170 * 72 / 25.4; // Alto en puntos
                                
            // Establecer el tamaño del papel
            $custom_paper = array(0, 0, $width_in_points, $height_in_points);
            $pdf = PDF::loadview('almacenes.productos.pdf.adhesivo', [
                                    'nota_id'       =>  $id,
                                    'nota_detalle'  =>  $traslado_detalle,
                                    'empresa'       =>  $empresa
                                    ])->setPaper($custom_paper)
                                    ->setWarnings(false);
                             
            return $pdf->stream('etiquetas.pdf');
        } catch (\Throwable $th) {
            dd($th->getMessage());
         

            return redirect()->route('almacenes.traslados.index');
        }
       
    }

}
