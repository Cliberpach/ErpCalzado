<?php

namespace App\Http\Controllers\Reportes;

use App\Almacenes\Almacen;
use App\Almacenes\CodigoBarra;
use App\Almacenes\DetalleNotaIngreso;
use App\Almacenes\DetalleNotaSalidad;
use App\Almacenes\NotaIngreso;
use App\Almacenes\Producto;
use App\Compras\Documento\Detalle;
use App\Http\Controllers\Controller;
use App\Ventas\Documento\Detalle as DocumentoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\Reportes\PI\Producto_PI;
use Maatwebsite\Excel\Facades\Excel;
use App\Ventas\Nota;
use App\Ventas\NotaDetalle;
use App\Mantenimiento\Empresa\Empresa;
use Barryvdh\DomPDF\Facade as PDF;
use App\Almacenes\ProductoColorTalla;
use App\Mantenimiento\Sedes\Sede;
use Carbon\Carbon;

class ProductoController extends Controller
{
    public function informe()
    {   
        $sedes          =   Sede::where('estado','ACTIVO')->get();
        $almacenes      =   Almacen::where('estado','ACTIVO')->get();

        return view('reportes.almacenes.producto.informe',compact('sedes','almacenes'));
    }

    public function getTable()
    {
        return datatables()->query(
            DB::table('productos')
                ->join('marcas', 'productos.marca_id', '=', 'marcas.id')
                ->join('almacenes', 'almacenes.id', '=', 'productos.almacen_id')
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->select('categorias.descripcion as categoria', 'almacenes.descripcion as almacen', 'marcas.marca', 'productos.*')
                ->orderBy('productos.id', 'ASC')
                ->where('productos.estado', 'ACTIVO')
        )->toJson();
    }

    public function llenarCompras($producto_id,$color_id,$talla_id)
    {
        $compras = Detalle::where('producto_id', $producto_id)
        ->where('estado', 'ACTIVO')
        ->where('color_id', $color_id)
        ->where('talla_id', $talla_id)
        ->orderBy('id', 'desc')->get();
        $coleccion = collect([]);
        foreach ($compras as $producto) {
            $coleccion->push([
                'proveedor'     => $producto->documento->proveedor->descripcion,
                'documento'     => $producto->documento->tipo_compra,
                'numero'        => $producto->documento->serie_tipo . '-' . $producto->documento->numero_tipo,
                'fecha_emision' => $producto->documento->fecha_emision,
                'cantidad'      => $producto->cantidad,
                'precio_doc'    => number_format($producto->precio_soles, 2),
                'costo_flete'   => number_format($producto->costo_flete, 2),
                'precio_compra' => number_format($producto->precio_soles + $producto->costo_flete, 2),                
                // 'fecha_vencimiento' => $producto->fecha_vencimiento,
                // 'medida'            => $producto->producto->medidaCompleta(),
                // 'lote' => $producto->lote,
            ]);
        }
        return DataTables::of($coleccion)->make(true);
    }

    public function llenarVentas($almacen_id,$producto_id,$color_id,$talla_id)
    {
        ini_set('memory_limit', '1024M');
        try{
            $ventas =   DB::table('cotizacion_documento_detalles as cdd')
                        ->join('cotizacion_documento as cd','cd.id','cdd.documento_id')
                        ->join('users as u','u.id','cd.user_id')
                        ->select(
                            DB::raw("CONCAT(cd.tipo_documento_cliente,':',cd.documento_cliente,'-',cd.cliente) as cliente"),
                            'u.usuario as registrador_nombre',
                            'cd.tipo_venta_nombre as documento',
                            DB::raw("CONCAT(cd.serie,'-',cd.correlativo) as serie"),
                            'cd.created_at as fecha',
                            DB::raw("FLOOR(cdd.cantidad) as cantidad"),
                            'cdd.precio_unitario_nuevo',
                            'cd.convert_en_serie'
                        )
                        ->where('cdd.estado', 'ACTIVO')
                        ->where("cdd.producto_id",$producto_id)
                        ->where("cdd.color_id",$color_id)
                        ->where("cdd.talla_id",$talla_id)
                        ->get();
          
          
            return DataTables::of($ventas)->make(true);
        }catch(\Exception $ex){
            dd($ex->getMessage());
            return response()->json([
                "data"=> [],
                "draw"=> 0,
                "input"=>"1664832061783",
                "recordsFiltered"=>0,
                "recordsTotal"=> 0,
                "ex"=>$ex
            ]);
        }
    }

