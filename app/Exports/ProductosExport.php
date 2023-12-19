<?php

namespace App\Exports;

use App\Almacenes\Producto;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductosExport implements ShouldAutoSize,WithHeadings,FromArray,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $productos= DB::table('productos as p')->where('p.estado','ACTIVO')->get();
        $data=array();
        $fecha_hoy = Carbon::now()->toDateString();
        $fecha = Carbon::createFromFormat('Y-m-d', $fecha_hoy);
        $fecha = str_replace("-", "", $fecha);
        $fecha = str_replace(" ", "", $fecha);
        $fecha = str_replace(":", "", $fecha);

        $fecha_actual = Carbon::now();
        $fecha_actual = date("d/m/Y", strtotime($fecha_actual));
        $fecha_5 = date("Y-m-d", strtotime($fecha_hoy . "+ 5 years"));
        foreach($productos as $producto)
        {
            $categoria=DB::table('categorias')->where('id',$producto->categoria_id)->first();
            $medida=DB::table('tabladetalles')->where('id',$producto->medida)->first();
            $marca=DB::table('marcas')->where('id',$producto->marca_id)->first();
            $almacen=DB::table('almacenes')->where('id',$producto->almacen_id)->first();
            array_push($data,array(
                "codigo"=>$producto->codigo,
                "nombre"=>$producto->nombre,
                "descripcion"=>$producto->descripcion,
                "categoria"=>$categoria->descripcion,
                "medida"=>$medida->descripcion,
                "marca"=>$marca->marca,
                "almacen"=>$almacen->descripcion,
                "codigo_barra"=>$producto->codigo_barra,
                "igv"=>($producto->igv=="1") ? "SI": "NO",
                "fecha_vencimiento"=>$fecha_5,
                "codigo_lote"=>"L-".$fecha_5,
                "cantidad"=>"",
                "costo_total"=>"",

            ));
        }
        /*->join('familias as f','f.id','p.familia_id')
        ->join('subfamilias as sf','sf.id','p.sub_familia_id')
        ->select('p.codigo','p.nombre','p.descripcion'
                  ,'f.familia','sf.descripcion')
        ->where('p.estado','ACTIVO')->get();*/
        return $data;
    }

    public function headings(): array
    {
        return [
            [
                'codigo',
                'nombre',
                'descripcion',
                'categoria',
                'medida',
                'marca',
                'almacen',
                'codigo_barra',
                'igv',
                'fecha_vencimiento',
                'codigo_lote',
                'cantidad',
                'costo_total',
            ]
        ]
       ;
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('K1:M1')->applyFromArray([


                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startColor' => [
                            'argb' => 'ffffff',
                        ],
                        'endColor' => [
                            'argb' => 'ffffff',
                        ],
                    ],


                ]

                );
                $event->sheet->getStyle('A1:M1')->applyFromArray([


                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startColor' => [
                            'argb' => 'ffffff',
                        ],
                        'endColor' => [
                            'argb' => 'ffffff',
                        ],
                    ],


                ]

                );



               // $event->sheet->getColumnDimension('C')->setWidth(20);

            },
        ];
    }
}
