<?php

namespace App\Exports\Proveedor;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use App\Almacenes\Marca;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ProveedorExport implements fromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    function title():String{
        return "productos";
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = array();
        array_push($data,[
            'Nombre'=>'E & E REPUESTOS Y SERVICIOS GENERALES S.A.C.',
            'Ruc'=>'20601702275',
            'TipoPersona'=>'PERSONA NATURAL',
            'Direccion'=>'AV. CESAR VALLEJO NRO. 1114 URB. PALERMO, LA LIBERTAD-TRUJILLO-TRUJILLO',
            'Zona'=>'NORTE',
            'Correo'=>'ejemplo@gmail.com',
            'Telefono'=>'987654321',
            'Celular'=>'',
            'NombreContacto'=>'',
            'CorreoContacto'=>'SANTOTOMASAC264@HOTMAIL.COM',
            'TelefonoContacto'=>'',
            'CelularContacto'=>''
        ]);
        return $data;
    }
    public function headings(): array
    {
        return [
            'Nombre',
            'Ruc',
            'TipoPersona',
            'Direccion',
            'Zona',
            'Correo',
            'Telefono',
            'Celular',
            'NombreContacto',
            'CorreoContacto',
            'TelefonoContacto',
            'CelularContacto'
        ];
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:H1')->applyFromArray(
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
                $event->sheet->getStyle('I1:L1')->applyFromArray(
                    [
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startColor' => [
                                'argb' => '00B2FF',
                            ],
                            'endColor' => [
                                'argb' => '00B2FF',
                            ],
                        ],


                    ]

                );
                for ($j = 2; $j < 100; $j++) {
                    $validation = $event->sheet->getCell('C' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('detalles!$A$2:$A$'.(personas()->count()+1));

                    $validation = $event->sheet->getCell('E' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('detalles!$B$2:$B$'.(zonas()->count()+1));
                }
            },
        ];
    }
}