    public function llenarNotasCredito($producto_id,$color_id,$talla_id)
    {
        ini_set('memory_limit', '1024M');
        
        try{
            $detalle_notas_credito = NotaDetalle::orderBy('id', 'desc')
            ->where("producto_id",$producto_id)
            ->where("color_id",$color_id)
            ->where("talla_id",$talla_id)
            ->get();
          
            $coleccion = collect([]);
            foreach ($detalle_notas_credito as $producto) {
                $coleccion->push([
                    'cliente'               =>  $producto->nota_dev->cliente,
                    'usuario'               =>  $producto->nota_dev->user->usuario,
                    'doc_afec'              =>  $producto->nota_dev->numDocfectado,
                    'fecha_emision'         =>  $producto->nota_dev->fecha_atencion,
                    'numero'                =>  $producto->nota_dev->serie.'-'.$producto->nota_dev->correlativo,
                    'fecha_emision'         =>  $producto->nota_dev->fechaEmision,
                    'cantidad'              =>  $producto->cantidad,
                    'precio_unitario_nuevo' =>  $producto->mtoPrecioUnitario,
                    'motivo'                =>  $producto->nota_dev->desMotivo    
                ]);
            }
            return DataTables::of($coleccion)->make(true);
        }catch(\Exception $ex){
            dd($ex->getMessage());
            return response()->json([
                "data"=> [],
                "draw"=> 0,
                "input"=>"1664832061783",
                "recordsFiltered"=>0,
                "recordsTotal"=> 0,
                "ex"=>$ex
            ]);
        }
    }

    public function llenarSalidas($almacen_id,$producto_id,$color_id,$talla_id)
    {
        $salidas =  DB::table('detalle_nota_salidad as dns')
                    ->join('nota_salidad as ns', 'ns.id', '=', 'dns.nota_salida_id')
                    ->select(
                        DB::raw('CONCAT("NS-",ns.id) as codigo'),
                        'ns.almacen_origen_nombre',
                        'ns.almacen_destino_nombre',
                        'dns.cantidad',
                        'ns.registrador_nombre',
                        'ns.created_at as fecha',
                    )
                    ->where('dns.almacen_id', $almacen_id)  
                    ->where('dns.producto_id', $producto_id)  
                    ->where('dns.color_id', $color_id)    
                    ->where('dns.talla_id', $talla_id)      
                    ->where('ns.estado', '!=', 'ANULADO')
                    ->get();

       
        return DataTables::of($salidas)->make(true);
    }

    public function llenarTrasladoSalida($almacen_id,$producto_id,$color_id,$talla_id)
    {
        $salidas =  DB::table('traslados_detalle as td')
                    ->join('traslados as t', 't.id', '=', 'td.traslado_id')
                    ->join('almacenes as a','a.id','t.almacen_origen_id')
                    ->join('almacenes as ad','ad.id','t.almacen_origen_id')
                    ->select(
                        DB::raw('CONCAT("TR-",t.id) as codigo'),
                        'a.descripcion as almacen_origen_nombre',
                        'ad.descripcion as almacen_destino_nombre',
                        'td.cantidad',
                        't.registrador_nombre',
                        't.created_at as fecha',
                    )
                    ->where('td.almacen_id', $almacen_id)  
                    ->where('td.producto_id', $producto_id)  
                    ->where('td.color_id', $color_id)    
                    ->where('td.talla_id', $talla_id)      
                    ->where('t.estado', '!=', 'ANULADO')
                    ->get();

       
        return DataTables::of($salidas)->make(true);
    }

