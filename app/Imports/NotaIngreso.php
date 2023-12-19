<?php

namespace App\Imports;

use App\Almacenes\DetalleNotaIngreso;
use App\Almacenes\LoteProducto;
use App\Almacenes\MovimientoNota;
use App\Almacenes\NotaIngreso as AlmacenesNotaIngreso;
use App\Almacenes\Producto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;

class NotaIngreso implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        try
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

            $nota = AlmacenesNotaIngreso::create([
                'numero' => $numero,
                'fecha' => $fecha_hoy,
                'destino' => 'ALMACEN',
                'moneda' => 'SOLES',
                'tipo_cambio' => $dolar,
                'dolar' => $dolar,
                'origen' => 'IMPORT EXCEL',
                'usuario' => Auth()->user()->usuario
            ]);

            $total = 0;

            foreach ($collection as $row) {
                if ($row['codigo'] != null) {
                    $producto = DB::table('productos')->where('codigo', $row['codigo'])->first();

                    if($row['costo_total'] < 0)
                    {
                        $row['costo_total'] = (-1) * $row['costo_total'];
                    }
                    $costo_soles = (float)  $row['costo_total'] / (float) $row['cantidad'];
                    $costo_dolares = $costo_soles / (float) $dolar;

                    DetalleNotaIngreso::create([
                        'nota_ingreso_id' => $nota->id,
                        'lote' => $row['codigo_lote'],
                        'cantidad' => $row['cantidad'],
                        'producto_id' => $producto->id,
                        'fecha_vencimiento' => $fecha_5,
                        'costo' => $costo_soles,
                        'costo_soles' => $costo_soles,
                        'costo_dolares' => $costo_dolares,
                        'valor_ingreso' => (float) $row['costo_total']
                    ]);

                    $total = $total + (float) $row['costo_total'];
                }
            }

            $nota_ingreso = AlmacenesNotaIngreso::find($nota->id);
            $nota_ingreso->total = $total;
            $nota_ingreso->total_soles = $total;
            $nota_ingreso->total_dolares = $total / $dolar;
            $nota_ingreso->update();
        }
        catch(Exception $e)
        {
            Log::info($e->getMessage());
        }
    }
    public function rules(): array
    {
        return [
            'codigo' => function ($attribute, $value, $onFailure) {

                $valor = DB::table('productos')->where('codigo', $value)->count();

                if ($valor == 0) {

                    $onFailure('No existe este Producto');
                }
            }
        ];
    }
}
