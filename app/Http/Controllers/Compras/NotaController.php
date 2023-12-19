<?php

namespace App\Http\Controllers\Compras;

use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use App\Compras\Documento\Detalle;
use App\Compras\Documento\Documento;
use App\Compras\Nota;
use App\Compras\NotaDetalle;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Empresa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Luecano\NumeroALetras\NumeroALetras;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;

class NotaController extends Controller
{
    public function index($id)
    {
        $documento = Documento::find($id);
        return view('compras.notas.index', compact('documento'));
    }

    public function getNotes($id)
    {
        $notas = Nota::where('documento_id', $id)->orderBy('id', 'DESC')->get();

        $coleccion = collect([]);
        foreach ($notas as $nota) {

            $coleccion->push([
                'id' => $nota->id,
                'documento_afectado' => $nota->numDocfectado,
                'fecha_emision' =>  $nota->fechaEmision,
                'numero' =>  $nota->serie . '-' . $nota->correlativo,
                'cliente' => $nota->proveedor,
                'monto' => 'S/. ' . number_format($nota->mtoImpVenta, 2, '.', ''),
                'tipo_nota' => "02",
                'estado' => $nota->estado,
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function create(Request $request)
    {
        $documento = Documento::findOrFail($request->documento_id);
        $fecha_hoy = Carbon::now()->toDateString();

        return view('compras.notas.credito.create', [
            'documento' => $documento,
            'fecha_hoy' => $fecha_hoy,
        ]);
    }

    public function getDetalles($id)
    {

        $detalles = Detalle::where('estado', 'ACTIVO')->where('documento_id', $id)->get();
        $coleccion = collect();
        foreach ($detalles as $item) {
            $cant_nota_electronica = 0;
            foreach ($item->loteProducto->detalles_venta as $detail) {
                $cant_nota_electronica = $cant_nota_electronica + $detail->detalles->sum("cantidad");
            }
            $cantidad = $item->loteProducto->cantidad_inicial + $cant_nota_electronica - $item->loteProducto->detalles_venta->sum("cantidad") - $item->loteProducto->detalles_salida->sum("cantidad") - $item->detalles->sum('cantidad');
            if ($cantidad > 0) {
                $coleccion->push([
                    'id' => $item->id,
                    'cantidad' => $cantidad,
                    'descripcion' => $item->producto->nombre,
                    'precio_unitario' => $item->precio,
                    'importe_venta' => $item->precio * $cantidad,
                    'editable' => 0
                ]);
            }
        }
        //return DataTables::of($coleccion)->make(true);

        return response()->json([
            'success' => true,
            'detalles' => $coleccion
        ]);
    }

    public function convertirTotal($total)
    {
        $formatter = new NumeroALetras();
        $convertir = $formatter->toInvoice($total, 2, 'SOLES');
        return $convertir;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $rules = [
                'documento_id' => 'required',
                'fecha_emision' => 'required',
                'proveedor' => 'required',
                'des_motivo' => 'required',
                'serie' => 'required',
                'numero' => 'required',

            ];
            $message = [
                'fecha_emision.required' => 'El campo Fecha de Emisión es obligatorio.',
                'des_motivo.required' => 'El campo motivo es obligatorio.',
                'proveedor.required' => 'El campo Proveedor es obligatorio.',
                'serie.required' => 'El campo serie es obligatorio.',
                'numero.required' => 'El campo numero es obligatorio.',
                'des_motivo.required' => 'El campo Motivo es obligatorio.',
            ];

            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => true,
                    'data' => array('mensajes' => $validator->getMessageBag()->toArray())
                ]);
            }

            $documento = Documento::find($request->get('documento_id'));

            $igv = $documento->igv ? $documento->igv : 18;

            $nota = new Nota();
            $nota->documento_id = $documento->id;
            $nota->tipoDoc = "0";
            $nota->serie = $request->serie;
            $nota->correlativo = $request->numero;
            $nota->numDocfectado = $documento->serie_tipo . '-' . $documento->numero_tipo;
            $nota->desMotivo =  $request->get('des_motivo');

            $nota->tipoMoneda = $documento->moneda == "SOLES" ? "PEN" : "USD";

            $nota->fechaEmision = $request->get('fecha_emision');

            //PROVEDOR
            $nota->cod_tipo_documento_proveedor =  $documento->proveedor->tipo_documento == "DNI" ? "03" : "01";
            $nota->tipo_documento_proveedor =  $documento->proveedor->tipo_documento;
            $nota->documento_proveedor =  $documento->proveedor->dni != "" ? $documento->proveedor->dni : $documento->proveedor->ruc;
            $nota->direccion_proveedor =  $documento->proveedor->direccion;
            $nota->proveedor =  $documento->proveedor->descripcion;

            $nota->mtoOperGravadas = $request->get('sub_total_nuevo');
            $nota->mtoIGV = $request->get('total_igv_nuevo');
            $nota->totalImpuestos = $request->get('total_igv_nuevo');
            $nota->mtoImpVenta =  $request->get('total_nuevo');

            $nota->value = self::convertirTotal($request->get('total_nuevo'));
            $nota->code = '1000';
            $nota->user_id = auth()->user()->id;
            $nota->save();

            //Llenado de los articulos
            $productosJSON = $request->get('productos_tabla');
            $productotabla = json_decode($productosJSON);

            foreach ($productotabla as $producto) {
               if($producto->editable == 1)
               {
                    $detalle = Detalle::find($producto->id);
                    $lote = LoteProducto::findOrFail($detalle->lote_id);
                    NotaDetalle::create([
                        'nota_id' => $nota->id,
                        'detalle_id' => $detalle->id,
                        'codProducto' => $lote->producto->codigo,
                        'unidad' => $lote->producto->getMedida(),
                        'descripcion' => $lote->producto->nombre . ' - ' . $lote->codigo,
                        'cantidad' => $producto->cantidad,

                        'mtoBaseIgv' => ($producto->precio_unitario / (1 + ($documento->igv / 100))) * $producto->cantidad,
                        'porcentajeIgv' => 18,
                        'igv' => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv / 100)))) * $producto->cantidad,
                        'tipAfeIgv' => 10,

                        'totalImpuestos' => ($producto->precio_unitario - ($producto->precio_unitario / (1 + ($documento->igv / 100)))) * $producto->cantidad,
                        'mtoValorVenta' => ($producto->precio_unitario / (1 + ($documento->igv / 100))) * $producto->cantidad,
                        'mtoValorUnitario' =>  $producto->precio_unitario / (1 + ($documento->igv / 100)),
                        'mtoPrecioUnitario' => $producto->precio_unitario,
                    ]);

