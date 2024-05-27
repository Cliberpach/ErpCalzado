<?php

namespace App\Exports\Pedido;

use App\Almacenes\Producto;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PedidosExport implements ShouldAutoSize,WithHeadings,FromArray,WithEvents
{
    public $fecha_inicio,$fecha_fin,$estado;
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $query  =   "select DISTINCT
                        CONCAT('PE-',pe.pedido_nro)  as `N°PEDIDO`,
                        pe.estado as ESTADO_PED,
                        pe.cliente_nombre as CLIENTE,
                        u.usuario as USUARIO,
                        cd.created_at as FECHA_ATENCION,
                        CONCAT(cd.serie,'-',cd.correlativo) as DOC_ATEND,
                        cd.total as SUBTOTAL_ATEND,
                        cd.total_igv as IGV_ATEND,
                        cd.total_pagar as TOTAL_ATEND,
                        ca.descripcion as CATEGORIA,
                        ma.marca as MARCA,
                        cdd.nombre_modelo as MODELO,
                        cdd.nombre_producto as PRODUCTO,
                        cdd.nombre_color as COLOR,
                        cdd.nombre_talla as TALLA,
                        cdd.cantidad as CANTIDAD,
                        cdd.precio_unitario_nuevo as PRECIO,
                        cdd.importe_nuevo as IMPORTE
                        from pedidos as pe 
                        inner join pedidos_detalles as pd on pd.pedido_id=pe.id  
                        inner join pedidos_atenciones as pa on pe.id=pa.pedido_id
                        inner join cotizacion_documento as cd on cd.id=pa.documento_id
                        inner join cotizacion_documento_detalles as cdd on cdd.documento_id=cd.id
                        inner join productos as pr on pr.id=cdd.producto_id
                        inner join marcas as ma on pr.marca_id=ma.id
                        inner join categorias as ca on ca.id=pr.categoria_id
                        inner join users as u on u.id=cd.user_id
                        where 1=1";

        $bindings   =   [];

        if ($this->fecha_inicio && $this->fecha_fin) {
            $query      .= " and pe.fecha_registro between ? AND ?";
            $bindings[] = $this->fecha_inicio;
            $bindings[] = $this->fecha_fin;
        } elseif ($this->fecha_inicio) {
            $query      .= " and pe.fecha_registro >= ?";
            $bindings[] = $this->fecha_inicio;
        } elseif ($this->fecha_fin) {
            $query      .= " and pe.fecha_registro <= ?";
            $bindings[] = $this->fecha_fin;
        }

        if($this->estado){
            $query          .= " and pe.estado = ?";
            $bindings[]     = $this->estado;
        }
         
        $query  .=  ' order by pe.pedido_nro desc,cd.serie asc,cd.correlativo asc';

        $pedidos_docs      =   DB::select($query,$bindings);

        
        $query_2    =   "select 
                        CONCAT('PE-',pe.id) as `PEDIDO`,
                        pe.estado as ESTADO_DET,
                        pe.created_at as FECHA_PED,
                        pe.user_nombre as USUARIO_PED,
                        pe.total as SUBTOTAL_PED,
                        pe.total_igv as IGV_PED,
                        pe.total_pagar as TOTAL_PED,
                        pd.producto_nombre as PRODUCTO_DET, 
                        pd.color_nombre as COLOR_DET,
                        pd.talla_nombre as TALLA_DET,
                        pd.cantidad as CANT_SOLICITADA,
                        pd.cantidad_atendida as CANT_ATENDIDA,
                        pd.cantidad_pendiente as CANT_PENDIENTE
                        from pedidos as pe
                        inner join pedidos_detalles as pd on pd.pedido_id=pe.id
                        where 1=1";


        $bindings   =   [];

        if ($this->fecha_inicio && $this->fecha_fin) {
            $query_2      .= " and pe.fecha_registro between ? AND ?";
            $bindings[] = $this->fecha_inicio;
            $bindings[] = $this->fecha_fin;
        } elseif ($this->fecha_inicio) {
            $query_2      .= " and pe.fecha_registro >= ?";
            $bindings[] = $this->fecha_inicio;
        } elseif ($this->fecha_fin) {
            $query_2      .= " and pe.fecha_registro <= ?";
            $bindings[] = $this->fecha_fin;
        }
                
        if($this->estado){
            $query_2          .= " and pe.estado = ?";
            $bindings[]     = $this->estado;
        }
                         
        $query_2  .=  ' order by pe.pedido_nro desc';

        $pedidos_detalles   =   DB::select($query_2,$bindings);

        $data   =   [];

        $cant_1 =   count($pedidos_docs);
        $cant_2 =   count($pedidos_detalles);

        $order = [
            "PEDIDO","ESTADO_DET","FECHA_PED","USUARIO_PED", "SUBTOTAL_PED","IGV_PED","TOTAL_PED",
            "PRODUCTO_DET", "COLOR_DET", "TALLA_DET", "CANT_SOLICITADA", "CANT_ATENDIDA", "CANT_PENDIENTE",
            "", "N°PEDIDO", "ESTADO_PED", "CLIENTE", "USUARIO","FECHA_ATENCION", 
            "DOC_ATEND", "SUBTOTAL_ATEND", "IGV_ATEND", "TOTAL_ATEND", "CATEGORIA", "MARCA", "MODELO", "PRODUCTO",
            "COLOR", "TALLA", "CANTIDAD", "PRECIO", "IMPORTE"
        ];

        if($cant_1  >=  $cant_2){

            $index  =   0;
            foreach ($pedidos_detalles as  $ped) {
                $pedidos_docs[$index]->PEDIDO           = $ped->PEDIDO;
                $pedidos_docs[$index]->ESTADO_DET       = $ped->ESTADO_DET;
                $pedidos_docs[$index]->FECHA_PED        = $ped->FECHA_PED;
                $pedidos_docs[$index]->USUARIO_PED      = $ped->USUARIO_PED;
                $pedidos_docs[$index]->SUBTOTAL_PED     = $ped->SUBTOTAL_PED;
                $pedidos_docs[$index]->IGV_PED          = $ped->IGV_PED;
                $pedidos_docs[$index]->TOTAL_PED        = $ped->TOTAL_PED;
                $pedidos_docs[$index]->PRODUCTO_DET     = $ped->PRODUCTO_DET;
                $pedidos_docs[$index]->COLOR_DET        = $ped->COLOR_DET;
                $pedidos_docs[$index]->TALLA_DET        = $ped->TALLA_DET;
                $pedidos_docs[$index]->CANT_SOLICITADA  = $ped->CANT_SOLICITADA;
                $pedidos_docs[$index]->CANT_ATENDIDA    = $ped->CANT_ATENDIDA;
                $pedidos_docs[$index]->CANT_PENDIENTE   = $ped->CANT_PENDIENTE;
                $pedidos_docs[$index]->{""}             =   '';

               $index++;
            }

            foreach ($pedidos_docs as &$pedido) {
                $nuevo_pedido = new \stdClass();
                foreach ($order as $prop) {
                    if (property_exists($pedido, $prop)) {
                        $nuevo_pedido->$prop = $pedido->$prop;
                    } else {
                        $nuevo_pedido->$prop = '';
                    }
                }
                $pedido = $nuevo_pedido;
            }
           
            return $pedidos_docs;
        }else{
            $index  =   0;
            foreach ($pedidos_docs as $ped) {
                $pedidos_detalles[$index]->{""}             =   '';
                $pedidos_detalles[$index]->{'N°PEDIDO'}     = $ped->{'N°PEDIDO'};
                $pedidos_detalles[$index]->ESTADO_PED       = $ped->ESTADO_PED;
                $pedidos_detalles[$index]->CLIENTE          = $ped->CLIENTE;
                $pedidos_detalles[$index]->USUARIO          = $ped->USUARIO;
                $pedidos_detalles[$index]->FECHA_ATENCION   = $ped->FECHA_ATENCION;
                $pedidos_detalles[$index]->DOC_ATEND        = $ped->DOC_ATEND;
                $pedidos_detalles[$index]->SUBTOTAL_ATEND   = $ped->SUBTOTAL_ATEND;
                $pedidos_detalles[$index]->IGV_ATEND        = $ped->IGV_ATEND;
                $pedidos_detalles[$index]->TOTAL_ATEND      = $ped->TOTAL_ATEND;
                $pedidos_detalles[$index]->CATEGORIA        = $ped->CATEGORIA;
                $pedidos_detalles[$index]->MARCA            = $ped->MARCA;
                $pedidos_detalles[$index]->MODELO           = $ped->MODELO;
                $pedidos_detalles[$index]->PRODUCTO         = $ped->PRODUCTO;
                $pedidos_detalles[$index]->COLOR            = $ped->COLOR;
                $pedidos_detalles[$index]->TALLA            = $ped->TALLA;
                $pedidos_detalles[$index]->CANTIDAD         = $ped->CANTIDAD;
                $pedidos_detalles[$index]->PRECIO           = $ped->PRECIO;
                $pedidos_detalles[$index]->IMPORTE          = $ped->IMPORTE;
                $index++;
            }

            foreach ($pedidos_detalles as &$pedido) {
                $nuevo_pedido = new \stdClass();
                foreach ($order as $prop) {
                    if (property_exists($pedido, $prop)) {
                        $nuevo_pedido->$prop = $pedido->$prop;
                    } else {
                        $nuevo_pedido->$prop = '';
                    }
                }
                $pedido = $nuevo_pedido;
            }
            return $pedidos_detalles;
        }      
          
    }

    public function __construct($fecha_inicio,$fecha_fin,$estado)
    {
        $this->fecha_inicio     = $fecha_inicio;
        $this->fecha_fin        = $fecha_fin;
        $this->estado           = $estado;
    }

    public function headings(): array
    {
        return [
            [
           
            ]
        ]
       ;
    }
    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class => function (AfterSheet $event) {
                // Combinar celdas y colocar texto en la celda combinada
                $event->sheet->getDelegate()->mergeCells('A1:M1');
                $event->sheet->getDelegate()->setCellValue('A1', "DETALLES DE PEDIDOS");

                $event->sheet->getDelegate()->mergeCells('O1:AF1');
                $event->sheet->getDelegate()->setCellValue('O1', "ATENCIONES POR PEDIDO");

                // Ajustar alineación del texto
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('O1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getStyle('O1')->getFont()->setBold(true);

                // Ajustar configuración de la página
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
                $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
                
                // Insertar fila para encabezados
                $event->sheet->insertNewRowBefore(2);
                
                //ENCABEZADOS TABLA 1 DETALLES PEDIDOS ========
                $event->sheet->getStyle('A2:M2')->getFont()->setBold(true);
                $event->sheet->setCellValue('A2', 'N°PED');
                $event->sheet->setCellValue('B2', 'ESTADO_PED');
                $event->sheet->setCellValue('C2', 'FECHA_PED');
                $event->sheet->setCellValue('D2', 'USER_PED');
                $event->sheet->setCellValue('E2', 'SUBTOTAL_PED');
                $event->sheet->setCellValue('F2', 'IGV_PED');
                $event->sheet->setCellValue('G2', 'TOTAL_PED');
                $event->sheet->setCellValue('H2', 'PRODUCTO_PED');
                $event->sheet->setCellValue('I2', 'COLOR_PED');
                $event->sheet->setCellValue('J2', 'TALLA_PED');
                $event->sheet->setCellValue('K2', 'CANT_SOLICITADA');
                $event->sheet->setCellValue('L2', 'CANT_ATENDIDA');
                $event->sheet->setCellValue('M2', 'CANT_PENDIENTE');

                $event->sheet->getStyle('O2:AF2')->getFont()->setBold(true);
                $event->sheet->setCellValue('O2', 'N°PEDIDO');
                $event->sheet->setCellValue('P2', 'ESTADO_PED');
                $event->sheet->setCellValue('Q2', 'CLIENTE');
                $event->sheet->setCellValue('R2', 'USUARIO');
                $event->sheet->setCellValue('S2', 'FECHA_ATENCION');
                $event->sheet->setCellValue('T2', 'DOC_ATEND');
                $event->sheet->setCellValue('U2', 'SUBTOTAL_ATEND');
                $event->sheet->setCellValue('V2', 'IGV_ATEND');
                $event->sheet->setCellValue('W2', 'TOTAL_ATEND');
                $event->sheet->setCellValue('X2', 'CATEGORIA');
                $event->sheet->setCellValue('Y2', 'MARCA');
                $event->sheet->setCellValue('Z2', 'MODELO');
                $event->sheet->setCellValue('AA2', 'PRODUCTO');
                $event->sheet->setCellValue('AB2', 'COLOR');
                $event->sheet->setCellValue('AC2', 'TALLA');
                $event->sheet->setCellValue('AD2', 'CANTIDAD');
                $event->sheet->setCellValue('AE2', 'PRECIO');
                $event->sheet->setCellValue('AF2', 'IMPORTE');

                //======= PINTANDO ======
                $event->sheet->getStyle('A1:M1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']]
                ]);
                $event->sheet->getStyle('A2:M2')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']]
                ]);
                $event->sheet->getStyle('A:A')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']]
                ]);


                $event->sheet->getStyle('O1:AF1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D6EAF8']
                    ]
                ]);
                
                $event->sheet->getStyle('O2:AF2')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D6EAF8']
                    ]
                ]);
                $event->sheet->getStyle('O:O')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D6EAF8']
                    ]
                ]);
                
                
            },
           
        ];
    }
}
