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


class PedidosExport implements ShouldAutoSize,WithHeadings,FromArray,WithEvents
{
    public $fecha_inicio,$fecha_fin,$estado;
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $query  =   "select 
                        CONCAT('PE-',pe.pedido_nro)  as `N°PEDIDO`,
                        pe.estado as ESTADO_PED,
                        pe.cliente_nombre as CLIENTE,
                        pe.user_nombre as USUARIO,
                        pe.created_at as FECHA_PEDIDO,
                        pe.total as SUBTOTAL_PEDIDO,
                        pe.total_igv as IGV_PEDIDO,
                        pe.total_pagar as TOTAL_PEDIDO,
                        CONCAT(cd.serie,'-',cd.correlativo) as DOC_ATEND,
                        cd.total as SUBTOTAL,
                        cd.total_igv as IGV,
                        cd.total_pagar as TOTAL,
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
                        left join pedidos_atenciones as pa on pe.id=pa.pedido_id
                        left join cotizacion_documento as cd on cd.id=pa.documento_id
                        left join cotizacion_documento_detalles as cdd on cdd.documento_id=cd.id
                        left join productos as pr on pr.id=cdd.producto_id
                        left join marcas as ma on pr.marca_id=ma.id
                        left join categorias as ca on ca.id=pr.categoria_id
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

        $productos      =   DB::select($query,$bindings);

        
        return $productos;
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
            ['N°PEDIDO',
            'ESTADO_PED',
            'CLIENTE',
            'USUARIO',
            'FECHA_PEDIDO',
            'SUBTOTAL_PEDIDO',
            'IGV_PEDIDO',
            'TOTAL_PEDIDO',
            'DOC_ATEND',
            'SUBTOTAL',
            'IGV',
            'TOTAL',
            'CATEGORIA',
            'MARCA',
            'MODELO',
            'PRODUCTO',
            'COLOR',
            'TALLA',
            'CANTIDAD',
            'PRECIO',
            'IMPORTE'
            ]
        ]
       ;
    }
    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class => function (AfterSheet $event) {
                // Aplicar color a las columnas de A a H
                $event->sheet->getStyle('A1:H1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startColor' => ['argb' => '1ab394'],
                        'endColor' => ['argb' => '1ab394'],
                    ],
                ]);
                
                // Aplicar color a las columnas de I a U
                $event->sheet->getStyle('I1:U1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startColor' => ['argb' => 'ADD8E6'], // Celeste pálido
                        'endColor' => ['argb' => 'ADD8E6'], // Celeste pálido
                    ],
                ]);
                
            },
        ];
    }
}
