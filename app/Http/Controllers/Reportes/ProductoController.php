<?php

namespace App\Http\Controllers\Reportes;

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

class ProductoController extends Controller
{
    public function informe()
    {
        return view('reportes.almacenes.producto.informe');
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

    public function llenarVentas($producto_id,$color_id,$talla_id)
    {
        ini_set('memory_limit', '1024M');
        try{
            $ventas = DocumentoDetalle::orderBy('id', 'desc')
            ->where('estado', 'ACTIVO')
            ->where("producto_id",$producto_id)
            ->where("color_id",$color_id)
            ->where("talla_id",$talla_id)
            ->get();
          
            $coleccion = collect([]);
            foreach ($ventas as $producto) {
                $coleccion->push([
                    'cliente'               =>  $producto->documento->clienteEntidad->nombre,
                    'documento'             =>  $producto->documento->nombreTipo(),
                    'numero'                =>  $producto->documento->serie . '-' . $producto->documento->correlativo,
                    'fecha_emision'         =>  $producto->documento->fecha_atencion,
                    'cantidad'              =>  $producto->cantidad,
                    'precio_unitario_nuevo' =>  $producto->precio_unitario_nuevo,
                    'convertir'             =>  $producto->documento->doc_convertido(),
                    'usuario'               =>  $producto->documento->usuario()    
                    // 'fecha_vencimiento' => $producto->documento->fecha_vencimiento,
                    // 'medida' => $producto->producto->medidaCompleta(),
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
        // try{
        //     $ventas = DocumentoDetalle::orderBy('id', 'desc')
        //     ->where('estado', 'ACTIVO')
        //     ->where("eliminado","0")
        //     ->get();
        //     $coleccion = collect([]);
        //     foreach ($ventas as $producto) {
        //         if ($producto->lote->producto_id == $id) {
        //             $coleccion->push([
        //                 'cliente' => $producto->documento->clienteEntidad->nombre,
        //                 'documento' => $producto->documento->nombreTipo(),
        //                 'numero' => $producto->documento->serie . '-' . $producto->documento->correlativo,
        //                 'fecha_emision' => $producto->documento->fecha_atencion,
        //                 'cantidad' => $producto->cantidad,
        //                 'precio' => $producto->precio_nuevo,
        //                 'lote' => $producto->lote->codigo_lote,
        //                 'fecha_vencimiento' => $producto->documento->fecha_vencimiento,
        //                 'medida' => $producto->lote->producto->medidaCompleta(),
        //             ]);
        //         }
        //     }
        //     return DataTables::of($coleccion)->make(true);
        // }catch(\Exception $ex){
        //     return response()->json([
        //         "data"=> [],
        //         "draw"=> 0,
        //         "input"=>"1664832061783",
        //         "recordsFiltered"=>0,
        //         "recordsTotal"=> 0,
        //         "ex"=>$ex
        //     ]);
        // }
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

    public function llenarSalidas($producto_id,$color_id,$talla_id)
    {
        $salidas = DB::table('detalle_nota_salidad')
        ->join('nota_salidad', 'nota_salidad.id', '=', 'detalle_nota_salidad.nota_salidad_id')
        ->join('productos', 'productos.id','=', 'detalle_nota_salidad.producto_id')
        ->join('tabladetalles', 'tabladetalles.id','=','productos.medida')
        // ->join('lote_productos', 'lote_productos.id', '=', 'detalle_nota_salidad.lote_id')
        ->select(
            'detalle_nota_salidad.cantidad',
            'nota_salidad.origen',
            'nota_salidad.destino',
            'nota_salidad.fecha',
            'nota_salidad.usuario',
            //'lote_productos.codigo_lote',
            'tabladetalles.descripcion as unidad'
        )
        ->where('detalle_nota_salidad.producto_id', $producto_id)  
        ->where('detalle_nota_salidad.color_id', $color_id)    
        ->where('detalle_nota_salidad.talla_id', $talla_id)      
        ->where('nota_salidad.estado', '!=', 'ANULADO')->get();

        $coleccion = collect([]);
        foreach ($salidas as $salida) {
            $coleccion->push([
                'origen'    =>  $salida->origen,
                'destino'   =>  $salida->destino,
                'cantidad'  =>  $salida->cantidad,
                //'lote' => $salida->codigo_lote,
                'fecha'     =>  $salida->fecha,
                'medida'    =>  $salida->unidad,
                'usuario'   =>  $salida->usuario
            ]);
        }
        return DataTables::of($coleccion)->make(true);
    }

    public function llenarIngresos($producto_id,$color_id,$talla_id)
    {
        $ingresos = DetalleNotaIngreso::orderBy('id', 'desc')
                    ->where('producto_id', $producto_id)
                    ->where('color_id', $color_id)
                    ->where('talla_id', $talla_id)
                    ->join('nota_ingreso', 'detalle_nota_ingreso.nota_ingreso_id', '=', 'nota_ingreso.id')
                    ->select('detalle_nota_ingreso.*', 'nota_ingreso.usuario','nota_ingreso.created_at')
                    ->get();

        $coleccion = collect([]);
        foreach ($ingresos as $ingreso) {
            $coleccion->push([
                'origen'            => $ingreso->nota_ingreso->origen,
                'numero'            => $ingreso->nota_ingreso->numero,
                'destino'           => $ingreso->nota_ingreso->destino,
                'cantidad'          => $ingreso->cantidad,
                'costo'             => $ingreso->costo_soles,
                'nombre'            => $ingreso->producto->nombre,
                'total'             => $ingreso->valor_ingreso,
                'nota_ingreso_id'   => $ingreso->nota_ingreso->id,
                'id'                => $ingreso->id,
                'moneda'            => $ingreso->nota_ingreso->moneda,
                'medida'            => $ingreso->producto->medidaCompleta(),
                'usuario'           => $ingreso->usuario,
                'fecha'             => $ingreso->created_at->format('Y-m-d H:i:s')
                //'medida' => $ingreso->loteProducto->producto->medidaCompleta(),
            ]);
        }
        return DataTables::of($coleccion)->make(true);
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

    public function getProductos(){
        return datatables()->query(
            DB::table('productos')
                ->select('productos.id as producto_id', 'colores.id as color_id', 'tallas.id as talla_id', 'productos.codigo as producto_codigo',
                         'productos.nombre as producto_nombre', 'colores.descripcion as color_nombre', 'tallas.descripcion as talla_nombre',
                         'modelos.descripcion as modelo_nombre', 'categorias.descripcion as categoria_nombre', 'producto_color_tallas.stock',
                         'producto_color_tallas.ruta_cod_barras','producto_color_tallas.codigo_barras')
                ->join('producto_colores', 'productos.id', '=', 'producto_colores.producto_id')
                ->join('producto_color_tallas', function ($join) {
                    $join->on('producto_color_tallas.producto_id', '=', 'producto_colores.producto_id')
                         ->on('producto_color_tallas.color_id', '=', 'producto_colores.color_id');
                })
                ->join('colores', 'colores.id', '=', 'producto_colores.color_id')
                ->join('tallas', 'tallas.id', '=', 'producto_color_tallas.talla_id')
                ->join('modelos', 'modelos.id', '=', 'productos.modelo_id')
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->where('productos.estado', '=', 'ACTIVO')
                // ->orderBy('productos.id', 'asc')
                // ->orderBy('colores.id', 'asc')
                // ->orderBy('tallas.id', 'asc')
        )->toJson();
    }

    public function excelProductos(){
        return Excel::download(new Producto_PI(), 'productos_pi.xlsx');
    }

    public function obtenerBarCode(Request $request){

        //======= REVIZANDO SI TIENE O NO CODIGO DE BARRAS ========
        try {
            $producto_id        =   $request->get('producto_id');
            $color_id           =   $request->get('color_id');
            $talla_id           =   $request->get('talla_id');

            $producto           =   DB::select('select pct.producto_id,pct.color_id,pct.talla_id,
                                    p.nombre as producto_nombre,c.descripcion as color_nombre,t.descripcion as talla_nombre,
                                    m.descripcion as modelo_nombre,pct.ruta_cod_barras,pct.codigo_barras,pct.stock,pct.stock_logico
                                    from producto_color_tallas as pct
                                    inner join productos as p on p.id=pct.producto_id
                                    inner join colores as c on c.id=pct.color_id
                                    inner join tallas as t on t.id=pct.talla_id
                                    inner join modelos as m on m.id=p.modelo_id
                                    where pct.producto_id=? and pct.color_id=?  and pct.talla_id=?',
                                    [$producto_id,$color_id,$talla_id])[0];
            
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
            $producto           =   DB::select('select pct.producto_id,pct.color_id,pct.talla_id,m.id as modelo_id,
                                    p.nombre as producto_nombre,c.descripcion as color_nombre,t.descripcion as talla_nombre,
                                    m.descripcion as modelo_nombre,pct.ruta_cod_barras,pct.codigo_barras,pct.stock as cantidad,
                                    ca.descripcion as categoria_nombre
                                    from producto_color_tallas as pct
                                    inner join productos as p on p.id=pct.producto_id
                                    inner join colores as c on c.id=pct.color_id
                                    inner join tallas as t on t.id=pct.talla_id
                                    inner join modelos as m on m.id=p.modelo_id
                                    inner join categorias as ca on ca.id=p.categoria_id
                                    where pct.producto_id=? and pct.color_id=?  and pct.talla_id=?',
                                    [$producto_id,$color_id,$talla_id])[0];

            $empresa        =   Empresa::first();
          
            $width_in_points    = 80 * 72 / 25.4;  // 5 cm = 50 mm
            $height_in_points   = 50 * 72 / 25.4; 
                                
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
            ProductoColorTalla::where('producto_id', $producto->producto_id)
            ->where('color_id', $producto->color_id)
            ->where('talla_id', $producto->talla_id)
            ->update([
                'codigo_barras'         =>  $key,
                'ruta_cod_barras'       =>  'public/productos/'.$name  
            ]);


            DB::commit();
            return ['success'=>true,'message'=>"CÓDIGO DE BARRAS GENERADO, EL PRODUCTO NO CONTABA CON UNO",'codigo_barras'=>$key,'ruta_cod_barras'=>'public/productos/'.$name];
            
        } catch (\Throwable $th) {
            DB::rollback();
           return ['success'=>false,'message'=>"ERROR AL GENERAR CÓDIGO DE BARRAS",'exception'=>$th->getMessage()];
        }     
    }


}
