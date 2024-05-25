<?php

namespace App\Exports;

use App\Ventas\Documento\Documento;
use App\Ventas\Guia;
use App\Ventas\Nota;
use App\Ventas\Resumen;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ContabilidadExport implements FromCollection,WithHeadings,WithEvents
{

    use Exportable;
    public $tipo,$user,$fecha_desde,$fecha_hasta;

    public function headings(): array
    {
        return [
            ["RUC-EMISOR",
            "DOC.",
            "CODIGO.DOC",
            "FECHA",
            "TICKET",
            "TIENDA",
            "RUC/DNI",
            "CLIENTE",
            "SUNAT",
            "MONEDA",
            "MONTO",
            "OP.GRAVADA",
            "IVG",
            "EFECTIVO",
            "TRANSFERENCIA",
            "YAPE/PLIN",
            "ENVIADA",
            "ESTADO",
            "PRODUCTO",
            "COLOR",
            "TALLA",
            "CANTIDAD",
            "PRECIO",
            "IMPORTE"
            ]
        ];
    }

    function title(): String
    {
        return "Documentos";
    }

    public function __construct($tipo,$fecha_desde,$fecha_hasta,$user)
    {
        $this->tipo = $tipo;
        $this->fecha_desde = $fecha_desde;
        $this->fecha_hasta = $fecha_hasta;
        $this->user = $user;
    }

    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {   

        $tipo_documento     =   DB::select('select * from tabladetalles as td
                                where td.id=?',[$this->tipo]);
        
        
        if($this->tipo == 129 || $this->tipo == 128 || $this->tipo == 127)
        {
            $docs_venta     =   $this->getDocsVenta($this->tipo,$this->fecha_desde,$this->fecha_hasta,$this->user);
            
           return collect($docs_venta);
        }

        if($this->tipo == 126) //Ventas
        {
           $ventas      =   $this->getFacBolNotasVenta($this->fecha_desde,$this->fecha_hasta,$this->user);
           return collect($ventas);
        }

        if($this->tipo == 125) //Fact, Boletas y Nota Crédito
        {
            $docs   =   $this->getFacBolNotasCre($this->fecha_desde,$this->fecha_hasta,$this->user);
        
            return collect($docs);        
        }

        if($this->tipo == 130)
        {
            $notas_electronicas = Nota::where('estado','!=','ANULADO')->where('tipo_nota',"0")->where('tipDocAfectado','!=','04');
            if($this->fecha_desde && $this->fecha_hasta)
            {
                $notas_electronicas = $notas_electronicas->whereBetween('fechaEmision', [$this->fecha_desde, $this->fecha_hasta]);
            }

            $notas_electronicas = $notas_electronicas->orderBy('fechaEmision', 'asc')
            ->orderBy('serie', 'asc')
            ->orderBy('correlativo', 'asc')
            ->get();

            $coleccion = collect();
            foreach($notas_electronicas as $nota){
                $coleccion->push([
                    'RUC-EMISOR' => $nota->ruc_empresa,
                    'DOC.' => 'NOTA DE CRÉDITO',
                    'CODIGO.DOC' => $nota->tipoDoc,
                    'FECHA' => Carbon::parse($nota->fechaEmision)->format( 'Y-m-d'),
                    'TICKET' => $nota->serie.' - '.$nota->correlativo,
                    'TIENDA' => $nota->empresa,
                    'RUC/DNI' => $nota->documento_cliente,
                    'TIPO.CLIENTE' => $nota->cod_tipo_documento_cliente,
                    'CLIENTE' => $nota->cliente,
                    'SUNAT' => $nota->sunat == '2' ? "NULO" : "VALIDO",
                    'MONEDA' => $nota->tipoMoneda,
                    'MONTO' => $nota->mtoImpVenta,
                    'OP.GRAVADA' => $nota->mtoOperGravadas,
                    'IVG' => $nota->mtoIGV,
                    'EFECTIVO' => '-',
                    'TRANSFERENCIA' => '-',
                    'YAPE/PLIN' => '-',
                    'ENVIADA' => $nota->sunat == '1' || $nota->sunat == '2' ? 'SI' : 'NO',
                    'ESTADO'    =>  $nota->estado,
                    // 'HASH' => $nota->hash
                ]);
            }

            return $coleccion->sortBy('FECHA');
        }

        if(count($tipo_documento) > 0){
            if($tipo_documento[0]->simbolo = "09"){
                $guias  =    $this->getGuiasRemisionElectronicas($this->fecha_desde,$this->fecha_hasta,$this->user);
                return collect($guias);
            }
        }
    }

    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:X1')->applyFromArray([

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
                $event->sheet->getStyle('A1:S1')->applyFromArray([
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
               $event->sheet->getColumnDimension('B')->setWidth(20);
               $event->sheet->getColumnDimension('C')->setWidth(20);
               $event->sheet->getColumnDimension('D')->setWidth(20);
               $event->sheet->getColumnDimension('E')->setWidth(20);
               $event->sheet->getColumnDimension('F')->setWidth(20);
               $event->sheet->getColumnDimension('G')->setWidth(20);
               $event->sheet->getColumnDimension('H')->setWidth(20);
               $event->sheet->getColumnDimension('I')->setWidth(20);
               $event->sheet->getColumnDimension('J')->setWidth(20);
               $event->sheet->getColumnDimension('K')->setWidth(20);
               $event->sheet->getColumnDimension('L')->setWidth(20);
               $event->sheet->getColumnDimension('M')->setWidth(20);
               $event->sheet->getColumnDimension('N')->setWidth(20);
               $event->sheet->getColumnDimension('O')->setWidth(20);
               $event->sheet->getColumnDimension('P')->setWidth(20);
               $event->sheet->getColumnDimension('Q')->setWidth(20);
               $event->sheet->getColumnDimension('R')->setWidth(20);
               $event->sheet->getColumnDimension('S')->setWidth(20);
               $event->sheet->getColumnDimension('T')->setWidth(20);
               $event->sheet->getColumnDimension('U')->setWidth(20);
               $event->sheet->getColumnDimension('V')->setWidth(20);
               $event->sheet->getColumnDimension('W')->setWidth(20);
               $event->sheet->getColumnDimension('X')->setWidth(20);

            },
        ];
    }


    public function getDocsVenta($tipo_venta,$fecha_desde,$fecha_hasta,$user){
        $codigo_doc =   null;
        //======= FACTURA =======
        if($tipo_venta == 127){
            $codigo_doc =   '01';
        }
        //====== BOLETA ======
        if($tipo_venta == 128){
            $codigo_doc =   '03';
        }
        //====== NOTA DE VENTA ========
        if($tipo_venta == 129){
            $codigo_doc =   '04';
        }

        $query = "select 
                cd.ruc_empresa as `RUC-EMISOR`,
                td.descripcion as `DOC.`,
                ? AS `CODIGO.DOC`,
                cd.created_at as FECHA,
                CONCAT(cd.serie, '-', cd.correlativo) AS TICKET,
                cd.empresa as TIENDA,
                cd.documento_cliente as `RUC/DNI`,
                cd.cliente as CLIENTE,
                IF(cd.sunat = 2, 'NULO', 'VÁLIDO') as SUNAT,
                'PEN' as MONEDA,
                cd.total_pagar as MONTO,
                cd.total as `OP.GRAVADA`,
                cd.total_igv as IGV,
                cd.efectivo as EFECTIVO,
                IF(cd.tipo_pago_id = 2,cd.importe,0) as TRANSFERENCIA,
                IF(cd.tipo_pago_id = 3,cd.importe,0) as `YAPE/PLIN`,
                IF(cd.sunat = 1,'SI','NO') as ENVIADA,
                cd.estado as ESTADO,
                p.nombre as PRODUCTO,
                co.descripcion as COLOR,
                t.descripcion as TALLA,
                cdd.cantidad as CANTIDAD,
                cdd.precio_unitario_nuevo as PRECIO,
                cdd.importe_nuevo as IMPORTE
                FROM 
                cotizacion_documento as cd inner join cotizacion_documento_detalles as cdd on cd.id = cdd.documento_id
                inner join  tabladetalles as td on td.id = cd.tipo_venta
                inner join  clientes as c on c.id = cd.cliente_id
                inner join  productos as p on p.id = cdd.producto_id
                inner join  colores as co on co.id=cdd.color_id
                inner join tallas as t on t.id=cdd.talla_id
                inner join modelos as m on m.id=p.modelo_id
                WHERE 
                    cd.estado != 'ANULADO' and cd.tipo_venta=?";

            $bindings = [$codigo_doc,$tipo_venta];

            if ($fecha_desde && $fecha_hasta) {
                $query .= " AND cd.fecha_documento BETWEEN ? AND ?";
                $bindings[] = $fecha_desde;
                $bindings[] = $fecha_hasta;
            }

            if ($user) {
                $query .= " AND cd.user_id = ?";
                $bindings[] = $user;
            }

            $query .= " ORDER BY cd.id ASC";

            $docs_venta = DB::select($query, $bindings);

            return $docs_venta;
    }


    public function getFacBolNotasVenta($fecha_desde,$fecha_hasta,$user){
        $query = "select 
                cd.ruc_empresa as `RUC-EMISOR`,
                td.descripcion as `DOC.`,
                td.simbolo AS `CODIGO.DOC`,
                cd.created_at as FECHA,
                CONCAT(cd.serie, '-', cd.correlativo) AS TICKET,
                cd.empresa as TIENDA,
                cd.documento_cliente as `RUC/DNI`,
                cd.cliente as CLIENTE,
                IF(cd.sunat = 2, 'NULO', 'VÁLIDO') as SUNAT,
                'PEN' as MONEDA,
                cd.total_pagar as MONTO,
                cd.total as `OP.GRAVADA`,
                cd.total_igv as IGV,
                cd.efectivo as EFECTIVO,
                IF(cd.tipo_pago_id = 2,cd.importe,0) as TRANSFERENCIA,
                IF(cd.tipo_pago_id = 3,cd.importe,0) as `YAPE/PLIN`,
                IF(cd.sunat = 1,'SI','NO') as ENVIADA,
                cd.estado as ESTADO,
                p.nombre as PRODUCTO,
                co.descripcion as COLOR,
                t.descripcion as TALLA,
                cdd.cantidad as CANTIDAD,
                cdd.precio_unitario_nuevo as PRECIO,
                cdd.importe_nuevo as IMPORTE
                FROM 
                cotizacion_documento as cd inner join cotizacion_documento_detalles as cdd on cd.id = cdd.documento_id
                inner join  tabladetalles as td on td.id = cd.tipo_venta
                inner join  clientes as c on c.id = cd.cliente_id
                inner join  productos as p on p.id = cdd.producto_id
                inner join  colores as co on co.id=cdd.color_id
                inner join tallas as t on t.id=cdd.talla_id
                inner join modelos as m on m.id=p.modelo_id
                WHERE 
                    cd.estado != 'ANULADO'";

        $bindings = [];
       
        if ($fecha_desde && $fecha_hasta) {
            $query .= " AND cd.created_at BETWEEN ? AND ?";
            $bindings[] = $fecha_desde;
            $bindings[] = $fecha_hasta;
        }

        if ($user) {
            $query .= " AND cd.user_id = ?";
            $bindings[] = $user;
        }

        $query .= " ORDER BY cd.id desc";

        $ventas = DB::select($query, $bindings);
        
        return $ventas;
    }


    public function getFacBolNotasCre($fecha_desde,$fecha_hasta,$user){
        $query = "select 
                cd.ruc_empresa as `RUC-EMISOR`,
                td.descripcion as `DOC.`,
                td.simbolo AS `CODIGO.DOC`,
                cd.created_at as FECHA,
                CONCAT(cd.serie, '-', cd.correlativo) AS TICKET,
                cd.empresa as TIENDA,
                cd.documento_cliente as `RUC/DNI`,
                cd.cliente as CLIENTE,
                IF(cd.sunat = 2, 'NULO', 'VÁLIDO') as SUNAT,
                'PEN' as MONEDA,
                cd.total_pagar as MONTO,
                cd.total as `OP.GRAVADA`,
                cd.total_igv as IGV,
                cd.efectivo as EFECTIVO,
                IF(cd.tipo_pago_id = 2,cd.importe,0) as TRANSFERENCIA,
                IF(cd.tipo_pago_id = 3,cd.importe,0) as `YAPE/PLIN`,
                IF(cd.sunat = 1,'SI','NO') as ENVIADA,
                cd.estado as ESTADO,
                p.nombre as PRODUCTO,
                co.descripcion as COLOR,
                t.descripcion as TALLA,
                cdd.cantidad as CANTIDAD,
                cdd.precio_unitario_nuevo as PRECIO,
                cdd.importe_nuevo as IMPORTE
                FROM 
                cotizacion_documento as cd inner join cotizacion_documento_detalles as cdd on cd.id = cdd.documento_id
                inner join  tabladetalles as td on td.id = cd.tipo_venta
                inner join  clientes as c on c.id = cd.cliente_id
                inner join  productos as p on p.id = cdd.producto_id
                inner join  colores as co on co.id=cdd.color_id
                inner join tallas as t on t.id=cdd.talla_id
                inner join modelos as m on m.id=p.modelo_id
                WHERE 
                cd.estado != 'ANULADO'
                AND cd.tipo_venta != 129
        ";

        $bindings = [];

        if ($fecha_desde && $fecha_hasta) {
            $query .= " and cd.fecha_documento between ? and ?";
            $bindings[] = $fecha_desde;
            $bindings[] = $fecha_hasta;
        }

        if ($user) {
            $query .= " AND cd.user_id = ?";
            $bindings[] = $user;
        }

        $query .= " ORDER BY cd.id ASC";

        $docs_venta_fact_bol = DB::select($query, $bindings);


        //========== HALLANDO LAS NOTAS ELECTRÓNICAS ==========
        $query = "select 
                n.ruc_empresa `RUC-EMISOR`,
                'NOTA DE CRÉDITO' as `DOC.`,
                '07' as `CODIGO.DOC`,
                n.created_at as FECHA,
                CONCAT(n.serie, '-', n.correlativo) AS TICKET,
                n.empresa as TIENDA,
                n.documento_cliente as `RUC/DNI`,
                n.cliente as CLIENTE,
                IF(n.sunat = 2, 'NULO', 'VÁLIDO') as SUNAT,
                'PEN' as MONEDA,
                n.mtoImpVenta as MONTO,
                n.mtoOperGravadas as `OP.GRAVADA`,
                n.mtoIGV as `IGV`,
                '-' as EFECTIVO,
                '-' as TRANSFERENCIA,
                '-' as `YAPE/PLIN`,
                IF(n.sunat = 1,'SI','NO') as ENVIADA,
                'ACTIVO' as ESTADO,
                p.nombre as PRODUCTO,
                co.descripcion as COLOR,
                t.descripcion as TALLA,
                ned.cantidad as CANTIDAD,
                ned.mtoPrecioUnitario as PRECIO,
                ned.cantidad * ned.mtoPrecioUnitario AS IMPORTE
            FROM 
                nota_electronica as n inner join nota_electronica_detalle as ned on n.id = ned.nota_id
                inner join  productos as p on p.id = ned.producto_id
                inner join  colores as co on co.id=ned.color_id
                inner join tallas as t on t.id=ned.talla_id
                inner join modelos as m on m.id=p.modelo_id
            WHERE 
                n.estado != 'ANULADO'
                AND n.tipo_nota = ?
                AND n.tipDocAfectado != ?";

        $bindings = ["0", "04"];

        if ($fecha_desde && $fecha_hasta) {
            $query .= " and n.fechaEmision between ? and ?";
            $bindings[] = $fecha_desde;
            $bindings[] = $fecha_hasta;
        }

        if ($user) {
            $query .= " AND n.user_id = ?";
            $bindings[] = $user;
        }

        $query .= " ORDER BY n.id ASC";

        $notas_electronicas = DB::select($query, $bindings);


        $resultado_unido = array_merge($docs_venta_fact_bol, $notas_electronicas);
      

        return $resultado_unido;
    }

    function getGuiasRemisionElectronicas($fecha_desde,$fecha_hasta,$user){
        $query  =   "select 
                    gr.ruc_empresa  as `RUC-EMISOR`,
                    'GUÍA DE REMISIÓN ELECTRÓNICA' as `DOC.`,
                    '09' as `CODIGO.DOC`,
                    gr.created_at as FECHA,
                    CONCAT(gr.serie,gr.correlativo) as TICKET,
                    gr.empresa as TIENDA,
                    gr.documento_cliente as `RUC/DNI`,
                    gr.cliente as CLIENTE,
                    IF(gr.sunat != 2,'VÁLIDO','NO VÁLIDO') as SUNAT,
                    'PEN' as MONEDA,
                    '-' as MONTO,
                    '-' as `OP.GRAVADA`,
                    '-' as `IGV`,
                    '-' as `EFECTIVO`,
                    '-' as `TRANSFERENCIA`,
                    '-' as `YAPE/PLIN`,
                    IF(gr.sunat=0,'NO','SI') as ENVIADA,
                    gr.estado as ESTADO,
                    gd.nombre_producto as PRODUCTO, 
                    gd.nombre_color as  COLOR,
                    gd.nombre_talla as TALLA,
                    gd.cantidad as CANTIDAD,
                    '-' as PRECIO,
                    '-' as IMPORTE 
                    from guias_remision as gr
                    inner join guia_detalles as gd  on gr.id=gd.guia_id
                    where gr.estado != 'ANULADO' ";

        $bindings   =   [];
        if ($fecha_desde && $fecha_hasta) {
            $query .= " and gr.created_at between ? and ?";
            $bindings[] = $fecha_desde;
            $bindings[] = $fecha_hasta;
        }
            
                    if ($user) {
                        $query .= " AND gr.user_id = ?";
                        $bindings[] = $user;
                    }
            
                    $query .= " ORDER BY gr.id ASC";
            
        $guias_remision = DB::select($query, $bindings);

        return $guias_remision;     
    }

}
