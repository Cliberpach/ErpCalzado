<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ErrorExcel implements FromArray,ShouldAutoSize,WithHeadings,WithEvents
{
    public $datos=array();
    public $errores=array();
    public $columna=   ['codigo'=>"A",
        'codigo_lote'=>"B",
        'cantidad'=>"C",
        'fecha_vencimiento'=>"D",
        'fecha_entrega'=>"E",
        'fecha_produccion'=>"E",
    ];
    public function __construct($datos,$errores)
    {
        $this->datos=$datos;
        $this->errores=$errores;

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        return $this->datos;
    }
    public function headings(): array
    {
        return [
            ['codigo',
            'codigo_lote',
            'cantidad',
            'fecha_vencimiento',
            'fecha_entrega',
            'fecha_produccion'
            ]
        ]
       ;
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('B1:E1')->applyFromArray([


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
                $event->sheet->getStyle('A1:A1')->applyFromArray([


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
                for($i=0;$i<count($this->errores);$i++)
                {
                    $event->sheet->getStyle(($this->columna[$this->errores[$i]['atributo']]).($this->errores[$i]['fila']))->applyFromArray([


                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                                'color' => ['argb' => 'fc0303'],
                            ],
                        ]


                    ]

                    );
                }




               // $event->sheet->getColumnDimension('C')->setWidth(20);

            },
        ];
    }
}