                    if ($lote->cantidad - $producto->cantidad < 0) {
                        return response()->json([
                            'errors' => true,
                            'data' => array('mensajes' => array("Mensaje" => ["El lote del producto " . $lote->producto->nombre . "tiene una cantidad de " . $lote->cantidad . " en stock, es decir ya tiene ventas o salidas registradas, no puedes hacer la devolucion total de este detalle"]))
                        ]);
                    }

                    $lote->cantidad = $lote->cantidad - $producto->cantidad;
                    $lote->cantidad_logica = $lote->cantidad_logica - $producto->cantidad;
                    $lote->update();
               }
            }

            //Registro de actividad
            $descripcion = "SE AGREGÓ UNA NOTA DE CRÉDITO CON LA FECHA: " . Carbon::parse($nota->fechaEmision)->format('d/m/y');
            $gestion = "NOTA DE DEBITO";
            crearRegistro($nota, $descripcion, $gestion);

            DB::commit();

            $text = 'Nota de crédito creada';

            Session::flash('success', $text);
            return response()->json([
                'success' => true,
                'nota_id' => $nota->id
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
                'excepcion' => $e->getMessage()
            ]);
        }
    }

    public function obtenerLeyenda($nota)
    {
        //CREAR LEYENDA DEL COMPROBANTE
        $arrayLeyenda = array();
        $arrayLeyenda[] = array(
            "code" => $nota->code,
            "value" => $nota->value
        );
        return $arrayLeyenda;
    }

    public function show($id)
    {
        $nota = Nota::with(['documento'])->findOrFail($id);
        $empresa = Empresa::first();
        $detalles = NotaDetalle::where('nota_id', $id)->get();

        //file_put_contents($pathToFile, $data);
        //return response()->file($pathToFile);
        $legends = self::obtenerLeyenda($nota);
        $legends = json_encode($legends, true);
        $legends = json_decode($legends, true);

        $name = $nota->serie . '-' . $nota->correlativo . '.pdf';

        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantessiscom'))) {
            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantessiscom'));
        }

        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantessiscom' . DIRECTORY_SEPARATOR . 'notas'))) {
            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantessiscom' . DIRECTORY_SEPARATOR . 'notas'));
        }

        $pdf = PDF::loadview('compras.notas.impresion.comprobante_normal_nuevo', [
            'nota' => $nota,
            'detalles' => $detalles,
            'moneda' => $nota->tipoMoneda,
            'empresa' => $empresa,
            "legends" =>  $legends,
        ])->setPaper('a4')->setWarnings(false);

        $pdf->save(public_path() . '/storage/comprobantessiscom/notas/' . $name);
        return $pdf->stream($name);
    }

}
