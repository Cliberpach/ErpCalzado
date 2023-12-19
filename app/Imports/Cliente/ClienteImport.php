<?php

namespace App\Imports\Cliente;


use App\Mantenimiento\Tabla\General;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
use App\Ventas\Cliente;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
class ClienteImport implements ToCollection,WithHeadingRow,WithValidation
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

        foreach($collection as $row)
        {
            Log::info($row);
            // if(isset($row['nombre']))
            // {
            //     $tiposcliente= General::find(17)->detalles()->where('descripcion',$row['tipo_cliente'])->first();
            //     $departamento= Departamento::where('nombre',$row['departamento'])->first();
            //     $provincia= Provincia::where('nombre',$row['provincia'])->first();
            //     $distrito= Distrito::where('nombre',$row['distrito'])->first();
            //     $tiposDocumento= General::find(3)->detalles()->where('simbolo',$row['tipo_documento'])->first();
            //     Log::info($row);
            //     Log::info($tiposcliente);
            //     Log::info($departamento);
            //     Log::info($provincia);
            //     Log::info($distrito);
            //     Cliente::create([
            //         'tipo_documento'=>$row['tipo_documento'],
            //         'tabladetalles_id'=>$tiposcliente->id,
            //         'documento'=>$row['documento'],
            //         'nombre'=>$row['nombre'],
            //         'nombre_comercial'=>$row['nombre_comercial'],
            //         'departamento_id'=>$departamento->id,
            //         'provincia_id'=>$provincia->id,
            //         'distrito_id'=>$distrito->id,
            //         'direccion'=>$row['direccion'],
            //         'zona'=>$row['zona'],
            //         'correo_electronico'=>$row['correo_electronico'],
            //         'telefono_movil'=>$row['telefono_movil'],
            //         'telefono_fijo'=>$row['telefono_fijo'],
            //     ]);
            // }

        }

    }
    public function rules(): array
    {
        return [
            //     'codigo' => function($attribute, $value, $onFailure) {

            //      $valor=DB::table('productos')->where('codigo',$value)->count();

            //       if ($valor === 0) {

            //            $onFailure('No existe este Producto');
            //       }
            //   }
        ];
    }

}