    public function llenarIngresos($almacen_id,$producto_id,$color_id,$talla_id)
    {
       
        $ingresos   =   DB::table('detalle_nota_ingreso  as dni')
                        ->join('nota_ingreso as ni', 'dni.nota_ingreso_id', '=', 'ni.id')
                        ->where('almacen_id', $almacen_id)
                        ->where('producto_id', $producto_id)
                        ->where('color_id', $color_id)
                        ->where('talla_id', $talla_id)
                        ->select(
                            'dni.*',
                            DB::raw("CONCAT('NI-',ni.id) as codigo"),
                            'ni.registrador_nombre as usuario',
                            'ni.almacen_destino_nombre as destino',
                            'ni.created_at as fecha'
                        )
                        ->orderByDesc('ni.id')
                        ->get();

     
        return DataTables::of($ingresos)->make(true);
    }

    public function updateIngreso(Request $request)
    {
        DB::beginTransaction();
        $data = $request->all();

        $rules = [
            'id' => 'required',
            'nota_ingreso_id' => 'required',
            'costo' => 'required',

        ];

        $message = [
            'id.required' => 'El id del detalle es obligatorio.',
            'nota_ingreso_id.required' => 'El id de la nota de ingreso es obligatorio.',
            'costo.required' => 'El campo costo es obligatorio.'
        ];

        $validator =  Validator::make($data, $rules, $message);

        if ($validator->fails()) {
            $clase = $validator->getMessageBag()->toArray();
            $cadena = "";
            foreach ($clase as $clave => $valor) {
                $cadena =  $cadena . "$valor[0] ";
            }

            Session::flash('error', $cadena);
            DB::rollBack();
            return redirect()->route('reporte.producto.informe');
        }

        $notaingreso = NotaIngreso::find($request->nota_ingreso_id);
        $dolar = $notaingreso->dolar;
        if ($notaingreso->moneda == 'DOLARES') {
            $costo_soles = (float) $request->costo * (float) $dolar;

            $costo_dolares = (float) $request->costo;
        } else {
            $costo_soles = (float) $request->costo;

            $costo_dolares = (float) $request->costo / (float) $dolar;
        }
        $detalle = DetalleNotaIngreso::findOrFail($request->id);
        $detalle->costo = $request->costo;
        $detalle->costo_soles = $costo_soles;
        $detalle->costo_dolares = $costo_dolares;
        $detalle->valor_ingreso = $request->costo * $detalle->cantidad;
        $detalle->update();

        $notaingreso->total = $notaingreso->detalles->sum('valor_ingreso');
        if ($notaingreso->moneda == 'DOLARES') {
            $notaingreso->total_soles = (float) $notaingreso->detalles->sum('valor_ingreso') * (float) $dolar;

            $notaingreso->total_dolares = (float) $notaingreso->detalles->sum('valor_ingreso');
        } else {
            $notaingreso->total_soles = (float) $notaingreso->detalles->sum('valor_ingreso');

            $notaingreso->total_dolares = (float) $notaingreso->detalles->sum('valor_ingreso') / $dolar;
        }

        $notaingreso->update();

        Session::flash('success', 'Se actualizo correctamente el costo de ingreso.');
        DB::commit();
        return redirect()->route('reporte.producto.informe');
    }

    public function getProductos(Request $request){

        $sede_id    =   $request->get('sede_id');
        $almacen_id =   $request->get('almacen_id');

        $productos  =    DB::table('productos as p')
                        ->join('producto_color_tallas as pct', 'p.id', '=', 'pct.producto_id')
                        ->join('colores as co', 'co.id', '=', 'pct.color_id')
                        ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                        ->join('modelos as m', 'm.id', '=', 'p.modelo_id')
                        ->join('categorias as ca', 'ca.id', '=', 'p.categoria_id')
                        ->join('almacenes as a','a.id','pct.almacen_id')
                        ->select(
                            'a.id as almacen_id',
                            'p.id as producto_id',
                            'co.id as color_id',
                            't.id as talla_id',
                            'p.codigo as producto_codigo',
                            'p.nombre as producto_nombre',
                            'co.descripcion as color_nombre',
                            't.descripcion as talla_nombre',
                            'm.descripcion as modelo_nombre',
                            'ca.descripcion as categoria_nombre', 
                            'pct.stock',
                            'pct.ruta_cod_barras',
                            'pct.codigo_barras',
                            'a.descripcion as almacen_nombre'
                        )
                        ->where('p.estado', '=', 'ACTIVO');  

        if($sede_id){
            $productos->where('a.sede_id',$sede_id);
        }
        if($almacen_id){
            $productos->where('pct.almacen_id',$almacen_id);
        }

        return datatables()->query($productos)->toJson();
    }

