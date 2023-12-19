<?php

namespace App\Exports\Cliente;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ClienteExport implements fromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = array();
        array_push($data,[
            'tipo_documento'=>'DNI',
            'tipo_cliente'=>'NORMAL',
            'documento'=>'14567822',
            'nombre'=>'JUAN RODRIGUEZ SANCHEZ',
            'nombre_comercial'=>'JUAN RODRIGUEZ SANCHEZ',
            'departamento'=>'LA LIBERTAD',
            'provincia'=>'PACASMAYO',
            'distrito'=>'SAN JOSE',
            'direccion'=>'CALLE PRUEBA',
            'zona'=>'NORTE',
            'correo_electronico'=>'corre@prueba.com',
            'telefono_movil'=>'989956565',
            'telefono_fijo'=>'9894888885',
        ]);
        return $data;
    }
    public function headings(): array
    {
        return [
            [
                'tipo_documento',
                'tipo_cliente',
                'documento',
                'nombre',
                'nombre_comercial',
                'departamento',
                'provincia',
                'distrito',
                'direccion',
                'zona',
                'correo_electronico',
                'telefono_movil',
                'telefono_fijo',
            ]
        ];
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:M1')->applyFromArray(
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
                for ($j = 2; $j < 100; $j++) {
                    $validation = $event->sheet->getCell('B' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('listCombobox!$A$2:$A$'.(tipo_clientes()->count()+1));

                    $validation = $event->sheet->getCell('F' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('listCombobox!$B$2:$B$'.(departamentos()->count()+1));

                    $validation = $event->sheet->getCell('G' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('listCombobox!$C$2:$C$'.(provincias()->count()+1));

                    $validation = $event->sheet->getCell('H' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('listCombobox!$D$2:$D$'.(distritos()->count()+1));

                    $validation = $event->sheet->getCell('A' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('listCombobox!$E$2:$E$'.(tipos_documento()->count()+1));
                }

            },
        ];
    }
}
