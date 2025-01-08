<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use App\Almacenes\Marca;
use App\Almacenes\Modelo;
use App\Almacenes\Color;
use App\Almacenes\Talla;
use App\Almacenes\ProductoColor;
use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Producto;
use App\Almacenes\ProductoDetalle;
use App\Almacenes\TipoCliente;
use App\Exports\Producto\CodigoBarra;
use App\Exports\Producto\ProductosExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Almacen\Producto\ProductoStoreRequest;
use App\Http\Requests\Almacen\Producto\ProductoUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','producto.index');
        $colores = DB::select('select c.id as color_id,c.descripcion as color_nombre,
                    p.id as producto_id,p.nombre as producto_nombre 
                    from producto_colores as pc
                    inner join colores as c on c.id=pc.color_id
                    inner join productos as p on p.id=pc.producto_id
                    where c.estado="ACTIVO" and p.estado="ACTIVO" ');

        $tallas = Talla::where('estado', 'ACTIVO')->get();

        $stocks = ProductoColorTalla::join('colores', 'producto_color_tallas.color_id', '=', 'colores.id')
        ->join('tallas', 'producto_color_tallas.talla_id', '=', 'tallas.id')
        ->select('producto_color_tallas.*')
        ->where('colores.estado', 'ACTIVO')
        ->where('tallas.estado', 'ACTIVO')
        ->get();

  

        return view('almacenes.productos.index',compact('colores','tallas','stocks'));
    }

    public function getTable()
    {
        $this->authorize('haveaccess','producto.index');

         return datatables()->query(
             DB::table('productos')
             ->join('marcas','productos.marca_id','=','marcas.id')
             ->join('almacenes','almacenes.id','=','productos.almacen_id')
             ->join('categorias','categorias.id','=','productos.categoria_id')
             ->join('tabladetalles','tabladetalles.id','=','productos.medida')
             ->join('modelos','modelos.id','=','productos.modelo_id')
             ->select('categorias.descripcion as categoria','almacenes.descripcion as almacen','modelos.descripcion as modelo','marcas.marca','productos.*')
             ->orderBy('productos.id','DESC')
             ->where('productos.estado', 'ACTIVO')
         )->toJson();    
    }

    public function create()
    {
        $this->authorize('haveaccess','producto.index');
        $marcas = Marca::where('estado', 'ACTIVO')->get();
        $almacenes = Almacen::where('estado', 'ACTIVO')->get();
        $categorias = Categoria::where('estado', 'ACTIVO')->get();
        $modelos = Modelo::where('estado', 'ACTIVO')->get();
        $colores = Color::where('estado', 'ACTIVO')->get();
        $tallas = Talla::where('estado', 'ACTIVO')->get();


        return view('almacenes.productos.create', compact('marcas', 'categorias','almacenes','modelos','colores','tallas'));
    }

    public function store(ProductoStoreRequest $request)
    {
     
        $this->authorize('haveaccess','producto.index');
        $data = $request->all();
      
        DB::beginTransaction();

        try {
            
            //======= GUARDANDO PRODUCTO =======
            $producto                   =   new Producto();
            $producto->codigo           =   $request->get('codigo');
            $producto->codigo_barra     =   $request->get('codigo_barra');
            $producto->nombre           =   $request->get('nombre');
            $producto->marca_id         =   $request->get('marca');
            $producto->almacen_id       =   $request->get('almacen');
            $producto->categoria_id     =   $request->get('categoria');
            $producto->modelo_id        =   $request->get('modelo');
            $producto->medida           =   $request->get('medida');
            $producto->precio_venta_1   =   $request->get('precio1');
            $producto->precio_venta_2   =   $request->get('precio2');
            $producto->precio_venta_3   =   $request->get('precio3');
            $producto->costo            =   $request->get('costo')?$request->get('costo'):0;  
            $producto->save();
           

            //======= GUARDAMOS LOS COLORES ASIGNADOS AL PRODUCTO ========
            $coloresAsignados = json_decode($request->get('coloresJSON'));

            foreach ($coloresAsignados as $color_id) {
                $producto_color                 =   new ProductoColor();
                $producto_color->producto_id    =   $producto->id;
                $producto_color->color_id       =   $color_id;
                $producto_color->save();     
            }


            $producto->codigo = 1000 + $producto->id;
            $producto->update();

    
            //Registro de actividad
            $descripcion = "SE AGREGÓ EL PRODUCTO CON LA DESCRIPCION: ". $producto->nombre;
            $gestion = "PRODUCTO";
            crearRegistro($producto, $descripcion , $gestion);

            DB::commit();
            Session::flash('success','Producto creado.');
            return redirect()->route('almacenes.producto.index')->with('guardar', 'success');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
             
    }

    public function edit($id)
    {

        $this->authorize('haveaccess','producto.index');
        $producto   =   Producto::findOrFail($id);
        $marcas     =   Marca::where('estado', 'ACTIVO')->get();
        $clientes   =   TipoCliente::where('estado','ACTIVO')->where('producto_id',$id)->get();
        $categorias =   Categoria::where('estado', 'ACTIVO')->get();
        $almacenes  =   Almacen::where('estado', 'ACTIVO')->get();
        $modelos    =   Modelo::where('estado','ACTIVO')->get();
        $colores    =   Color::where('estado','ACTIVO')->get();
        $tallas     =   Talla::where('estado','ACTIVO')->get();
        $colores_asignados     =   DB::select('select * from producto_colores as pc
                        inner join colores  as c on c.id = pc.color_id
                        where c.estado = "ACTIVO" and pc.producto_id = ?',
                        [$id]);       

        return view('almacenes.productos.edit', [
            'producto' => $producto,
            'marcas' => $marcas,
            'clientes' => $clientes,
            'categorias' => $categorias,
            'almacenes' => $almacenes,
            'modelos' => $modelos,
            'colores' => $colores,
            'tallas'   => $tallas,
            'colores_asignados'   => $colores_asignados,
        ]);
    }

    public function update(ProductoUpdateRequest $request, $id)
    {

        $this->authorize('haveaccess','producto.index');

        DB::beginTransaction();
        
        try {
            $producto                   =   Producto::findOrFail($id);
            $producto->codigo           =   $request->get('codigo');
            $producto->nombre           =   $request->get('nombre');
            $producto->marca_id         =   $request->get('marca');
            $producto->almacen_id       =   $request->get('almacen');
            $producto->categoria_id     =   $request->get('categoria');
            $producto->modelo_id        =   $request->get('modelo');
            $producto->precio_venta_1   =   $request->get('precio1');
            $producto->precio_venta_2   =   $request->get('precio2');
            $producto->precio_venta_3   =   $request->get('precio3');
            $producto->medida           =   $request->get('medida');
            $producto->codigo_barra     =   $request->get('codigo_barra');
            $producto->costo            =   $request->get('costo')?$request->get('costo'):0;  
            // $producto->peso_producto = $request->get('peso_producto') ? $request->get('peso_producto') : 0;
            // $producto->stock_minimo = $request->get('stock_minimo');
            // $producto->precio_venta_minimo = $request->get('precio_venta_minimo');
            // $producto->precio_venta_maximo = $request->get('precio_venta_maximo');
            // $producto->igv = $request->get('igv');
            // $producto->peso_producto = $request->get('peso_producto');
            // $producto->facturacion = $request->get("facturacion_producto");
            $producto->update();

            // if($request->get('codigo_barra'))
            // {
            //     $generatorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();
            //     $code = base64_encode($generatorPNG->getBarcode($request->get('codigo_barra'), $generatorPNG::TYPE_CODE_128));
            //     $data_code = base64_decode($code);
            //     $name =  $producto->codigo_barra.'.png';

            //     if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'))) {
            //         mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'));
            //     }

            //     $pathToFile = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'.DIRECTORY_SEPARATOR.$name);

            //     file_put_contents($pathToFile, $data_code);
            // }

            //=========== EDITAMOS LOS COLORES DEL PRODUCTO ==========
            $coloresNuevos = json_decode($request->get('coloresJSON'));//['A','C']     ['A','R','C']  ['A','B']      
            

            //===== OBTENIENDO COLORES ANTERIORES DEL PRODUCTO ===== //['A','R','C']     ['A','C']   ['A','B']
            $colores_anteriores =   DB::select('select pc.producto_id as producto_id, 
                                    pc.color_id as color_id
                                    from producto_colores as pc
                                    where pc.producto_id=?',[$id]);

            $collection_colores_anteriores  =   collect($colores_anteriores);   
            $collection_colores_nuevos      =   collect($coloresNuevos);   

            $ids_colores_anteriores = $collection_colores_anteriores->pluck('color_id')->toArray();
            $ids_colores_nuevos = $collection_colores_nuevos->toArray();

            //===== CASO I: COLORES DE LA LISTA ANTERIOR NO ESTÁN EN LA LISTA NUEVA =====
            //===== DEBEN DE ELIMINARSE =====
            $colores_diferentes_1 = array_diff($ids_colores_anteriores, $ids_colores_nuevos);
            foreach ($colores_diferentes_1 as $key => $value) {
                //==== ELIMINANDO COLORES ======
                DB::table('producto_colores')
                ->where('producto_id', $id)
                ->where('color_id', $value)
                ->delete();
                //===== ELIMINANDO TALLAS DEL COLOR =====
                DB::table('producto_color_tallas')
                ->where('producto_id', $id)
                ->where('color_id', $value)
                ->delete();
            }

            //======== CASO II: COLORES DE LA LISTA NUEVA NO ESTÁN EN LA LISTA ANTERIOR ======
            //===== DEBEN REGISTRARSE =====
            $colores_diferentes_2 = array_diff($ids_colores_nuevos, $ids_colores_anteriores);
            foreach ($colores_diferentes_2 as $key => $value) {
                //==== REGISTRANDO COLORES ======
                $producto_color                 =  new ProductoColor();
                $producto_color->producto_id    =   $id;
                $producto_color->color_id       =   $value;
                $producto_color->save(); 
            }
                     
      

            //Registro de actividad
            $descripcion = "SE MODIFICÓ EL PRODUCTO CON LA DESCRIPCION: ". $producto->nombre;
            $gestion = "PRODUCTO";
            modificarRegistro($producto, $descripcion , $gestion);

            Session::flash('success','Producto modificado.');
            DB::commit();
            return redirect()->route('almacenes.producto.index')->with('guardar', 'success');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
   
    }

    public function show($id)
    {
        $this->authorize('haveaccess','producto.index');
        $producto = Producto::findOrFail($id);
        $clientes = TipoCliente::where('estado','ACTIVO')->where('producto_id',$id)->get();
        return view('almacenes.productos.show', [
            'producto' => $producto,
            'clientes' => $clientes,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess','producto.index');
        $producto = Producto::findOrFail($id);
        $producto->estado = 'ANULADO';
        $producto->update();

        //========== ANULAMOS PRODUCTO COLORES Y PRODUCTO COLOR TALLAS =========
        DB::table('producto_colores')
        ->where('producto_id', $id)
        ->update([
            "estado"        =>  'ANULADO',
            "updated_at"    =>  Carbon::now()
        ]);

        DB::table('producto_color_tallas')
        ->where('producto_id', $id)
        ->update([
            "estado"        =>  'ANULADO',
            "updated_at"    =>  Carbon::now()
        ]);

        // $producto->detalles()->update(['estado'=> 'ANULADO']);

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL PRODUCTO CON LA DESCRIPCION: ". $producto->nombre;
        $gestion = "PRODUCTO";
        eliminarRegistro($producto, $descripcion , $gestion);

        Session::flash('success','Producto eliminado.');
        return redirect()->route('almacenes.producto.index')->with('eliminar', 'success');
    }

    public function destroyDetalle(Request $request)
    {
        $data = $request->all();

        $result = 1;
        // if ($data['id']) {
        //     ProductoDetalle::destroy($data['id']);
        //     $result = 1;
        // }

        $data = ['exito' => ($result === 1)];

        return response()->json($data);
    }

    public function getCodigo(Request $request)
    {
        $data = $request->all();
        $codigo = $data['codigo'];
        $id = $data['id'];
        $producto = null;

        if ($codigo && $id) { // edit
            $producto = Producto::where([
                                    ['codigo', $data['codigo']],
                                    ['id', '<>', $data['id']]
                                ])->first();
        } else if ($codigo && !$id) { // create
            $producto = Producto::where('codigo', $data['codigo'])->first();
        }

        $result = ['existe' => ($producto) ? true : false];

        return response()->json($result);
    }

    public function obtenerProducto($id)
    {
        $cliente_producto = DB::table('productos_clientes')
                    ->join('productos', 'productos_clientes.producto_id', '=', 'productos.id')
                    ->where('productos_clientes.estado','ACTIVO')
                    ->where('productos_clientes.producto_id',$id)
                    ->get();

        $producto = Producto::where('id',$id)->where('estado','ACTIVO')->first();

        $resultado = [
                'cliente_producto' => $cliente_producto,
                'producto' => $producto,
            ];
        return $resultado;
    }

    public function productoDescripcion($id)
    {
        $producto = Producto::findOrFail($id);
        return $producto;
    }

    public function getProducto($id)
    {
        $producto = Producto::findOrFail($id);
        return $producto;
    }


    public function getProductos(){
        return datatables()->query(
            DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select('productos.*','categorias.descripcion as categoria')
            ->where('productos.estado','ACTIVO')
        )->toJson();
    }

    public function generarCode()
    {
        return response()->json([
            'code' => generarCodigo(8)
        ]);
    }

    public function codigoBarras($id){
        ob_end_clean();
        ob_start();
        $producto = Producto::find($id);
        return  Excel::download(new CodigoBarra($producto), $producto->codigo_barra.'.xlsx');
    }

    public function getExcel()
    {
        ob_end_clean(); // this
        ob_start();
        return  Excel::download(new ProductosExport, 'productos.xlsx');
    }

    public function getProductosByModelo($modelo_id){

        $stocks =  DB::select('select p.id as producto_id, p.nombre as producto_nombre,
                                    p.precio_venta_1,p.precio_venta_2,p.precio_venta_3,
                                    pct.color_id,c.descripcion as color_name,
                                    pct.talla_id,t.descripcion as talla_name,pct.stock,
                                    pct.stock_logico
                                    from producto_color_tallas as pct
                                    inner join productos as p
                                    on p.id = pct.producto_id
                                    inner join colores as c
                                    on c.id = pct.color_id
                                    inner join tallas as t
                                    on t.id = pct.talla_id
                                    where p.modelo_id=? AND c.estado="ACTIVO" AND t.estado="ACTIVO"
                                    AND p.estado="ACTIVO" 
                                    order by p.id,c.id,t.id',[$modelo_id]);

        $producto_colores = DB::select('select p.id as producto_id,p.nombre as producto_nombre,
                                        c.id as color_id, c.descripcion as color_nombre,
                                        p.precio_venta_1,p.precio_venta_2,p.precio_venta_3
                                        from producto_colores as pc
                                        inner join productos as p
                                        on p.id = pc.producto_id
                                        inner join colores as c
                                        on c.id = pc.color_id
                                        where p.modelo_id = ? AND c.estado="ACTIVO" 
                                        AND p.estado="ACTIVO"
                                        group by p.id,p.nombre,c.id,c.descripcion,
                                        p.precio_venta_1,p.precio_venta_2,p.precio_venta_3
                                        order by p.id,c.id',[$modelo_id]);

        $productosProcesados=[];
        foreach ($producto_colores as $pc) {
             if(!in_array($pc->producto_id, $productosProcesados)){
                 $pc->printPreciosVenta=TRUE;
                 array_push($productosProcesados, $pc->producto_id);
             }else{
                 $pc->printPreciosVenta=FALSE;
             }
        }

        return response()->json(["message" => "success" , "stocks" => $stocks 
                                ,"producto_colores" => $producto_colores ]);
    }

    public function getStockLogico($producto_id,$color_id,$talla_id){

        try {

            $stock_logico = DB::select('
                SELECT pct.stock_logico 
                FROM producto_color_tallas as pct
                WHERE pct.producto_id = ? AND pct.color_id = ? AND pct.talla_id = ?',
                [$producto_id, $color_id, $talla_id]
            );


            return response()->json(["message" => "success", "data" => $stock_logico]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Error al obtener el stock lógico", "error" => $e->getMessage()], 500);
        }                    
    }


}
