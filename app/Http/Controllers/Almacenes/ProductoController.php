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
use App\Exports\Reportes\PI\Producto_PI;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reportes\ProductoController as ReportesProductoController;
use App\Http\Requests\Almacen\Producto\ProductoStoreRequest;
use App\Http\Requests\Almacen\Producto\ProductoUpdateRequest;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Sedes\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess','producto.index');

        $sede_id    =   Auth::user()->sede_id;
        $almacenes  =   Almacen::where('estado','ACTIVO')
                        ->where('sede_id',$sede_id)
                        ->get();
      
        return view('almacenes.productos.index',compact('almacenes'));
    }

    public function getTable()
    {
        $this->authorize('haveaccess','producto.index');

        $productos  =    DB::table('productos')
                        ->join('marcas','productos.marca_id','=','marcas.id')
                        ->join('categorias','categorias.id','=','productos.categoria_id')
                        ->join('modelos','modelos.id','=','productos.modelo_id')
                        ->select(
                            'categorias.descripcion as categoria',
                            'modelos.descripcion as modelo',
                            'marcas.marca',
                            'productos.*')
                        ->orderBy('productos.id','DESC')
                        ->where('productos.estado', 'ACTIVO')
                        ->get();

        return DataTables::of($productos)
        ->make(true);
         
    }

    public function create()
    {
        $this->authorize('haveaccess','producto.index');
        
        $marcas         = Marca::where('estado', 'ACTIVO')->get();
        $categorias     = Categoria::where('estado', 'ACTIVO')->get();
        $modelos        = Modelo::where('estado', 'ACTIVO')->get();
        $colores        = Color::where('estado', 'ACTIVO')->get();
        $tallas         = Talla::where('estado', 'ACTIVO')->get();

        $sede_id    =   Auth::user()->sede->id;
        $almacenes  =   Almacen::where('estado','ACTIVO')   
                        ->where('sede_id',$sede_id)
                        ->get();

        return view('almacenes.productos.create', 
        compact('marcas', 'categorias','almacenes','modelos','colores','tallas'));
    }


