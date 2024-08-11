<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Mantenimiento\Empresa\Empresa;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class OrdenPedidoController extends Controller
{
    public function index(){
        return view('pedidos.ordenes.index');
    }

    public function getTable(Request $request){
        
        $ordenes_pedido =   DB::select('select CONCAT("OP","-",op.id) as op_id_name,
                            op.id, op.user_id, op.user_nombre,op.fecha_propuesta_atencion,op.observacion,
                            op.created_at as fecha_registro,op.estado 
                            from ordenes_pedido as op 
                            where op.estado != "ANULADO"');

        
        return DataTables::of($ordenes_pedido)->toJson();
    }

    public function getDetalle($orden_pedido_id){
        try {
            $orden_pedido_detalle   =   DB::select('select * 
                                        from ordenes_pedido_detalles as opd
                                        where opd.orden_pedido_id = ?',[$orden_pedido_id]);
                            
            return response()->json(['success'=>true,'orden_pedido_detalle'=>$orden_pedido_detalle]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function pdf($orden_pedido_id){
        try {
            $orden_pedido_detalle       =   DB::select('select * from ordenes_pedido_detalles as opd
                                            where opd.orden_pedido_id = ?',[$orden_pedido_id]);

            $lstProgramacionProduccion  =   $this->formatearOrdenPedidoDetalle($orden_pedido_detalle);

            $tallasBD                   =   DB::select('select t.id,t.descripcion from tallas as t
                                            where t.estado ="ACTIVO"');

            $empresa                    =   Empresa::first();

            $usuario_impresion_nombre   =   Auth::user()->usuario;
            $fecha_actual               =   Carbon::now();

            
            $pdf = PDF::loadview('pedidos.detalles.pdf.prog_produccion', [
                'lstProgramacionProduccion'     =>  $lstProgramacionProduccion,
                'tallasBD'                      =>  $tallasBD,
                'empresa'                       =>  $empresa,
                'usuario_impresion_nombre'      =>  $usuario_impresion_nombre,
                'fecha_actual'                =>  $fecha_actual
            ])->setPaper('a4', 'landscape')->setWarnings(false);
            
            return $pdf->stream('PROGRAMACIÓN_PRODUCCIÓN.pdf');

        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function formatearOrdenPedidoDetalle($orden_pedido_detalle){
        $productos = [];
    
        foreach ($orden_pedido_detalle as $detalle) {
            // Verificar si el producto ya existe en el array $productos
            if (!isset($productos[$detalle->producto_id])) {
                $productos[$detalle->producto_id] = (object)[
                    "modelo" => (object)[
                        "id" => $detalle->modelo_id,
                        "nombre" => $detalle->modelo_nombre
                    ],
                    "id" => $detalle->producto_id,
                    "nombre" => $detalle->producto_nombre,
                    "colores" => []
                ];
            }
    
            // Referencia al producto actual
            $producto = $productos[$detalle->producto_id];
    
            // Verificar si el color ya existe en el array $colores del producto
            if (!isset($producto->colores[$detalle->color_id])) {
                $producto->colores[$detalle->color_id] = (object)[
                    "id" => $detalle->color_id,
                    "nombre" => $detalle->color_nombre,
                    "tallas" => []
                ];
            }
    
            // Agregar la talla al color correspondiente
            $producto->colores[$detalle->color_id]->tallas[] = (object)[
                "id" => $detalle->talla_id,
                "nombre" => $detalle->talla_nombre,
                "cantidad_pendiente" => $detalle->cantidad
            ];
    
            // Actualizar el producto en el array
            $productos[$detalle->producto_id] = $producto;
        }
    
        // Convertir los índices del array a un formato plano (opcional)
        $productos = array_values(array_map(function($producto) {
            $producto->colores = array_values($producto->colores);
            return $producto;
        }, $productos));
    
        return $productos;
    }
    

}