    public static function queryProductosPI(Request $request){
        $productos  =   DB::table('producto_color_tallas as pct')
                        ->join('productos as p', 'p.id', '=', 'pct.producto_id')
                        ->join('colores as c', 'c.id', '=', 'pct.color_id')
                        ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                        ->join('modelos as m', 'm.id', '=', 'p.modelo_id')
                        ->join('categorias as ca', 'ca.id', '=', 'p.categoria_id')
                        ->join('almacenes as a','a.id','pct.almacen_id')
                        ->join('empresa_sedes as es','es.id','a.sede_id')
                        ->where('p.estado','ACTIVO')
                        ->select(
                            'es.nombre as sede',
                            'a.descripcion as almacen',
                            'p.nombre as producto',
                            'c.descripcion as color',
                            't.descripcion as talla',
                            'm.descripcion as modelo',
                            'ca.descripcion as categoria',
                            'pct.stock'
                        )
                        ->orderBy('es.nombre')
                        ->orderBy('a.descripcion')
                        ->orderBy('p.nombre')
                        ->orderBy('c.descripcion')
                        ->orderBy('t.descripcion');

        if($request->get('sedeId')){
            $productos      =   $productos->where('a.sede_id',$request->get('sedeId'));
        }

        if($request->get('almacenId')){
            $productos      =   $productos->where('pct.almacen_id',$request->get('almacenId'));
        }
                
        return $productos->get();
    }

