<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Modelo;
use App\Almacenes\Talla;
use App\Almacenes\Almacen;
use App\Almacenes\DetalleNotaSalidad;
use App\Almacenes\LoteProducto;
use App\Almacenes\MovimientoNota;
use App\Almacenes\NotaSalidad;
use App\Almacenes\Producto;
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
    public function gettable()
    {
        $data = DB::table("nota_salidad as n")->select('n.*',)->where('n.estado', 'ACTIVO')->get();
        $detalles = DB::select(
        'select distinct p.nombre as producto_nombre,ni.id as nota_ingreso_id ,ni.destino, ni.observacion as observacion
        from nota_salidad as ni 
        inner join detalle_nota_salidad  as dni
        on ni.id=dni.nota_salidad_id 
        inner join productos as p
        on p.id=dni.producto_id');

        foreach ($data as $notaIngreso) {
            
            $detallesFiltrados = array_filter($detalles, function($detalle) use ($notaIngreso) {
                return $detalle->nota_ingreso_id == $notaIngreso->id;
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

                if($notaIngreso->observacion==null){
                    $notaIngreso->observacion='Sin Observaciones';
                }
            }

            // Añadir la cadena de detalles como un nuevo campo en la nota de ingreso
            $notaIngreso->cadena_detalles = rtrim($cadenaDetalles, ', '); // Eliminar la última coma
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
        $fecha_hoy = Carbon::now()->format('Y-m-d');
       
        $fecha=Carbon::createFromFormat('Y-m-d', $fecha_hoy);
        
        $fecha=str_replace("-", "", $fecha);
        $fecha=str_replace(" ", "", $fecha);
        $fecha=str_replace(":", "", $fecha);
        
        $origenes   =   General::find(28)->detalles;
        $destinos   =   General::find(29)->detalles;
        $lotes      =   DB::table('lote_productos')->get();
        $ngenerado  =   $fecha.(DB::table('nota_salidad')->count()+1);
        $usuarios   =   User::get();
        $productos  =   Producto::where('estado','ACTIVO')->get();
        $almacenes  =   Almacen::all();
        $tallas     =   Talla::where('estado','ACTIVO')->get();
        $fullaccess =   false;
        $modelos    =   Modelo::where('estado','ACTIVO')->get();

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
        return view('almacenes.nota_salidad.create',["fecha_hoy"=>$fecha_hoy,
        'tallas' => $tallas, 'modelos' => $modelos,
        "origenes"=>$origenes,'destinos'=>$destinos,
        'ngenerado'=>$ngenerado,'usuarios'=>$usuarios,
        "almacenes"=>$almacenes,
        'productos'=>$productos,'lotes'=>$lotes,'fullaccess'=>$fullaccess]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $this->authorize('haveaccess','nota_salida.index');
       $fecha_hoy= Carbon::now()->toDateString();
       $fecha=Carbon::createFromFormat('Y-m-d',$fecha_hoy);
       $fecha = str_replace("-", "", $fecha);
       $fecha = str_replace(" ", "", $fecha);
       $fecha = str_replace(":", "", $fecha);
       $destino= Almacen::find($request->destino);
    
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

        $notasalidad=new NotaSalidad();
        $notasalidad->numero=$request->numero;
        $notasalidad->fecha=$request->fecha;
        // $destino=DB::table('tabladetalles')->where('id',$request->destino)->first();
        $notasalidad->destino=$destino->descripcion;
        $notasalidad->origen=$request->origen;
        $notasalidad->observacion=$request->observacion;
        $notasalidad->usuario=Auth()->user()->usuario;
        $notasalidad->save();

        $articulosJSON = $request->get('notadetalle_tabla');
        $notatabla = json_decode($articulosJSON[0]);
     
        foreach ($notatabla as $fila) {
           DetalleNotaSalidad::create([
                'nota_salidad_id' => $notasalidad->id,
                // 'lote_id' => $fila->lote_id,
                'color_id' => $fila->color_id,
                'talla_id' => $fila->talla_id,
                'cantidad' => $fila->cantidad,
                'producto_id'=> $fila->producto_id,
            ]);
        }
      
        $descripcion = "SE AGREGÓ LA NOTA DE SALIDAD";
        $gestion = "ALMACEN / NOTA SALIDAD";
        crearRegistro($notasalidad, $descripcion , $gestion);


        Session::flash('success','NOTA DE SALIDAD');
        return redirect()->route('almacenes.nota_salidad.index')->with('guardar', 'success');
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
                                where dns.nota_salidad_id=?',[$id]);
     
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
        $documento = NotaSalidad::find($id);
        $detalles = DetalleNotaSalidad::where('nota_salidad_id', $id)->get();

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


    public function getStock($producto_id,$color_id,$talla_id){

        try {

            $stock_logico = DB::select('
                SELECT pct.stock 
                FROM producto_color_tallas as pct
                WHERE pct.producto_id = ? AND pct.color_id = ? AND pct.talla_id = ?',
                [$producto_id, $color_id, $talla_id]
            );


            return response()->json(["message" => "success", "data" => $stock_logico]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Error al obtener el stock", "error" => $e->getMessage()], 500);
        }                    
    }


}
