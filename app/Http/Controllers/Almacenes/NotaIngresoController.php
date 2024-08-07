<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Color; 
use App\Almacenes\Talla; 
use App\Almacenes\Modelo; 
use App\Almacenes\DetalleNotaIngreso;
use App\Almacenes\LoteProducto;
use App\Almacenes\MovimientoNota;
use App\Almacenes\NotaIngreso;
use App\Almacenes\Producto;
use App\Exports\ErrorExcel;
use App\Exports\ModeloExport;
use App\Exports\ProductosExport;
use App\Http\Controllers\Controller;
use App\Imports\DataExcel;
use App\Mantenimiento\Tabla\General;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use App\Imports\NotaIngreso as ImportsNotaIngreso;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade as PDF;
use App\Mantenimiento\Empresa\Empresa;
use App\Almacenes\ProductoColorTalla;


class NotaIngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('haveaccess','nota_ingreso.index');
        return view('almacenes.nota_ingresos.index');
    }
    public function gettable()
    {
        $data = DB::table("nota_ingreso as n")->select('n.*',)->where('n.estado', 'ACTIVO')->get();
        $detalles = DB::select('select distinct p.nombre as producto_nombre,ni.id as nota_ingreso_id,ni.observacion
                from nota_ingreso as ni 
                inner join detalle_nota_ingreso as dni
                on ni.id=dni.nota_ingreso_id
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

        $this->authorize('haveaccess','nota_ingreso.index');
        $fecha_hoy = Carbon::now()->toDateString();
        $fecha = Carbon::createFromFormat('Y-m-d', $fecha_hoy);
        $fecha = str_replace("-", "", $fecha);
        $fecha = str_replace(" ", "", $fecha);
        $fecha = str_replace(":", "", $fecha);
        $fecha_actual = Carbon::now();
        $fecha_actual = date("d/m/Y",strtotime($fecha_actual));
        $fecha_5 = date("Y-m-d",strtotime($fecha_hoy."+ 5 years"));
        $origenes =  General::find(28)->detalles;
        $destinos =  General::find(29)->detalles;
        $lotes = DB::table('lote_productos')->get();
        $ngenerado = $fecha . (DB::table('nota_ingreso')->count() + 1);
        $usuarios = User::get();
        $productos = Producto::where('estado', 'ACTIVO')->get();
        $monedas =  tipos_moneda();
        $modelos = Modelo::where('estado','ACTIVO')->get();
        $tallas = Talla::where('estado','ACTIVO')->get();
        $colores =  Color::where('estado','ACTIVO')->get();
        return view('almacenes.nota_ingresos.create', [
            "fecha_hoy" => $fecha_hoy,
            "fecha_actual" => $fecha_actual,
            "fecha_5" => $fecha_5,
            "origenes" => $origenes, 'destinos' => $destinos,
            'ngenerado' => $ngenerado, 'usuarios' => $usuarios,
            'productos' => $productos, 'lotes' => $lotes,
            'monedas' => $monedas,
            'modelos' => $modelos,
            'colores' => $colores,
            'tallas' => $tallas
        ]);
    }
    
    // public function getProductos(Request $request)
    // {
    //     $data = DB::table('lote_productos')->where('id', $request->lote_id)->get();
    //     return json_encode($data);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('haveaccess','nota_ingreso.index');
        $fecha_hoy = Carbon::now()->toDateString();
        $fecha = Carbon::createFromFormat('Y-m-d', $fecha_hoy);
        $fecha = str_replace("-", "", $fecha);
        $fecha = str_replace(" ", "", $fecha);
        $fecha = str_replace(":", "", $fecha);

        $data = $request->all();

        $rules = [
            'fecha' => 'required',
            'destino' => 'nullable',
            'origen' => 'required',
            'notadetalle_tabla' => 'required',
            //'moneda' => 'required',
        ];
        $message = [

            'fecha.required' => 'El campo fecha  es Obligatorio',
            'origen.required' => 'El campo origen  es Obligatorio',
            //'moneda.required' => 'El campo moneda  es Obligatorio',
            'notadetalle_tabla.required' => 'No hay detalles',
        ];

        Validator::make($data, $rules, $message)->validate();

        // $dolar_aux = json_encode(precio_dolar(), true);
        // $dolar_aux = json_decode($dolar_aux, true);

        // $dolar = (float)$dolar_aux['original']['venta'];


        // //$registro_sanitario = new RegistroSanitario();
         $notaingreso = new NotaIngreso();
         $notaingreso->numero = $fecha . (DB::table('nota_ingreso')->count() + 1);
         $notaingreso->fecha = $request->get('fecha');
       if($request->destino)
       {
            $destino = DB::table('tabladetalles')->where('id', $request->destino)->first();
            $notaingreso->destino = $destino->descripcion;
       }
        $origen                     = DB::table('tabladetalles')->where('id', $request->origen)->first();
        $notaingreso->origen        = $origen->descripcion;
        $notaingreso->usuario       = Auth()->user()->usuario;
        $notaingreso->observacion   =   $request->get('observacion');
        // $notaingreso->total = $request->get('monto_total');
        // $notaingreso->moneda = $request->get('moneda');
        // $notaingreso->tipo_cambio = $dolar;
        // $notaingreso->dolar = $dolar;
        // if($request->get('moneda') == 'DOLARES')
        // {
        //     $notaingreso->total_soles = (float) $request->get('monto_total') * (float) $dolar;

        //     $notaingreso->total_dolares = (float) $request->get('monto_total');
        // }
        // else
        // {
        //     $notaingreso->total_soles = (float) $request->get('monto_total');

        //     $notaingreso->total_dolares = (float) $request->get('monto_total') / $dolar;
        // }
        $notaingreso->save();

        $articulosJSON = $request->get('notadetalle_tabla');
        $notatabla = json_decode($articulosJSON[0]);

        foreach ($notatabla as $fila) {
            $detalleNotaIngreso                     =   new   DetalleNotaIngreso();
            $detalleNotaIngreso->nota_ingreso_id    =   $notaingreso->id;
            $detalleNotaIngreso->producto_id        =   $fila->producto_id;
            $detalleNotaIngreso->color_id           =   $fila->color_id;
            $detalleNotaIngreso->talla_id           =   $fila->talla_id;
            $detalleNotaIngreso->cantidad           =   $fila->cantidad;
            $detalleNotaIngreso->save();

            $this->generarCodigoBarras($fila);
        }

        

        //Registro de actividad
        $descripcion = "SE AGREGÓ LA NOTA DE INGRESO ";
        $gestion = "ALMACEN / NOTA INGRESO";
        crearRegistro($notaingreso, $descripcion, $gestion);
        
        Session::flash('succes_store_nota_ingreso', 'NOTA INGRESO REGISTRADA');

        if($request->get('generarAdhesivos') === "SI"){
            Session::flash('generarAdhesivos', 'GENERANDO ADHESIVOS');
            Session::flash('nota_id',$notaingreso->id);
        }

        return redirect()->route('almacenes.nota_ingreso.index');
    }

    public function storeFast(Request $request)
    {
        $fecha_hoy = Carbon::now()->toDateString();
        $fecha = Carbon::createFromFormat('Y-m-d', $fecha_hoy);
        $fecha = str_replace("-", "", $fecha);
        $fecha = str_replace(" ", "", $fecha);
        $fecha = str_replace(":", "", $fecha);

        $fecha_actual = Carbon::now();
        $fecha_actual = date("d/m/Y", strtotime($fecha_actual));
        $fecha_5 = date("Y-m-d", strtotime($fecha_hoy . "+ 5 years"));

        $numero = $fecha . (DB::table('nota_ingreso')->count() + 1);

        $dolar_aux = json_encode(precio_dolar(), true);
        $dolar_aux = json_decode($dolar_aux, true);

        $dolar = (float)$dolar_aux['original']['venta'];
        $fecha_5 = date("Y-m-d",strtotime($fecha_hoy."+ 5 years"));

        $data = $request->all();

        $rules = [
            'producto_id' => 'required',
            'cantidad' => 'nullable',
        ];

        $message = [

            'producto_id.required' => 'El campo producto  es Obligatorio',
            'cantidad.required' => 'El campo cantidad  es Obligatorio',
        ];

        $validator =  Validator::make($data, $rules, $message);

        if ($validator->fails()) {
            Session::flash('error','Ingreso no creado porfavor llenar todos los datos.');
            return redirect()->route('almacenes.producto.index')->with('guardar', 'error');
        }

        $nota = NotaIngreso::create([
            'numero' => $numero,
            'fecha' => $fecha_hoy,
            'destino' => 'ALMACEN',
            'moneda' => 'SOLES',
            'tipo_cambio' => $dolar,
            'dolar' => $dolar,
            'total' => $request->costo * $request->cantidad,
            'total_soles' => $request->costo * $request->cantidad,
            'total_dolares' => ($request->costo * $request->cantidad) / $dolar,
            'origen' => 'INGRESO RAPIDO',
            'usuario' => Auth()->user()->usuario
        ]);

        $costo_soles = (float) $request->get('costo') / (float) $request->cantidad;

        $costo_dolares = (float) $costo_soles / (float) $dolar;

        DetalleNotaIngreso::create([
            'nota_ingreso_id' => $nota->id,
            'lote' => 'LT-'.$fecha_actual,
            'cantidad' => $request->cantidad,
            'producto_id' => $request->producto_id,
            'fecha_vencimiento' => $fecha_5,
            'costo' => $costo_soles,
            'costo_soles' => $costo_soles,
            'costo_dolares' => $costo_dolares,
            'valor_ingreso' => $request->costo ,
        ]);

        //Registro de actividad
        $descripcion = "SE AGREGÓ LA NOTA DE INGRESO ";
        $gestion = "ALMACEN / NOTA INGRESO";
        crearRegistro($nota, $descripcion, $gestion);


        Session::flash('success','Ingreso creado correctamente.');
        return redirect()->route('almacenes.producto.index')->with('guardar', 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('haveaccess','nota_ingreso.index');

        $fecha_hoy = Carbon::now()->toDateString();
        $fecha = Carbon::createFromFormat('Y-m-d', $fecha_hoy);
        $fecha = str_replace("-", "", $fecha);
        $fecha = str_replace(" ", "", $fecha);
        $fecha = str_replace(":", "", $fecha);
        $fecha_actual = Carbon::now();
        $fecha_actual = date("d/m/Y",strtotime($fecha_actual));
        $fecha_5 = date("Y-m-d",strtotime($fecha_hoy."+ 5 years"));
        $notaingreso = NotaIngreso::findOrFail($id);
        $data = array();
        $detallenotaingreso = DB::table('detalle_nota_ingreso')->where('nota_ingreso_id', $notaingreso->id)->get();

        foreach ($detallenotaingreso as $fila) {
            //$lote = LoteProducto::where('codigo_lote', $fila->lote)->first();
            $producto = DB::table('productos')->where('id', $fila->producto_id)->first();
            $color = DB::table('colores')->where('id', $fila->color_id)->first();
            $talla = DB::table('tallas')->where('id', $fila->talla_id)->first();


            array_push($data, array(
                'producto_id' => $fila->producto_id,
                'color_id'    => $fila->color_id,
                'talla_id'    => $fila->talla_id,
                'id' => $fila->id,
                'cantidad' => $fila->cantidad,
                // 'lote' => $lote->codigo_lote,
                'producto_nombre' => $producto->nombre,
                'talla_nombre' => $talla->descripcion,
                'color_nombre' => $color->descripcion,
                // 'fechavencimiento' => $fila->fecha_vencimiento,
                // 'costo' => $fila->costo,
                // 'valor_ingreso' => $fila->valor_ingreso,
            ));
        }

        $origenes =  General::find(28)->detalles;
        $destinos =  General::find(29)->detalles;
        //$lotes = DB::table('lote_productos')->get();
        $usuarios = User::get();
        $productos = Producto::where('estado', 'ACTIVO')->get();
        $monedas =  tipos_moneda();
        $modelos = Modelo::where('estado','ACTIVO')->get();
        $tallas = Talla::where('estado','ACTIVO')->get();
        $colores =  Color::where('estado','ACTIVO')->get();

        return view('almacenes.nota_ingresos.edit', [
            "fecha_hoy" => $fecha_hoy,
            "fecha_actual" => $fecha_actual,
            "fecha_5" => $fecha_5,
            "origenes" => $origenes,
            'destinos' => $destinos,
            'usuarios' => $usuarios,
            'productos' => $productos,
            //'lotes' => $lotes,
            'monedas' => $monedas,
            'notaingreso' => $notaingreso,
            'detalle' => json_encode($data),
            'modelos' => $modelos,
            'colores' => $colores,
            'tallas' => $tallas
        ]);
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
        $this->authorize('haveaccess','nota_ingreso.index');
        $data = $request->all();

        $rules = [
            'fecha' => 'required',
            'destino' => 'nullable',
            'origen' => 'required',
            'notadetalle_tabla' => 'required',


        ];

        $message = [

            'fecha.required' => 'El campo fecha  es Obligatorio',
            'origen.required' => 'El campo origen  es Obligatorio',
            'notadetalle_tabla.required' => 'No hay dispositivos',
        ];

        Validator::make($data, $rules, $message)->validate();

        //$registro_sanitario = new RegistroSanitario();
        $notaingreso = NotaIngreso::findOrFail($id);
        $notaingreso->fecha = $request->get('fecha');

        if($request->destino)
        {
             $destino = DB::table('tabladetalles')->where('id', $request->destino)->first();
             $notaingreso->destino = $destino->descripcion;
        }

        $dolar = (float)$notaingreso->dolar;

        $origen = DB::table('tabladetalles')->where('id', $request->origen)->first();
        $notaingreso->origen = $origen->descripcion;
        $notaingreso->usuario = Auth()->user()->usuario;
        $notaingreso->moneda = $request->get('moneda');
        $notaingreso->tipo_cambio = $dolar;
        $notaingreso->dolar = $dolar;
        $notaingreso->total = $request->get('monto_total');
        if($request->get('moneda') == 'DOLARES')
        {
            $notaingreso->total_soles = (float) $request->get('monto_total') * (float) $dolar;

            $notaingreso->total_dolares = (float) $request->get('monto_total');
        }
        else
        {
            $notaingreso->total_soles = (float) $request->get('monto_total');

            $notaingreso->total_dolares = (float) $request->get('monto_total') / $dolar;
        }
        $notaingreso->update();

        $articulosJSON = $request->get('notadetalle_tabla');
        $notatabla = json_decode($articulosJSON[0]);
        foreach ($notatabla as $fila) {
            if($request->get('moneda') == 'DOLARES')
            {
                $costo_soles = (float) $fila->costo * (float) $dolar;

                $costo_dolares = (float) $fila->costo;
            }
            else
            {
                $costo_soles = (float) $fila->costo;

                $costo_dolares = (float) $fila->costo / (float) $dolar;
            }
            $detalle = DetalleNotaIngreso::findOrFail($fila->id);
            $detalle->lote = $fila->lote;
            //$detalle->cantidad = $fila->cantidad;
            $detalle->producto_id = $fila->producto_id;
            $detalle->fecha_vencimiento = $fila->fechavencimiento;
            $detalle->costo = $fila->costo;
            $detalle->costo_soles = $costo_soles;
            $detalle->costo_dolares = $costo_dolares;
            $detalle->valor_ingreso = $fila->valor_ingreso;
            $detalle->update();
        }
        /*if ($notatabla != "") {
            foreach($notaingreso->lotes as $lot)
            {
                MovimientoNota::where('lote_id', $lot->id)->where('producto_id', $lot->producto_id)->where('nota_id', $lot->nota_ingreso_id)->where('movimiento', 'INGRESO')->delete();
                $lot->estado = '0';
                $lot->update();
            }
            DetalleNotaIngreso::where('nota_ingreso_id', $notaingreso->id)->delete();
            //LoteProducto::where('nota_ingreso_id', $notaingreso->id)->delete();
            foreach ($notatabla as $fila) {
                DetalleNotaIngreso::create([
                    'nota_ingreso_id' => $id,
                    'lote' => $fila->lote,
                    'cantidad' => $fila->cantidad,
                    'producto_id' => $fila->producto_id,
                    'fecha_vencimiento' => $fila->fechavencimiento
                ]);
            }
        }*/

        //Registro de actividad
        $descripcion = "SE ACTUALIZO NOTA DE INGRESO ";
        $gestion = "ALMACEN / NOTA INGRESO";
        crearRegistro($notaingreso, $descripcion, $gestion);


        Session::flash('success', 'NOTA DE INGRESO');
        return redirect()->route('almacenes.nota_ingreso.index')->with('guardar', 'success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('haveaccess','nota_ingreso.index');
        $notaingreso = NotaIngreso::findOrFail($id);
        $notaingreso->estado = "ANULADO";
        $notaingreso->save();
        // foreach($notaingreso->detalles as $detalle)
        // {

        // }
        Session::flash('success', 'NOTA DE INGRESO');
        return redirect()->route('almacenes.nota_ingreso.index')->with('guardar', 'success');
    }

    public function uploadnotaingreso(Request $request)
    {
        $data = array();
        $file = $request->file();
        $archivo = $file['files'][0];
        $objeto = new DataExcel();
        Excel::import($objeto, $archivo);

        $datos = $objeto->data;

        try {
            Excel::import(new ImportsNotaIngreso, $archivo);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $failures = $e->failures();

            foreach ($failures as $failure) {
                array_push($data, array(
                    "fila" => $failure->row(),
                    "atributo" => $failure->attribute(),
                    "error" => $failure->errors()
                ));
            }
            array_push($data, array("excel" => $datos));
        } catch (Exception $er) {
            Log::info($er);
            array_push($data, array(
                "fila" => 0,
                "atributo" => 'none',
                "error" => $er->getMessage()
            ));
        }

        return json_encode($data);
    }

    public function getDownload()
    {
        ob_end_clean(); // this
        ob_start();
        return  Excel::download(new ModeloExport, 'modelo_nota_ingreso.xlsx');
    }

    public function getProductosExcel()
    {
        ob_end_clean(); // this
        ob_start();
        return  Excel::download(new ProductosExport, 'productos.xlsx');
    }
    public function getErrorExcel(Request $request)
    {
        ob_end_clean(); // this
        ob_start();
        $errores = array();
        $datos = json_decode(($request->arregloerrores));
        for ($i = 0; $i < count($datos) - 1; $i++) {
            array_push($errores, array(
                "fila" => $datos[$i]->fila,
                "atributo" => $datos[$i]->atributo,
                "error" => $datos[$i]->error
            ));
        }
        $data = $datos[count($datos) - 1]->excel;

        return  Excel::download(new ErrorExcel($data, $errores), 'excel_error.xlsx');
    }


    public function getProductos($modelo_id){

        try {
            $productos      =       DB::select('select p.nombre as producto_nombre,c.descripcion as color_nombre,t.descripcion as talla_nombre, 
                                    pct.stock,pct.stock_logico,p.id as producto_id, c.id as color_id,t.id as talla_id 
                                    from producto_colores as pc 
                                    inner join productos as p on p.id=pc.producto_id 
                                    inner join colores as c on c.id=pc.color_id 
                                    left join producto_color_tallas as pct on (pc.color_id=pct.color_id and pc.producto_id=pct.producto_id) 
                                    left join tallas as t on t.id = pct.talla_id 
                                    where p.modelo_id=? and p.estado="ACTIVO";
                                    ',[$modelo_id]);

            return response()->json(['success'=>true,'productos'=>$productos]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>'ERROR AL OBTENER LOS PRODUCTOS EN EL SERVIDOR',
            'exception'=>$th->getMessage()]);
        }

    }


    public function generarEtiquetas($nota_id){

        try {
            $nota_detalle   =   DB::select('select p.nombre as producto_nombre,c.descripcion as color_nombre,
                                t.descripcion as talla_nombre,m.descripcion as modelo_nombre,pct.ruta_cod_barras,dni.cantidad,
                                p.id as producto_id,c.id as color_id,t.id as talla_id,m.id as modelo_id,
                                ca.descripcion as categoria_nombre
                                from detalle_nota_ingreso as dni
                                inner join productos as p on p.id=dni.producto_id
                                inner join colores as c on c.id=dni.color_id
                                inner join tallas as t on t.id=dni.talla_id
                                inner join modelos as m on m.id=p.modelo_id
                                inner join categorias as ca on ca.id=p.categoria_id
                                inner join producto_color_tallas as pct on (pct.producto_id=p.id and pct.color_id=c.id and pct.talla_id=t.id)
                                where dni.nota_ingreso_id=?',[$nota_id]);
            
            $empresa        =   Empresa::first();
          
            
            $width_in_points    = 160 * 72 / 25.4;  // Ancho en puntos 5cm = 50 mm
            $height_in_points   = 100 * 72 / 25.4; // Alto en puntos
                                
            // Establecer el tamaño del papel
            $custom_paper = array(0, 0, $width_in_points, $height_in_points);
            $pdf = PDF::loadview('almacenes.productos.pdf.adhesivo', [
                                    'nota_id'       =>  $nota_id,
                                    'nota_detalle'  =>  $nota_detalle,
                                    'empresa'       =>  $empresa
                                    ])->setPaper($custom_paper)
                                    ->setWarnings(false);
                             
            return $pdf->stream('etiquetas.pdf');
        } catch (\Throwable $th) {
            Session::flash('nota_ingreso_error_message','ERROR AL GENERAR LAS ETIQUETAS ADHESIVAS');
            Session::flash('nota_ingreso_error_exception',$th->getMessage());

            return redirect()->route('almacenes.nota_ingreso.index');
        }
       
    }

    public function generarCodigoBarras($item){
        $producto   =   DB::select('select * from producto_color_tallas as pct
                        where pct.producto_id = ? and
                        pct.color_id = ? and pct.talla_id = ?',[$item->producto_id,
                        $item->color_id,$item->talla_id]);
        
        //======== SI EL PRODUCTO YA EXISTE ========
        if(count($producto)>0){
            //========== REVIZAR QUE NO TENGA COD BARRAS GENERADO =======
            if(!$producto[0]->codigo_barras && !$producto[0]->ruta_cod_barras){
                //========= GENERAR IDENTIFICADOR ÚNICO PARA EL COD BARRAS ========
                $key            =   generarCodigo(8);
                //======== GENERAR IMG DEL COD BARRAS ========
                $generatorPNG   =   new \Picqer\Barcode\BarcodeGeneratorPNG();
                $code           =   $generatorPNG->getBarcode($key, $generatorPNG::TYPE_CODE_128);
                //$data_code      =   base64_decode($code);
                $name           =   $key.'.png';
        
                if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'))) {
                    mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'));
                }
        
                $pathToFile = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'productos'.DIRECTORY_SEPARATOR.$name);
        
                file_put_contents($pathToFile, $code);

                //======== GUARDAR KEY Y RUTA IMG ========
                ProductoColorTalla::where('producto_id', $item->producto_id)
                ->where('color_id', $item->color_id)
                ->where('talla_id', $item->talla_id)
                ->update([
                    'codigo_barras'         =>  $key,
                    'ruta_cod_barras'       =>  'public/productos/'.$name  
                ]);
            }
        }
    }


}
