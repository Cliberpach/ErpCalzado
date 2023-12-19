<?php

namespace App\Exports\Producto;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use App\Almacenes\Marca;
use App\Almacenes\Producto;
use App\Mantenimiento\Tabla\Detalle;
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
        $productos= Producto::where('estado','ACTIVO')->orderBy('stock','desc')->get();
        $data=array();
        foreach($productos as $producto)
        {
            $categoria=Categoria::where('id',$producto->categoria_id)->first();
            $medida=Detalle::where('id',$producto->medida_max)->first();
            $marca=Marca::where('id',$producto->marca_id)->first();
            $almacen=Almacen::where('id',$producto->almacen_id)->first();
            array_push($data,array(
                "codigo"=>$producto->codigo,
                "nombre"=>$producto->nombre,
                "unidad"=>$producto->medidaCompleta(),
                "categoria"=>$categoria->descripcion,
                "marca"=>$marca->marca,
                "almacen"=>$almacen->descripcion,
                "codigo_barra"=>$producto->codigo_barra,
                "stock"=>$producto->stock,

            ));
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            ['codigo',
            'nombre',
            'unidad',
            'categoria',
            'marca',
            'almacen',
            'codigo_barra',
            'stock',
            ]
        ]
       ;
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:H1')->applyFromArray([


                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startColor' => [
                            'argb' => '1ab394',
                        ],
                        'endColor' => [
                            'argb' => '1ab394',
                        ],
                    ],


                ]

                );

               // $event->sheet->getColumnDimension('C')->setWidth(20);

            },
        ];
    }
}
