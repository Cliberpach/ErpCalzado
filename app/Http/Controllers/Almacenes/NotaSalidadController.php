<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Modelo;
use App\Almacenes\Talla;
use App\Almacenes\Almacen;
use App\Almacenes\DetalleNotaIngreso;
use App\Almacenes\DetalleNotaSalidad;
use App\Almacenes\Kardex;
use App\Almacenes\LoteProducto;
use App\Almacenes\MovimientoNota;
use App\Almacenes\NotaIngreso;
use App\Almacenes\NotaSalidad;
use App\Almacenes\Producto;
use App\Almacenes\ProductoColor;
use App\Almacenes\ProductoColorTalla;
use App\Compras\Documento\Pago\Detalle;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Tabla\General;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use Throwable;

class NotaSalidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        $this->authorize('haveaccess','nota_salida.index');
        return view('almacenes.nota_salidad.index');
    }
    public function gettable(Request $request)
    {
        $data       =   DB::table("nota_salidad as n")
                        ->select('n.*',)
                        ->where('n.estado', 'ACTIVO');

        //========= FILTRO POR ROLES ======
        $roles = DB::table('role_user as rl')
                ->join('roles as r', 'r.id', '=', 'rl.role_id')
                ->where('rl.user_id', Auth::user()->id)
                ->pluck('r.name')
                ->toArray(); 

        //======== ADMIN PUEDE VER TODAS LAS NOTAS DE INGRESO DE SU SEDE =====
        if (in_array('ADMIN', $roles)) {
            $data->where('n.sede_id', Auth::user()->sede_id);
        } else {
            //====== USUARIOS PUEDEN VER SUS PROPIAS NOTAS DE INGRESO ======
            $data->where('n.sede_id', Auth::user()->sede_id)
            ->where('registrador_id', Auth::user()->id);
        }

        $data   =   $data->get();

        
        $detalles   =   DB::select(
                        'select distinct 
                        p.nombre as producto_nombre,
                        dns.nota_salida_id 
                        from detalle_nota_salidad as dns 
                        inner join productos as p on p.id = dns.producto_id');

        foreach ($data as $notaSalida) {
            
            $detallesFiltrados = array_filter($detalles, function($detalle) use ($notaSalida) {
                return $detalle->nota_salida_id == $notaSalida->id;
            });

            $cadenaDetalles = '';
            $caracteresAcumulados = 0;
        
            foreach ($detallesFiltrados as $detalle) {
                $nombreProducto = $detalle->producto_nombre;
                $longitudNombre = strlen($nombreProducto);
        
                // Verificar si agregar el nombre del producto superará los 200 caracteres
                if ($caracteresAcumulados + $longitudNombre <= 200) {
                    $cadenaDetalles .= $nombreProducto . ', ';
                    $caracteresAcumulados += $longitudNombre;
                } else {
                    // Si supera los 200 caracteres, terminar el bucle
                    break;
                }

                if($notaSalida->observacion == null){
                    $notaSalida->observacion='Sin Observaciones';
                }
            }

            //====== AÑADIR CADENA DETALLES =====
            $notaSalida->cadena_detalles = rtrim($cadenaDetalles, ', '); // Eliminar la última coma
        }
        
        return DataTables::of($data)->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('haveaccess','nota_salida.index');
       
        $usuarios   =   User::get();

        $tallas     =   Talla::where('estado','ACTIVO')->get();
        $fullaccess =   false;
        $modelos    =   Modelo::where('estado','ACTIVO')->get();
        $sede_id    =   Auth::user()->sede_id;
        $registrador=   Auth::user();        
        $almacenes  =   Almacen::where('estado','ACTIVO')   
                        ->where('sede_id',$sede_id)
                        ->get();

        if(count(Auth::user()->roles)>0)
        {
            $cont = 0;
            while($cont < count(Auth::user()->roles))
            {
                if(Auth::user()->roles[$cont]['full-access'] == 'SI')
                {
                    $fullaccess = true;
                    $cont = count(Auth::user()->roles);
                }
                $cont = $cont + 1;
            }
        }

        return view('almacenes.nota_salidad.create',
        [
        'tallas'        => $tallas, 
        'modelos'       => $modelos,
        'sede_id'       =>  $sede_id,
        'almacenes'     =>  $almacenes,
        'registrador'   =>  $registrador]);
    }

   
/*
array:11 [
  "notadetalle_tabla" => array:1 [
    0 => null
  ]
  "_token"                      => "XCcigubfOivrL6d1gCUmLO5X52q2rCU2nt6Xk2vN"
  "registrador"                 => "2025-02-05"
  "almacen_origen"              => "1"
  "almacen_destino"             => "2"
  "observacion"                 => null
  "tabla_ns_productos_length"   => "10"
  "tabla_ns_detalle_length"     => "10"
  "lstNs"                       => "[{"producto_id":"1","producto_nombre":"PRODUCTO TEST","color_id":"2","color_nombre":"AZUL","talla_id":"1","talla_nombre":"34","cantidad":"4"},{"producto_id":"1","producto_nombre":"PRODUCTO TEST","color_id":"3","color_nombre":"CELESTE","talla_id":"1","talla_nombre":"34","cantidad":"6"}]"
  "registrador_id"              => "1"
  "sede_id"                     => "1"
]
*/ 
    public function store(Request $request)
    {
      
        $this->authorize('haveaccess','nota_salida.index');
       
    
        $registrador        =   User::find($request->get('registrador_id'));
        $almacen_destino    =   DB::select('select 
                                a.id,
                                a.descripcion
                                from almacenes as a
                                where a.id = ?',[$request->get('almacen_destino')])[0];

        $almacen_origen     =   DB::select('select 
                                a.id,
                                a.descripcion
                                from almacenes as a
                                where a.id = ?',[$request->get('almacen_origen')])[0];
       
        DB::beginTransaction();

        try {

            //======= NOTA SALIDA ALMACÉN ORIGEN =====
            $nota_salida                            =   new NotaSalidad();
            $nota_salida->sede_id                   =   $request->get('sede_id');
            $nota_salida->almacen_origen_id         =   $request->get('almacen_origen');
            $nota_salida->almacen_destino_id        =   $request->get('almacen_destino');
            $nota_salida->registrador_id            =   $registrador->id;
            $nota_salida->registrador_nombre        =   $registrador->usuario;
            $nota_salida->almacen_origen_nombre     =   $almacen_origen->descripcion;
            $nota_salida->almacen_destino_nombre    =   $almacen_destino->descripcion;
            $nota_salida->observacion               =   mb_strtoupper($request->get('observacion'), 'UTF-8');   
            $nota_salida->save();

            //======= NOTA INGRESO ALMACÉN DESTINO ======
            $nota_ingreso                            =   new NotaIngreso();
            $nota_ingreso->almacen_destino_id        =   $almacen_destino->id;
            $nota_ingreso->almacen_destino_nombre    =   $almacen_destino->descripcion;
            $nota_ingreso->sede_id                   =   $request->get('sede_id');
            $nota_ingreso->registrador_nombre        =   $registrador->usuario;
            $nota_ingreso->registrador_id            =   $request->get('registrador_id');
            $nota_ingreso->observacion               =   mb_strtoupper($request->get('observacion'), 'UTF-8');
            $nota_ingreso->nota_salida_id            =   $nota_salida->id;
            $nota_ingreso->save();
    
            $lstNs = json_decode($request->get('lstNs'));
         
            foreach ($lstNs as $item) {

                //======= COMPROBANDO SI EXISTE EL PRODUCTO COLOR TALLA EN ALMACÉN ORIGEN =========
                $producto_origen    =   DB::select('select
                                        pct.* 
                                        from producto_color_tallas as pct
                                        where 
                                        pct.almacen_id = ?
                                        and pct.producto_id = ? 
                                        and pct.color_id = ? 
                                        and pct.talla_id = ?',
                                        [
                                            $request->get('almacen_origen'),
                                            $item->producto_id,
                                            $item->color_id,
                                            $item->talla_id
                                        ]);

                if(count($producto_origen) === 0){
                    throw new Exception("NO EXISTE EL PRODUCTO EN EL ALMACÉN DE ORIGEN!!!");
                }

                $producto_existe     =  DB::select('select 
                                        p.nombre as producto_nombre
                                        from productos as p
                                        where
                                        p.id = ?
                                        and p.estado = "ACTIVO"',[$item->producto_id]);

                $color_existe       =  DB::select('select 
                                        c.descripcion as color_nombre
                                        from colores as c
                                        where
                                        c.id = ?
                                        and c.estado = "ACTIVO"',[$item->color_id]);

                $talla_existe       =  DB::select('select 
                                        t.descripcion as talla_nombre
                                        from tallas as t
                                        where
                                        t.id = ?
                                        and t.estado = "ACTIVO"',[$item->talla_id]);

                $detalle                        =   new   DetalleNotaSalidad();
                $detalle->nota_salida_id        =   $nota_salida->id;
                $detalle->almacen_id            =   $almacen_origen->id;
                $detalle->producto_id           =   $item->producto_id;
                $detalle->color_id              =   $item->color_id;
                $detalle->talla_id              =   $item->talla_id;
                $detalle->cantidad              =   $item->cantidad;
                $detalle->almacen_nombre        =   $almacen_destino->descripcion;
                $detalle->producto_nombre       =   $producto_existe[0]->producto_nombre;
                $detalle->color_nombre          =   $color_existe[0]->color_nombre;  
                $detalle->talla_nombre          =   $talla_existe[0]->talla_nombre;  
                $detalle->save();


                //======== RESTANDO STOCK EN ALMACÉN ORIGEN =======
                ProductoColorTalla::where('producto_id', $item->producto_id)
                ->where('color_id', $item->color_id)
                ->where('talla_id', $item->talla_id)
                ->where('almacen_id', $request->get('almacen_origen'))
                ->update([
                    'stock'         =>  DB::raw("stock - $item->cantidad"),
                    'stock_logico'  =>  DB::raw("stock_logico - $item->cantidad")                
                ]);

                $stock_posterior_origen =   DB::select('select 
                                            pct.* 
                                            from producto_color_tallas as pct
                                            where 
                                            pct.producto_id = ? 
                                            and pct.color_id = ? 
                                            and pct.talla_id = ?
                                            and pct.almacen_id = ?',
                                            [
                                                $item->producto_id,
                                                $item->color_id,
                                                $item->talla_id,
                                                $request->get('almacen_origen')
                                            ])[0]->stock;

                //======= GRABANDO EN KARDEX =====
                $kardex                     =   new Kardex();
                $kardex->sede_id            =   $request->get('sede_id');
                $kardex->almacen_id         =   $request->get('almacen_origen');
                $kardex->producto_id        =   $item->producto_id;
                $kardex->color_id           =   $item->color_id;
                $kardex->talla_id           =   $item->talla_id;
                $kardex->almacen_nombre     =   $almacen_origen->descripcion;
                $kardex->producto_nombre    =   $producto_existe[0]->producto_nombre;
                $kardex->color_nombre       =   $color_existe[0]->color_nombre;
                $kardex->talla_nombre       =   $talla_existe[0]->talla_nombre;
                $kardex->cantidad           =   $item->cantidad;
                $kardex->precio             =   null;
                $kardex->importe            =   null;
                $kardex->accion             =   'SALIDA';
                $kardex->stock              =   $stock_posterior_origen;
                $kardex->numero_doc         =   'NS-'.$nota_salida->id;
                $kardex->documento_id       =   $nota_salida->id;
                $kardex->registrador_id     =   $registrador->id;
                $kardex->registrador_nombre =   $registrador->usuario;
                $kardex->fecha              =   Carbon::today()->toDateString();
                $kardex->descripcion        =   mb_strtoupper($request->get('observacion'), 'UTF-8');
                $kardex->save();

                //====== CREANDO NOTA DE INGRESO EN ALMACÉN DESTINO ========
                $detalle                     =   new   DetalleNotaIngreso();
                $detalle->nota_ingreso_id    =   $nota_ingreso->id;
                $detalle->almacen_id         =   $almacen_destino->id;
                $detalle->producto_id        =   $item->producto_id;
                $detalle->color_id           =   $item->color_id;
                $detalle->talla_id           =   $item->talla_id;
                $detalle->cantidad           =   $item->cantidad;
                $detalle->almacen_nombre     =   $almacen_destino->descripcion;
                $detalle->producto_nombre    =   $producto_existe[0]->producto_nombre;
                $detalle->color_nombre       =   $color_existe[0]->color_nombre;  
                $detalle->talla_nombre       =   $talla_existe[0]->talla_nombre;  
                $detalle->save();     
                
                //======== INGRESANDO STOCK EN ALMACÉN DESTINO =======
                //=>COMPROBANDO SI EXISTE EL PRODUCTO COLOR TALLA
                $producto_destino   =   DB::select('select 
                                        pct.* 
                                        from producto_color_tallas as pct
                                        where 
                                        pct.almacen_id  = ?
                                        and pct.producto_id = ? 
                                        and pct.color_id = ? 
                                        and pct.talla_id = ?',
                                        [
                                            $request->get('almacen_destino'),
                                            $item->producto_id,
                                            $item->color_id,
                                            $item->talla_id
                                        ]);

                 //======== TALLA EXISTE, INCREMENTAR STOCK =========
                if (count($producto_destino) > 0) {

                    ProductoColorTalla::where('producto_id', $item->producto_id)
                    ->where('color_id', $item->color_id)
                    ->where('talla_id', $item->talla_id)
                    ->where('almacen_id', $request->get('almacen_destino'))
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
                                            ->where('almacen_id',$request->get('almacen_destino'))
                                            ->exists();
                
                    //======== COLOR NO EXISTE, REGISTRAR COLOR =======
                    if(!$existeColor){
                        $producto_color                 =   new ProductoColor();
                        $producto_color->producto_id    =   $item->producto_id;
                        $producto_color->color_id       =   $item->color_id;
                        $producto_color->almacen_id     =   $request->get('almacen_destino');
                        $producto_color->save(); 
                    }  

                    //====== REGISTRAR TALLA ============
                    $producto                   =   new ProductoColorTalla();
                    $producto->producto_id      =   $item->producto_id;
                    $producto->color_id         =   $item->color_id;
                    $producto->talla_id         =   $item->talla_id;
                    $producto->stock            =   $item->cantidad;
                    $producto->stock_logico     =   $item->cantidad;
                    $producto->almacen_id       =   $request->get('almacen_destino');
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
                                $request->get('almacen_destino')]);
                        
                //==================== KARDEX ==================
                $kardex                     =   new Kardex();
                $kardex->sede_id            =   $request->get('sede_id');
                $kardex->almacen_id         =   $request->get('almacen_destino');
                $kardex->producto_id        =   $item->producto_id;
                $kardex->color_id           =   $item->color_id;
                $kardex->talla_id           =   $item->talla_id;
                $kardex->almacen_nombre     =   $almacen_destino->descripcion;
                $kardex->producto_nombre    =   $producto_existe[0]->producto_nombre;
                $kardex->color_nombre       =   $color_existe[0]->color_nombre;
                $kardex->talla_nombre       =   $talla_existe[0]->talla_nombre;
                $kardex->cantidad           =   $item->cantidad;
                $kardex->precio             =   null;
                $kardex->importe            =   null;
                $kardex->accion             =   'INGRESO';
                $kardex->stock              =   $producto[0]->stock;
                $kardex->numero_doc         =   'NI-'.$nota_ingreso->id;
                $kardex->documento_id       =   $nota_ingreso->id;
                $kardex->registrador_id     =   $registrador->id;
                $kardex->registrador_nombre =   $registrador->usuario;
                $kardex->fecha              =   Carbon::today()->toDateString();
                $kardex->descripcion        =   mb_strtoupper($request->get('observacion'), 'UTF-8');
                $kardex->save();

            }
          
            $descripcion = "SE AGREGÓ LA NOTA DE SALIDAD";
            $gestion = "ALMACEN / NOTA SALIDAD";
            crearRegistro($nota_salida, $descripcion , $gestion);
    
    
            DB::commit();
            Session::flash('message_success','NOTA DE SALIDA REGISTRADA CON ÉXITO');
            return response()->json(['success'=>true,'message'=>'NOTA DE SALIDA REGISTRADA CON ÉXITO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('haveaccess','nota_salida.index');
        $notasalidad        =   NotaSalidad::findOrFail($id);
        $detallenotasalidad =   DB::select('select dns.id,dns.producto_id,dns.color_id,dns.talla_id,
                                p.nombre as producto_nombre,c.descripcion as color_nombre,t.descripcion as talla_nombre,
                                dns.cantidad
                                from detalle_nota_salidad as dns
                                inner join productos as p on p.id=dns.producto_id
                                inner join colores as c on c.id=dns.color_id
                                inner join tallas as t on t.id=dns.talla_id
                                where dns.nota_salida_id=?',[$id]);
     
        $fullaccess = false;

        if(count(Auth::user()->roles)>0)
        {
            $cont = 0;
            while($cont < count(Auth::user()->roles))
            {
                if(Auth::user()->roles[$cont]['full-access'] == 'SI')
                {
                    $fullaccess = true;
                    $cont = count(Auth::user()->roles);
                }
                $cont = $cont + 1;
            }
        }
        $origenes   =   General::find(28)->detalles;
        $destinos   =   General::find(29)->detalles;
        $usuarios   =   User::get();
        $tallas     =   Talla::where('estado','ACTIVO')->get();

        return view('almacenes.nota_salidad.show',[
            "origenes"      =>  $origenes,
            'destinos'      =>  $destinos,
            'usuarios'      =>  $usuarios,
            'notasalidad'   =>  $notasalidad,
            'detalle'       =>  $detallenotasalidad,
            'fullaccess'    =>  $fullaccess,
            'tallas'        =>  $tallas
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('haveaccess','nota_salida.index');
        $notasalidad=NotaSalidad::findOrFail($id);
        $data=array();
        $detallenotasalidad=DB::table('detalle_nota_salidad')->where('nota_salidad_id',$notasalidad->id)->get();
        foreach($detallenotasalidad as $fila)
        {
            $lote=DB::table('lote_productos')->where('id',$fila->lote_id)->first();
            $producto=DB::table('productos')->where('id',$fila->producto_id)->first();
            array_push($data,array(
                    'producto_id'=>$fila->producto_id,
                    'cantidad'=>$fila->cantidad,
                    'lote'=>$lote->codigo_lote,
                    'producto'=>$producto->nombre,
                    'lote_id'=>$fila->lote_id
            ));
        }
        $origenes=  General::find(28)->detalles;
        $destinos=  General::find(29)->detalles;
        $lotes=DB::table('lote_productos')->get();
        $usuarios   =   User::get();
        $tallas     =   Talla::where('estado','ACTIVO')->get();

        return view('almacenes.nota_salidad.edit',[
        "origenes"=>$origenes,'destinos'=>$destinos,
       'usuarios'=>$usuarios,
        'productos'=>$productos,'lotes'=>$lotes,'notasalidad'=>$notasalidad,'detalle'=>json_encode($data)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('haveaccess','nota_salida.index');
         $data = $request->all();

         $rules = [

            'fecha' => 'required',
            'destino' => 'required',
            'origen' => 'nullable',
            'notadetalle_tabla'=>'required',


        ];

        $message = [
            'fecha.required' => 'El campo fecha  es Obligatorio',
            'destino.required' => 'El campo destino  es Obligatorio',
            'notadetalle_tabla.required'=>'No hay dispositivos',
        ];

         Validator::make($data, $rules, $message)->validate();


         //$registro_sanitario = new RegistroSanitario();
         $notasalidad=NotaSalidad::findOrFail($id);
         $notasalidad->fecha=$request->get('fecha');
         $destino=DB::table('tabladetalles')->where('id',$request->destino)->first();
         $notasalidad->destino=$destino->descripcion;
         $notasalidad->observacion=$request->observacion;
         $notasalidad->usuario=Auth()->user()->usuario;
         $notasalidad->update();

         $productosJSON = $request->get('notadetalle_tabla');
         $notatabla = json_decode($productosJSON[0]);
         if($notatabla != "")
         {
             DetalleNotaSalidad::where('nota_salidad_id',$notasalidad->id)->delete();
             foreach ($notatabla as $fila) {

                $lote_producto = LoteProducto::findOrFail($fila->lote_id);
                $cantidadmovimiento = DB::table("movimiento_nota")->where('lote_id',$fila->lote_id)->where('producto_id',$fila->producto_id)->where('nota_id',$id)->where('movimiento','SALIDA')->first()->cantidad;
                $cantidadmovimiento = $cantidadmovimiento ? $cantidadmovimiento : 0;
                $lote_producto->cantidad = $lote_producto->cantidad + $cantidadmovimiento;
                $lote_producto->cantidad_logica = $lote_producto->cantidad + $cantidadmovimiento;
                $lote_producto->update();

                // MovimientoNota::where('lote_id',$fila->lote_id)->where('producto_id',$fila->producto_id)->where('nota_id',$id)->where('movimiento','SALIDA')->delete();

                DetalleNotaSalidad::create([
                    'nota_salidad_id' => $id,
                    'color_id' => $fila->color_id,
                    'talla_id' => $fila->talla_id,
                    // 'lote_id' => $fila->lote_id,
                    'cantidad' => $fila->cantidad,
                    'producto_id'=> $fila->producto_id,
                ]);

              }
         }
         //Registro de actividad
         $descripcion = "SE AGREGÓ LA NOTA DE SALIDAD ";
         $gestion = "ALMACEN / NOTA SALIDAD";
         crearRegistro($notasalidad, $descripcion , $gestion);


         Session::flash('success','NOTA DE SALIDAD');
         return redirect()->route('almacenes.nota_salidad.index')->with('guardar', 'success');
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('haveaccess','nota_salida.index');
        $notasalidad = NotaSalidad::findOrFail($id);
        $notasalidad->estado="ANULADO";
        $notasalidad->save();
        foreach($notasalidad->detalles as $detalle)
        {
            $lote = LoteProducto::find($detalle->lote_id);
            $lote->cantidad = $lote->cantidad + $detalle->cantidad;
            $lote->cantidad_logica = $lote->cantidad + $detalle->cantidad;
            $lote->update();
        }
        Session::flash('success','NOTA DE SALIDAD');
        return redirect()->route('almacenes.nota_salidad.index')->with('guardar', 'success');
    }

    public function getPdf($id) {
        $documento  = NotaSalidad::find($id);
        $detalles   = DetalleNotaSalidad::where('nota_salida_id', $id)->get();

        $pdf = PDF::loadview('almacenes.nota_salidad.impresion.comprobante_normal', [
            'documento' => $documento,
            'detalles' => $detalles,
            'moneda' => "SOLES",
            'empresa' => Empresa::first(),
        ])->setPaper('a4')->setWarnings(false);

        return $pdf->stream($documento->numero . '.pdf');

    }

    public function getLot()
    {
        $this->authorize('haveaccess','nota_salida.index');
        return datatables()->query(
            DB::table('lote_productos')
            ->join('productos','productos.id','=','lote_productos.producto_id')
            ->join('productos_clientes','productos_clientes.producto_id','=','productos.id')
            ->join('categorias','categorias.id','=','productos.categoria_id')
            ->join('marcas','marcas.id','=','productos.marca_id')
            ->join('tabladetalles','tabladetalles.id','=','productos.medida')
            ->leftJoin('detalle_nota_ingreso','detalle_nota_ingreso.lote_id','=','lote_productos.id')
            ->leftJoin('nota_ingreso','nota_ingreso.id','=','detalle_nota_ingreso.nota_ingreso_id')
            ->leftJoin('compra_documento_detalles','compra_documento_detalles.lote_id','=','lote_productos.id')
            ->leftJoin('compra_documentos','compra_documentos.id','=','compra_documento_detalles.documento_id')
            ->select(
                'nota_ingreso.moneda as moneda_ingreso',
                'compra_documentos.moneda as moneda_compra',
                'compra_documentos.dolar as dolar_compra',
                'compra_documentos.igv_check as igv_compra',
                'compra_documento_detalles.precio_soles',
                'compra_documento_detalles.precio as precio_compra',
                'detalle_nota_ingreso.costo as precio_ingreso',
                'detalle_nota_ingreso.costo_soles as precio_ingreso_soles',
                'nota_ingreso.dolar as dolar_ingreso',
                'compra_documento_detalles.precio_mas_igv_soles',
                'lote_productos.*',
                'productos.nombre',
                'productos.peso_producto',
                'productos.igv',
                'productos.codigo_barra',
               //'productos.porcentaje_normal',
                DB::raw('ifnull((select porcentaje
                    from productos_clientes pc
                    where pc.producto_id = lote_productos.producto_id
                    and pc.cliente = 121
                    and pc.estado = "ACTIVO"
                order by id desc
                limit 1),20) as porcentaje_normal'),
                //'productos.porcentaje_distribuidor',
                DB::raw('ifnull((select porcentaje
                    from productos_clientes pc
                    where pc.producto_id = lote_productos.producto_id
                    and pc.cliente = 122
                    and pc.estado = "ACTIVO"
                order by id desc
                limit 1),20) as porcentaje_distribuidor'),
                'productos_clientes.cliente',
                'productos_clientes.moneda',
                'productos_clientes.porcentaje',
                'tabladetalles.simbolo as unidad_producto',
                'categorias.descripcion as categoria',
                'marcas.marca',
                DB::raw('DATE_FORMAT(lote_productos.fecha_vencimiento, "%d/%m/%Y") as fecha_venci')
            )
            ->where('lote_productos.cantidad_logica','>',0)
            ->where('lote_productos.estado','1')
            ->where('productos_clientes.cliente','121')
            ->where('productos_clientes.moneda','1')
            ->orderBy('lote_productos.id','ASC')
            ->where('productos_clientes.estado','ACTIVO')
        )->toJson();
    }

    //CAMBIAR CANTIDAD LOGICA DEL LOTE
    public function quantity(Request $request)
    {
        $data = $request->all();
        $producto_id = $data['producto_id'];
        $cantidad = $data['cantidad'];
        $condicion = $data['condicion'];
        $mensaje = '';
        $lote = LoteProducto::findOrFail($producto_id);
        //DISMINUIR
        if ($lote->cantidad_logica >= $cantidad && $condicion == '1' ) {
            $nuevaCantidad = $lote->cantidad_logica - $cantidad;
            $lote->cantidad_logica = $nuevaCantidad;
            $lote->update();
            $mensaje = 'Cantidad aceptada';
        }
        //AUMENTAR
        if ($condicion == '0' ) {
            $nuevaCantidad = $lote->cantidad_logica + $cantidad;
            $lote->cantidad_logica = $nuevaCantidad;
            $lote->update();
            $mensaje = 'Cantidad regresada';
        }
        return $mensaje;
    }

    //DEVOLVER CANTIDAD LOGICA AL CERRAR VENTANA
    public function returnQuantity(Request $request)
    {
        $data = $request->all();
        $cantidades = $data['cantidades'];
        $productosJSON = $cantidades;
        $productotabla = json_decode($productosJSON);
        $mensaje = true;
        foreach ($productotabla as $detalle) {
            //DEVOLVEMOS CANTIDAD AL LOTE Y AL LOTE LOGICO
            $lote = LoteProducto::findOrFail($detalle->lote_id);
            $lote->cantidad_logica = $lote->cantidad_logica + $detalle->cantidad;
            //$lote->cantidad =  $lote->cantidad_logica;
            $lote->estado = '1';
            $lote->update();
            $mensaje = true;
        };

        return $mensaje;
    }

    //DEVOLVER CANTIDAD LOGICA AL CERRAR VENTANA EDIT
    public function returnQuantityEdit(Request $request)
    {
        $data = $request->all();
        $cantidades = $data['cantidades'];
        $productosJSON = $cantidades;
        $productotabla = json_decode($productosJSON);
        $id = $data['nota_id'];
        $mensaje = '';
        foreach ($productotabla as $detalle) {
            //DEVOLVEMOS CANTIDAD AL LOTE Y AL LOTE LOGICO
            $lote = LoteProducto::findOrFail($detalle->lote_id);
            //$cantidadmovimiento = DB::table("movimiento_nota")->where('lote_id',$lote->id)->where('producto_id',$lote->producto_id)->where('nota_id',$id)->where('movimiento','SALIDA')->first()->cantidad;
            $movimiento = DB::table("movimiento_nota")->where('lote_id',$lote->id)->where('producto_id',$lote->producto_id)->where('nota_id',$id)->where('movimiento','SALIDA')->first();
            if($movimiento)
            {
                $cantidadmovimiento = $movimiento->cantidad;

                if($cantidadmovimiento > $detalle->cantidad)
                {
                    $mover = $cantidadmovimiento - $detalle->cantidad;
                    $lote->cantidad_logica = $lote->cantidad_logica - $mover;
                }
                else
                {
                    $mover = $detalle->cantidad - $cantidadmovimiento;
                    $lote->cantidad_logica = $lote->cantidad_logica + $mover;
                }



                //$lote->cantidad =  $lote->cantidad_logica;
                $lote->estado = '1';
                $lote->update();
            }
            else{
                $lote = LoteProducto::findOrFail($detalle->lote_id);
                $lote->cantidad_logica = $lote->cantidad_logica + $detalle->cantidad;
                //$lote->cantidad =  $lote->cantidad_logica;
                $lote->estado = '1';
                $lote->update();
                $mensaje = 'Cantidad devuelta';
            }
            $mensaje = 'Cantidad devuelta';
        };

        return $mensaje;
    }

    //DEVOLVER CANTIDAD LOGICA DEL LOTE ELIMINADO
    public function returnQuantityLoteInicio(Request $request)
    {
        $data = $request->all();
        $cantidades = $data['cantidades'];
        $productosJSON = $cantidades;
        $productotabla = json_decode($productosJSON);
        $mensaje = '';
        foreach ($productotabla as $detalle) {
            //DEVOLVEMOS CANTIDAD AL LOTE Y AL LOTE LOGICO
            $lote = LoteProducto::findOrFail($detalle->lote_id);
            $lote->cantidad_logica = $lote->cantidad_logica - $detalle->cantidad;
            $lote->estado = '1';
            $lote->update();
            $mensaje = 'Cantidad devuelta';
        };

        return $mensaje;
    }

    //DEVOLVER LOTE
    public function returnLote(Request $request)
    {
        $data = $request->all();
        $lote_id = $data['lote_id'];
        $lote = LoteProducto::find($lote_id);

        if($lote)
        {
        return response()->json([
            'success' => true,
            'lote' => $lote,
        ]);
        }
        else{
        return response()->json([
            'success' => false,
        ]);
        }
    }

    //ACTUALIZAR LOTE E EDICION DE CANTIDAD
    public function updateLote(Request $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->all();
            $lote_id = $data['lote_id'];
            $cantidad_sum = $data['cantidad_sum'];
            $cantidad_res = $data['cantidad_res'];
            $lote = LoteProducto::find($lote_id);

            if($lote)
            {
                $lote->cantidad_logica = $lote->cantidad_logica + ($cantidad_sum - $cantidad_res);
                $lote->update();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'lote' => $lote,
                ]);
            }
            else{
                DB::rollBack();
                return response()->json([
                    'success' => false,
                ]);
            }
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ]);
        }
    }

    //ACTUALIZAR LOTE E EDICION DE CANTIDAD
    public function updateLoteEdit(Request $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->all();
            $lote_id = $data['lote_id'];
            $cantidad_sum = $data['cantidad_sum'];
            $cantidad_res = $data['cantidad_res'];
            $lote = LoteProducto::find($lote_id);

            if($lote)
            {
                $lote->cantidad_logica = $lote->cantidad_logica + ($cantidad_sum - $cantidad_res);
                $lote->update();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'lote' => $lote,
                ]);
            }
            else{
                DB::rollBack();
                return response()->json([
                    'success' => false,
                ]);
            }
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ]);
        }
    }


    public function getStock($almacen_id,$producto_id,$color_id,$talla_id){

        try {

            $stock_logico   = DB::select('
                                SELECT 
                                pct.stock_logico 
                                FROM producto_color_tallas as pct
                                WHERE 
                                pct.almacen_id = ?
                                AND pct.producto_id = ? 
                                AND pct.color_id = ? 
                                AND pct.talla_id = ?',
                                [$almacen_id,$producto_id, $color_id, $talla_id]
                            );

            if(count($stock_logico) === 0){
                throw new Exception("ESTA TALLA NO EXISTE AÚN PARA ESTE PRODUCTO!!!");    
            }


            return response()->json([ "success"=>true,"message" => "STOCK OBTENIDO", "stock_logico" => $stock_logico[0]->stock_logico]);
        } catch (Throwable $th) {
            return response()->json([ "success"=>false,"message" => $th->getMessage()], 500);
        }                    
    }

    public function getProductosAlmacen($modelo_id,$almacen_origen_id){
        
        try {

            $stocks =   DB::select('select p.id as producto_id, p.nombre as producto_nombre,
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
                        where p.modelo_id = ? 
                        AND pct.almacen_id = ?
                        AND c.estado="ACTIVO" 
                        AND t.estado="ACTIVO"
                        AND p.estado="ACTIVO" 
                        order by p.id,c.id,t.id',[$modelo_id,$almacen_origen_id]);

            $producto_colores = DB::select('select 
                                p.id as producto_id,p.nombre as producto_nombre,
                                c.id as color_id, c.descripcion as color_nombre,
                                p.precio_venta_1,p.precio_venta_2,p.precio_venta_3
                                from producto_colores as pc
                                inner join productos as p on p.id = pc.producto_id
                                inner join colores as c on c.id = pc.color_id
                                where 
                                p.modelo_id = ? 
                                AND pc.almacen_id = ?
                                AND c.estado="ACTIVO" 
                                AND p.estado="ACTIVO"
                                group by p.id,p.nombre,c.id,c.descripcion,
                                p.precio_venta_1,p.precio_venta_2,p.precio_venta_3
                                order by p.id,c.id',[$modelo_id,$almacen_origen_id]);

            return response()->json(
                [
                "success"           =>  true, 
                "stocks"            =>  $stocks ,
                "producto_colores"  =>  $producto_colores,
                'message'           =>  'PRODUCTOS OBTENIDOS'
                ]
                );
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
       

    }


}
