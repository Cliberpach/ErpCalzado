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

    public function llenarCompras($id)
    {
        $compras = Detalle::where('producto_id', $id)->where('estado', 'ACTIVO')->orderBy('id', 'desc')->get();
        $coleccion = collect([]);
        foreach ($compras as $producto) {
            $coleccion->push([
                'proveedor' => $producto->documento->proveedor->descripcion,
                'documento' => $producto->documento->tipo_compra,
                'numero' => $producto->documento->serie_tipo . '-' . $producto->documento->numero_tipo,
                'fecha_emision' => $producto->documento->fecha_emision,
                'cantidad' => $producto->cantidad,
                'precio' => $producto->precio_soles,
                'lote' => $producto->lote,
                'fecha_vencimiento' => $producto->fecha_vencimiento,
                'medida' => $producto->loteProducto->producto->medidaCompleta(),
            ]);
        }
        return DataTables::of($coleccion)->make(true);
    }

    public function llenarVentas($id)
    {
        ini_set('memory_limit', '1024M');
        try{
            $ventas = DocumentoDetalle::orderBy('id', 'desc')
            ->where('estado', 'ACTIVO')
            ->where("eliminado","0")
            ->get();
            $coleccion = collect([]);
            foreach ($ventas as $producto) {
                if ($producto->lote->producto_id == $id) {
                    $coleccion->push([
                        'cliente' => $producto->documento->clienteEntidad->nombre,
                        'documento' => $producto->documento->nombreTipo(),
                        'numero' => $producto->documento->serie . '-' . $producto->documento->correlativo,
                        'fecha_emision' => $producto->documento->fecha_atencion,
                        'cantidad' => $producto->cantidad,
                        'precio' => $producto->precio_nuevo,
                        'lote' => $producto->lote->codigo_lote,
                        'fecha_vencimiento' => $producto->documento->fecha_vencimiento,
                        'medida' => $producto->lote->producto->medidaCompleta(),
                    ]);
                }
            }
            return DataTables::of($coleccion)->make(true);
        }catch(\Exception $ex){
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

    public function llenarSalidas($id)
    {
        $salidas = DB::table('detalle_nota_salidad')
        ->join('nota_salidad', 'nota_salidad.id', '=', 'detalle_nota_salidad.nota_salidad_id')
        ->join('productos', 'productos.id','=', 'detalle_nota_salidad.producto_id')
        ->join('tabladetalles', 'tabladetalles.id','=','productos.medida')
        ->join('lote_productos', 'lote_productos.id', '=', 'detalle_nota_salidad.lote_id')
        ->select(
            'detalle_nota_salidad.cantidad',
            'nota_salidad.origen',
            'nota_salidad.destino',
            'lote_productos.codigo_lote',
            'tabladetalles.descripcion as unidad'
        )
        ->where('detalle_nota_salidad.producto_id', $id)    
        ->where('nota_salidad.estado', '!=', 'ANULADO')->get();
        $coleccion = collect([]);
        foreach ($salidas as $salida) {
            $coleccion->push([
                'origen' => $salida->origen,
                'destino' => $salida->destino,
                'cantidad' => $salida->cantidad,
                'lote' => $salida->codigo_lote,
                'medida' => $salida->unidad,
            ]);
        }
        return DataTables::of($coleccion)->make(true);
    }

    public function llenarIngresos($id)
    {
        $ingresos = DetalleNotaIngreso::orderBy('id', 'desc')->where('producto_id', $id)->get();
        $coleccion = collect([]);
        foreach ($ingresos as $ingreso) {
            $coleccion->push([
                'origen' => $ingreso->nota_ingreso->origen,
                'numero' => $ingreso->nota_ingreso->numero,
                'destino' => $ingreso->nota_ingreso->destino,
                'cantidad' => $ingreso->cantidad,
                'costo' => $ingreso->costo_soles,
                'nombre' => $ingreso->producto->nombre,
                'total' => $ingreso->valor_ingreso,
                'nota_ingreso_id' => $ingreso->nota_ingreso->id,
                'id' => $ingreso->id,
                'moneda' => $ingreso->nota_ingreso->moneda,
                'medida' => $ingreso->loteProducto->producto->medidaCompleta(),
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
}
