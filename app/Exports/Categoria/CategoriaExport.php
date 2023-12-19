<?php

namespace App\Exports\Categoria;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class CategoriaExport implements fromArray, WithHeadings, ShouldAutoSize, WithEvents,WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = array();
        array_push($data,[
            'rubro'=>'MOTORES',
        ]);
        return $data;
    }
    function title():String{
        return "Categorias";
    }
    public function headings(): array
    {
        return [
            [
                'rubro',
            ]
        ];
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:A1')->applyFromArray(
                    [
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

            },
        ];
    }
}
