<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ModeloExport implements FromArray,WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $data=array();
        array_push($data,  ['codigo'=>'1001',
        'codigo_lote'=>'LT-29/12/2021',
        'cantidad'=>'10',
        'costo_total'=>'100',
        'fecha_vencimiento'=>'2021-04-30'
        ]);
        return $data;
    }
    public function headings(): array
    {
        return [
            ['codigo',
            'codigo_lote',
            'cantidad',
            'costo_total',
            'fecha_vencimiento'
            ]
        ]
       ;
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:E1')->applyFromArray([


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
                $event->sheet->getStyle('A1:E1')->applyFromArray([
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