    public function excelProductos(Request $request){
        
        $productos      =  $this->queryProductosPI($request);

        $sede_nombre    =   null;
        $almacen_nombre =   null;
        if($request->get('sedeId')){
            $sede_nombre    =   Sede::find($request->get('sedeId'))->nombre;
        }
        if($request->get('almacenId')){
            $almacen_nombre =   Almacen::find($request->get('almacenId'))->descripcion;
        }

        $empresa    =   Empresa::find(1);

        $request->merge(['sede_nombre'=>$sede_nombre,'almacen_nombre'=>$almacen_nombre]);

        return Excel::download(new Producto_PI($productos,$request,$empresa), 'productosPI_' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function obtenerBarCode(Request $request){

        //======= REVIZANDO SI TIENE O NO CODIGO DE BARRAS ========
        try {
            
            $producto_id        =   $request->get('producto_id');
            $color_id           =   $request->get('color_id');
            $talla_id           =   $request->get('talla_id');

            $producto           =   DB::select('select 
                                    pct.producto_id,
                                    pct.color_id,
                                    pct.talla_id,
                                    p.nombre as producto_nombre,
                                    c.descripcion as color_nombre,
                                    t.descripcion as talla_nombre,
                                    m.descripcion as modelo_nombre,
                                    cb.ruta_cod_barras,
                                    cb.codigo_barras,
                                    pct.stock,
                                    pct.stock_logico
                                    from producto_color_tallas as pct
                                    inner join productos as p on p.id = pct.producto_id
                                    inner join colores as c on c.id = pct.color_id
                                    inner join tallas as t on t.id = pct.talla_id
                                    inner join modelos as m on m.id = p.modelo_id
                                    left join codigos_barra as cb on (cb.producto_id = p.id AND cb.color_id = c.id AND cb.talla_id = t.id)
                                    WHERE 
                                    pct.producto_id = ? 
                                    AND pct.color_id = ?  
                                    AND pct.talla_id = ?',
                                    [$producto_id,
                                    $color_id,
                                    $talla_id])[0];

            
            
            $message    =   'VISUALIZANDO CÓDIGO DE BARRAS';
            if(!$producto->codigo_barras && !$producto->ruta_cod_barras){
                $res_generarBarCode     =   $this->generarCodigoBarras($producto);

                if(!$res_generarBarCode['success']){
                    return response()->json(['success'=>false,'message'=>$res_generarBarCode['message'],
                    'exception'=>$res_generarBarCode['exception']]);
                }

                $producto->codigo_barras    =   $res_generarBarCode['codigo_barras'];
                $producto->ruta_cod_barras  =   $res_generarBarCode['ruta_cod_barras'];    
                $message                    =   $res_generarBarCode['message'];
            }


            return response()->json(['success'=>true,'producto'=>$producto,'message'=>$message]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>'ERROR EN EL SERVIDOR AL OBTENER EL CÓDIGO DE BARRAS',
            'exception'=>$th->getMessage()]);
        }
      
    }

    public function getAdhesivos($producto_id,$color_id,$talla_id){
        try {
            $producto           =   DB::select('select 
                                    pct.producto_id,
                                    pct.color_id,
                                    pct.talla_id,
                                    m.id as modelo_id,
                                    p.nombre as producto_nombre,
                                    c.descripcion as color_nombre,
                                    t.descripcion as talla_nombre,
                                    m.descripcion as modelo_nombre,
                                    cb.ruta_cod_barras,
                                    cb.codigo_barras,
                                    pct.stock as cantidad,
                                    ca.descripcion as categoria_nombre
                                    from producto_color_tallas as pct
                                    inner join productos as p on p.id = pct.producto_id
                                    inner join colores as c on c.id = pct.color_id
                                    inner join tallas as t on t.id = pct.talla_id
                                    inner join modelos as m on m.id = p.modelo_id
                                    inner join categorias as ca on ca.id = p.categoria_id
                                    left join codigos_barra as cb on (cb.producto_id = p.id and cb.color_id = c.id and cb.talla_id = t.id)
                                    WHERE 
                                    pct.producto_id = ? 
                                    AND pct.color_id = ?  
                                    AND pct.talla_id = ?',
                                    [$producto_id,
                                    $color_id,
                                    $talla_id])[0];

            $empresa            =   Empresa::first();
          
            $width_in_points    =   300 * 72 / 25.4;  // 5 cm = 50 mm
            $height_in_points   =   170 * 72 / 25.4; 
                                
            // Establecer el tamaño del papel
            $custom_paper = array(0, 0, $width_in_points, $height_in_points);
            $pdf = PDF::loadview('reportes.almacenes.producto.pdf.adhesivo', [
                                    'producto'      =>  $producto,
                                    'empresa'       =>  $empresa
                                    ])->setPaper($custom_paper);

            return $pdf->stream('etiquetas.pdf');
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }


    public function generarCodigoBarras($producto){
        DB::beginTransaction();

        try {
            //========= GENERAR IDENTIFICADOR ÚNICO PARA EL COD BARRAS ========
            $key            =   generarCodigo(8);
            //======== GENERAR IMG DEL COD BARRAS ========
            $generatorPNG   =   new \Picqer\Barcode\BarcodeGeneratorPNG();
            $code           =   $generatorPNG->getBarcode($key, $generatorPNG::TYPE_CODE_128);
            $name           =   $key.'.png';
        
            if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'))) {
                mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'));
            }
        
            $pathToFile = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'.DIRECTORY_SEPARATOR.$name);
        
            file_put_contents($pathToFile, $code);

            //======== GUARDAR KEY Y RUTA IMG ========
            $codigoBarra                    = new CodigoBarra();
            $codigoBarra->producto_id       = $producto->producto_id;
            $codigoBarra->color_id          = $producto->color_id;
            $codigoBarra->talla_id          = $producto->talla_id;
            $codigoBarra->codigo_barras     = $key;
            $codigoBarra->ruta_cod_barras   = 'public/productos/' . $name;
            $codigoBarra->save();


            DB::commit();
            return ['success'=>true,
            'message'=>"CÓDIGO DE BARRAS GENERADO, EL PRODUCTO NO CONTABA CON UNO",
            'codigo_barras'=>$key,
            'ruta_cod_barras'=>'public/productos/'.$name];
            
        } catch (\Throwable $th) {
            DB::rollback();
           return ['success'=>false,'message'=>"ERROR AL GENERAR CÓDIGO DE BARRAS",'exception'=>$th->getMessage()];
        }     
    }


}
