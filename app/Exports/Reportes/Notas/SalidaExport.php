<?php

namespace App\Exports\Reportes\Notas;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SalidaExport implements FromCollection,WithHeadings,WithEvents
{
    public $producto,$destino,$fecha_ini,$fecha_fin;
    use Exportable;

    public function headings(): array
    {
        return [
            [
                "FECHA",
                "PRODUCTO",
                "CANTIDAD",
                "DESTINO",
            ]
        ];
    }

    function title(): String
    {
        return "MOVIMIENTO CAJA ".$this->fecha_ini."-".$this->fecha_fin;
    }

    public function __construct($producto,$destino,$fecha_ini,$fecha_fin)
    {
        $this->producto = $producto;
        $this->destino = $destino;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $consulta = DB::table('productos')
        ->join('detalle_nota_salidad','productos.id','=','detalle_nota_salidad.producto_id')
        ->join('nota_salidad','nota_salidad.id','=','detalle_nota_salidad.nota_salidad_id')
        ->select(
            DB::raw('DATE_FORMAT(nota_salidad.created_at, "%Y-%m-%d") as fecha'),
            'productos.nombre',
            'detalle_nota_salidad.cantidad',
            'nota_salidad.destino',
        );

        if($this->producto)
        {
            $consulta = $consulta->where('productos.id',$this->producto);
        }

        if($this->destino)
        {
            $consulta = $consulta->where('nota_salidad.destino',$this->destino);
        }

        if($this->fecha_ini && $this->fecha_fin)
        {
            $consulta = $consulta->whereBetween(DB::raw('DATE_FORMAT(nota_salidad.created_at, "%Y-%m-%d")'),[$this->fecha_ini,$this->fecha_fin]);
        }

        return $consulta->get();
    }

    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:D1')->applyFromArray([

                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startColor' => [
                                'argb' => '00bbd4',
                            ],
                            'endColor' => [
                                'argb' => '00bbd4',
                            ],
                        ],


                    ]

                );
                $event->sheet->getStyle('A1:D1')->applyFromArray([
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



            $event->sheet->getColumnDimension('A')->setWidth(20);
            $event->sheet->getColumnDimension('B')->setWidth(30);
            $event->sheet->getColumnDimension('C')->setWidth(15);
            //    $event->sheet->getColumnDimension('D')->setWidth(20);
            //    $event->sheet->getColumnDimension('E')->setWidth(20);
            //    $event->sheet->getColumnDimension('F')->setWidth(20);
            //    $event->sheet->getColumnDimension('G')->setWidth(20);
            //    $event->sheet->getColumnDimension(''H)->setWidth(20);

            },
        ];
    }
}