/*
array:12 [
  "_token"              => "VwOfqQXxOTEgeJMRf05aOJU6I3o1BGag7yMP7m4D"
  "coloresJSON"         => "["2","3"]"
  "nombre"              => "LUIS DANIEL ALVA LUJAN"
  "categoria"           => "1"
  "marca"               => "1"
  "modelo"              => "1"
  "precio1"             => "1"
  "precio2"             => "2"
  "precio3"             => "3"
  "costo"               => "1"
  "almacen"             => "1"
  "table-colores_length" => "10"
]
*/ 
    public function store(ProductoStoreRequest $request)
    {
     
        $this->authorize('haveaccess','producto.index');
        $data = $request->all();
      
        DB::beginTransaction();

        try {

            //======= GUARDANDO PRODUCTO =======
            $producto                   =   new Producto();
            $producto->nombre           =   $request->get('nombre');
            $producto->marca_id         =   $request->get('marca');
            $producto->categoria_id     =   $request->get('categoria');
            $producto->modelo_id        =   $request->get('modelo');
            $producto->medida           =   105;
            $producto->precio_venta_1   =   $request->get('precio1');
            $producto->precio_venta_2   =   $request->get('precio2');
            $producto->precio_venta_3   =   $request->get('precio3');
            $producto->costo            =   $request->get('costo')?$request->get('costo'):0;  
            $producto->save();
           
            //======= GUARDAMOS LOS COLORES ASIGNADOS AL PRODUCTO ========
            $coloresAsignados = json_decode($request->get('coloresJSON'));

            foreach ($coloresAsignados as $color_id) {
                $almacen_id                     =   $request->get('almacen');

                $producto_color                 =   new ProductoColor();
                $producto_color->almacen_id     =   $almacen_id;
                $producto_color->producto_id    =   $producto->id;
                $producto_color->color_id       =   $color_id;
                $producto_color->save();     
            }

            $producto->codigo = 1000 + $producto->id;
            $producto->update();

            //======= REGISTRO DE ACTIVIDAD ========
            $descripcion = "SE AGREGÓ EL PRODUCTO CON LA DESCRIPCION: ". $producto->nombre;
            $gestion = "PRODUCTO";
            crearRegistro($producto, $descripcion , $gestion);

            DB::commit();
            Session::flash('success','Producto creado.');
            return response()->json(['success'=>true,'message'=>'PRODUCTO REGISTRADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
             
    }

    public function edit($id)
    {

        $this->authorize('haveaccess','producto.index');
        $producto   =   Producto::findOrFail($id);
        $marcas     =   Marca::where('estado', 'ACTIVO')->get();
        $clientes   =   TipoCliente::where('estado','ACTIVO')->where('producto_id',$id)->get();
        $categorias =   Categoria::where('estado', 'ACTIVO')->get();
        $modelos    =   Modelo::where('estado','ACTIVO')->get();
        $colores    =   Color::where('estado','ACTIVO')->get();
        $tallas     =   Talla::where('estado','ACTIVO')->get();

        $sede_id    =   Auth::user()->sede->id;
        $almacenes  =   Almacen::where('estado','ACTIVO')   
                        ->where('sede_id',$sede_id)
                        ->get();

        $colores_asignados  =   DB::select('select 
                                pc.* 
                                from producto_colores as pc
                                inner join colores  as c on c.id = pc.color_id
                                where 
                                c.estado = "ACTIVO" 
                                and pc.producto_id = ?',
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

/*
array:13 [
  "_token"          => "3xqT4tlXrWUKISDONEZ773EikNVHqFjCnWbfVU6K"
  "_method"         => "PUT"
  "coloresJSON"     => "[null,"1","6"]"
  "nombre"          => "PRODUCTO TEST SEDE CENTRAL"
  "categoria"       => "1"
  "marca"           => "1"
  "modelo"          => "1"
  "precio1"         => "1.00"
  "precio2"         => "2.00"
  "precio3"         => "3.00"
  "costo"           => "1.00"
  "almacen"         => "1"
  "table-colores_length" => "10"
]
*/ 
    public function update(ProductoUpdateRequest $request, $id)
    {
        $this->authorize('haveaccess','producto.index');
        
        DB::beginTransaction();
        
        try {

            $producto                   =   Producto::findOrFail($id);
            $producto->nombre           =   $request->get('nombre');
            $producto->marca_id         =   $request->get('marca');
            $producto->categoria_id     =   $request->get('categoria');
            $producto->modelo_id        =   $request->get('modelo');
            $producto->precio_venta_1   =   $request->get('precio1');
            $producto->precio_venta_2   =   $request->get('precio2');
            $producto->precio_venta_3   =   $request->get('precio3');
            $producto->costo            =   $request->get('costo')?$request->get('costo'):0;  
            $producto->update();

            //=========== EDITAMOS LOS COLORES DEL PRODUCTO ==========
            $coloresNuevos = json_decode($request->get('coloresJSON'));//['A','C']     ['A','R','C']  ['A','B']      
            

            //===== OBTENIENDO COLORES ANTERIORES DEL PRODUCTO ALMACÉN ===== //['A','R','C']     ['A','C']   ['A','B']
            $colores_anteriores =   DB::select('select 
                                    pc.producto_id as producto_id, 
                                    pc.color_id as color_id,
                                    pc.almacen_id
                                    from producto_colores as pc
                                    where 
                                    pc.producto_id = ?
                                    and pc.almacen_id = ?',
                                    [$id,
                                    $request->get('almacen')]);

            $collection_colores_anteriores  =   collect($colores_anteriores);   
            $collection_colores_nuevos      =   collect($coloresNuevos);   

            $ids_colores_anteriores = $collection_colores_anteriores->pluck('color_id')->toArray();
            $ids_colores_nuevos     = $collection_colores_nuevos->toArray();

            //===== CASO I: COLORES DE LA LISTA ANTERIOR NO ESTÁN EN LA LISTA NUEVA =====
            //===== DEBEN DE ELIMINARSE =====
            $colores_diferentes_1 = array_diff($ids_colores_anteriores, $ids_colores_nuevos);
            foreach ($colores_diferentes_1 as $key => $value) {
                //==== ELIMINANDO COLORES DEL ALMACÉN ======
                DB::table('producto_colores')
                ->where('producto_id', $id)
                ->where('color_id', $value)
                ->where('almacen_id', $request->get('almacen'))
                ->delete();
                //===== ELIMINANDO TALLAS DEL COLOR DEL ALMACÉN =====
                DB::table('producto_color_tallas')
                ->where('producto_id', $id)
                ->where('color_id', $value)
                ->where('almacen_id', $request->get('almacen'))
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
                $producto_color->almacen_id     =   $request->get('almacen');
                $producto_color->save(); 
            }
                     
            //Registro de actividad
            $descripcion = "SE MODIFICÓ EL PRODUCTO CON LA DESCRIPCION: ". $producto->nombre;
            $gestion = "PRODUCTO";
            modificarRegistro($producto, $descripcion , $gestion);

            Session::flash('success','Producto modificado.');
            DB::commit();
            return response()->json(['success'=>true,'message'=>'PRODUCTO ACTUALIZADO CON ÉXITO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
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
        $sede       =   Sede::find(Auth::user()->sede_id);

        $request    =   new Request(['sedeId'=>$sede->id,'almacenId'=>null,'sede_nombre'=>$sede->nombre]);
        $productos  =   ReportesProductoController::queryProductosPI($request);
      
        $empresa    =   Empresa::find(1);
        
        return Excel::download(new Producto_PI($productos,$request,$empresa), 'productos_' . Carbon::now()->format('Y-m-d') . '.xlsx');
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

    public function getStockLogico($almacen_id,$producto_id,$color_id,$talla_id){

        try {

            $stock_logico = DB::select('
                SELECT pct.stock_logico 
                FROM producto_color_tallas as pct
                WHERE pct.almacen_id = ? AND pct.producto_id = ? AND pct.color_id = ? AND pct.talla_id = ?',
                [$almacen_id,$producto_id, $color_id, $talla_id]
            );


            return response()->json(["message" => "success", "data" => $stock_logico]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Error al obtener el stock lógico", "error" => $e->getMessage()], 500);
        }                    
    }

    public function getColores($almacen_id,$producto_id){
        try {
            
            $producto_colores   =   DB::select('select 
                                    pc.color_id,
                                    c.descripcion as color_nombre
                                    from producto_colores as pc
                                    inner join colores as c on c.id = pc.color_id
                                    where 
                                    pc.producto_id = ?
                                    and pc.almacen_id = ?
                                    and pc.estado = "ACTIVO"',[$producto_id,$almacen_id]);

            return response()->json(['success'=>true,
            'message'   =>  'COLORES DEL PRODUCTO EN ALMACÉN OBTENIDOS',
            'data'      =>  $producto_colores]);
            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getTallas($almacen_id,$producto_id,$color_id){
        try {
            
            $tallas     =   DB::select('select 
                            pct.stock,
                            t.descripcion as talla_nombre
                            from producto_color_tallas as pct
                            inner join tallas as t on t.id = pct.talla_id
                            where 
                            pct.almacen_id = ?
                            and pct.producto_id = ?
                            and pct.color_id = ?
                            and pct.estado = "ACTIVO"',[$almacen_id,$producto_id,$color_id]);

            return response()->json(['success'=>true,
            'message'   =>  'TALLAS DEL PRODUCTO EN ALMACÉN OBTENIDOS',
            'data'      =>  $tallas]);
            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getProductosTodos(Request $request){

        try {
        
            $search         = $request->query('search'); 
            $almacenId      = $request->query('almacen_id'); 
            $page           = $request->query('page', 1);  

            if(!$almacenId){
                throw new Exception("FALTA SELECCIONAR UN ALMACÉN!!!");
            }
        
            $productos  =   DB::table('productos as p')
                            ->join('categorias as c','c.id','p.categoria_id')
                            ->join('marcas as ma','ma.id','p.marca_id')
                            ->join('modelos as mo','mo.id','p.modelo_id')
                            ->leftJoin('producto_color_tallas as pct','p.id','pct.producto_id')
                            ->select(
                            DB::raw("CONCAT(c.descripcion, ' - ', ma.marca, ' - ', mo.descripcion, ' - ', p.nombre) as producto_completo"),
                            'c.descripcion as categoria_nombre',
                            'ma.marca as marca_nombre',
                            'mo.descripcion as modelo_nombre',
                            'p.id as producto_id',
                            'c.id as categoria_id',
                            'ma.id as marca_id',
                            'mo.id as modelo_id',
                            'p.nombre as producto_nombre',
                            'pct.almacen_id',
                            DB::raw('SUM(pct.stock) as stock_total')
                            )
                            ->where(DB::raw("CONCAT(c.descripcion, ' - ', ma.marca, ' - ', mo.descripcion, ' - ', p.nombre)"), 'LIKE', "%$search%") 
                            ->where('pct.almacen_id',$almacenId)
                            ->where('p.estado','ACTIVO')
                            ->groupBy(
                                'pct.almacen_id',
                                'p.id',
                                'c.id',
                                'ma.id',
                                'mo.id',
                                'c.descripcion',
                                'ma.marca',
                                'mo.descripcion',
                                'p.nombre')
                            ->paginate(10, ['*'], 'page', $page); 

            return response()->json([
                'success' => true,
                'message' => 'PRODUCTOS OBTENIDOS',
                'productos' => $productos->items(),
                'more' => $productos->hasMorePages() 
            ]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=> $th->getMessage()]);
        }
    }


    public function getProductosConStock(Request $request){

        try {
        
            $search         = $request->query('search'); 
            $almacenId      = $request->query('almacen_id'); 
            $page           = $request->query('page', 1);  

            if(!$almacenId){
                throw new Exception("FALTA SELECCIONAR UN ALMACÉN!!!");
            }
        
            $productos  =   DB::table('productos as p')
                            ->join('categorias as c','c.id','p.categoria_id')
                            ->join('marcas as ma','ma.id','p.marca_id')
                            ->join('modelos as mo','mo.id','p.modelo_id')
                            ->leftJoin('producto_color_tallas as pct','p.id','pct.producto_id')
                            ->select(
                            DB::raw("CONCAT(c.descripcion, ' - ', ma.marca, ' - ', mo.descripcion, ' - ', p.nombre) as producto_completo"),
                            'c.descripcion as categoria_nombre',
                            'ma.marca as marca_nombre',
                            'mo.descripcion as modelo_nombre',
                            'p.id as producto_id',
                            'c.id as categoria_id',
                            'ma.id as marca_id',
                            'mo.id as modelo_id',
                            'p.nombre as producto_nombre',
                            'pct.almacen_id',
                            DB::raw('SUM(pct.stock) as stock_total')
                            )
                            ->where(DB::raw("CONCAT(c.descripcion, ' - ', ma.marca, ' - ', mo.descripcion, ' - ', p.nombre)"), 'LIKE', "%$search%") 
                            ->where('pct.almacen_id',$almacenId)
                            ->where('p.estado','ACTIVO')
                            ->groupBy(
                                'pct.almacen_id',
                                'p.id',
                                'c.id',
                                'ma.id',
                                'mo.id',
                                'c.descripcion',
                                'ma.marca',
                                'mo.descripcion',
                                'p.nombre')
                            ->having(DB::raw('SUM(pct.stock)'),'>','0')
                            ->paginate(10, ['*'], 'page', $page); 

            return response()->json([
                'success' => true,
                'message' => 'PRODUCTOS OBTENIDOS',
                'productos' => $productos->items(),
                'more' => $productos->hasMorePages() 
            ]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=> $th->getMessage()]);
        }
    }

    public function getColoresTalla($almacen_id,$producto_id){
        
        try {

            $precios_venta  =   DB::select('SELECT 
                                p.id AS producto_id,
                                p.nombre AS producto_nombre,
                                p.precio_venta_1,
                                p.precio_venta_2,
                                p.precio_venta_3
                                FROM 
                                    productos AS p 
                                WHERE 
                                    p.id = ? AND p.estado = "ACTIVO" ',
                                [$producto_id]);  

           
            $colores =  DB::select('SELECT 
                                    p.id AS producto_id,
                                    p.nombre AS producto_nombre,
                                    c.id AS color_id,
                                    c.descripcion AS color_nombre,
                                    p.codigo as producto_codigo
                                FROM 
                                    producto_colores AS pc 
                                    inner join productos as p on p.id = pc.producto_id
                                    inner join colores as c on c.id = pc.color_id
                                WHERE 
                                    pc.almacen_id = ?
                                    AND pc.producto_id = ? 
                                    AND p.estado = "ACTIVO" 
                                    AND c.estado = "ACTIVO" ',
                                    [$almacen_id,$producto_id]);

            $stocks =   DB::select('select  
                        pct.producto_id,
                        pct.color_id,
                        pct.talla_id,
                        pct.stock,
                        pct.stock_logico, 
                        t.descripcion as talla_nombre
                        from producto_color_tallas as pct
                        inner join productos as p on p.id = pct.producto_id
                        inner join colores as c on c.id = pct.color_id 
                        inner join tallas as t on t.id = pct.talla_id
                        where 
                        p.estado = "ACTIVO" 
                        AND c.estado = "ACTIVO" 
                        AND t.estado = "ACTIVO"
                        AND pct.almacen_id = ?
                        AND p.id = ?',
                        [$almacen_id,$producto_id]);

            $tallas =   Talla::where('estado','ACTIVO')->orderBy('id')->get();   

            $producto_color_tallas  =   null;
            if(count($colores) > 0){
                $producto_color_tallas  =   $this->formatearColoresTallas($colores,$stocks,$precios_venta,$tallas);
            }

            return response()->json(['success' => true,'producto_color_tallas'=>$producto_color_tallas]);
        } catch (\Throwable $th) {
    
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function formatearColoresTallas($colores, $stocks, $precios_venta, $tallas)
    {
        
        $producto = [];

        // Verifica si $colores no está vacío
        if (count($colores) > 0) {
            $producto['id']     = $colores[0]->producto_id;
            $producto['nombre'] = $colores[0]->producto_nombre;
            $producto['codigo'] = $colores[0]->producto_codigo;
        } else {
            // Maneja el caso cuando $colores está vacío
            $producto['id']     = null;
            $producto['nombre'] = null;
            $producto['codigo'] = null;

        }

        // Verifica si $precios_venta no está vacío
        if (count($precios_venta) > 0) {
            $producto['precio_venta_1'] = $precios_venta[0]->precio_venta_1;
            $producto['precio_venta_2'] = $precios_venta[0]->precio_venta_2;
            $producto['precio_venta_3'] = $precios_venta[0]->precio_venta_3;
        } else {
            // Maneja el caso cuando $precios_venta está vacío
            $producto['precio_venta_1'] = null;
            $producto['precio_venta_2'] = null;
            $producto['precio_venta_3'] = null;
        }

        $lstColores = [];

        //======== RECORRIENDO COLORES =======
        foreach ($colores as $color) {
            $item_color = [];
            $item_color['id']       =   $color->color_id;
            $item_color['nombre']   =   $color->color_nombre;

            //======== OBTENIENDO TALLAS DEL COLOR =======
            $lstTallas = [];

            foreach ($tallas as $talla) {
                $item_talla = [];
                $item_talla['id'] = $talla->id;
                $item_talla['nombre'] = $talla->descripcion;

                // Filtrar stocks para color y talla actuales
                $stock_filtrado = array_filter($stocks, function ($stock) use ($producto, $color, $talla) {
                    return $stock->producto_id == $producto['id'] &&
                        $stock->color_id == $color->color_id &&
                        $stock->talla_id == $talla->id;
                });

                // Asignar stock y stock lógico si existe, o establecer en 0
                if (!empty($stock_filtrado)) {
                    $first_stock                = reset($stock_filtrado); // Obtiene el primer elemento del array filtrado
                    $item_talla['stock']        = $first_stock->stock;
                    $item_talla['stock_logico'] = $first_stock->stock_logico;
                } else {
                    $item_talla['stock'] = 0;
                    $item_talla['stock_logico'] = 0;
                }

                $lstTallas[] = $item_talla;
            }

            $item_color['tallas'] = $lstTallas;
            $lstColores[] = $item_color;
        }

        $producto['colores'] = $lstColores;

        return $producto;
    }

}
