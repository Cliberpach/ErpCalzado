<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Almacenes\Talla;
use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use App\Exports\DocumentosExport;
use App\Exports\GuiaExport;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Persona\Persona;
use App\Ventas\Cliente;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\Guia;
use App\Ventas\Nota;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class ContabilidadController extends Controller
{
    public function index()
    {
        $auxs       = Persona::where('estado','ACTIVO')->get();
      
        $users = [];
        foreach($auxs as $user)
        {
            if($user->user_persona && $user->colaborador)
            {
                $user_aux = new stdClass();
                $user_aux->id = $user->user_persona->user->id;
                $user_aux->name = $user->getApellidosYNombres();

                array_push($users,$user_aux);
            }
        }
        return view('consultas.contabilidad.index',compact('users'));
    }

    public function getTable(Request $request){

        try{
            $tipo = $request->tipo;
            $user = $request->user;
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;

            $tipo_descripcion   =   DB::select('select * from tabladetalles as td 
                                    where td.tabla_id=21 and td.id=?',[$tipo]);
            
            if($tipo == 127 || $tipo == 128 || $tipo == 129)  //======== FACTURAS O  BOLETAS O NOTAS DE VENTA ========
            {
                $docs_venta     =   $this->getDocsVenta($tipo,$fecha_desde,$fecha_hasta,$user);

                return response()->json([
                    'success' => true,
                    'documentos' => $docs_venta,
                ]);
            }
            else if($tipo == 125)       //======== FACTURAS - BOLETAS Y NOTAS DE CRÉDITO ======
            {
                $resultado      =   $this->getFacBolNotasCre($fecha_desde,$fecha_hasta,$user,$tipo);

                return response()->json([
                    'success' => true,
                    'documentos' => $resultado,
                ]);

            }
            else if($tipo == 126)  //========= FACT - BOL Y NOTAS DE VENTA
            {
                $ventas     =   $this->getFacBolNotasVenta($fecha_desde,$fecha_hasta,$user);  
              
                return response()->json([
                    'success' => true,
                    'documentos' => $ventas,
                ]);
            }
            else if($tipo_descripcion[0]->descripcion === 'NOTA DE CRÉDITO FACTURA')  //====== FF01 =====
            {
                $resultado      =   $this->getNotasCredito($tipo,"0","01",$fecha_desde,$fecha_hasta,$user);

                return response()->json([
                    'success' => true,
                    'documentos' => $resultado,
                ]);
            }
            else if($tipo == 132) //====== GUÍAS REMISIÓN ELECTRÓNICAS ========
            {
               $guias_remision      =   $this->getGuiasRemision($tipo,$fecha_desde,$fecha_hasta,$user);

                return response()->json([
                    'success' => true,
                    'documentos' => $guias_remision,
                ]);
            }
            else{
                $coleccion = collect();
                return response()->json([
                    'success' => true,
                    'documentos' => $coleccion
                ]);
            }
        }
        catch(Exception $e)
        {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }


    public function getNotasCredito($tipo,$tipo_nota,$tipoDocAfectado,$fecha_desde,$fecha_hasta,$user){
        $consulta   =   "select n.id,
                                'NOTA DE CRÉDITO' as tipo_doc,
                                CONCAT(n.serie, '-', n.correlativo) AS numero,
                                n.mtoImpVenta as total,
                                n.sunat as sunat,
                                n.cliente as cliente,
                                n.created_at as fecha,
                                n.estado as estado,
                                '0' as convertir,
                                ? AS tipo,
                                p.nombre as producto_nombre,
                                co.descripcion as color_nombre,
                                t.descripcion as talla_nombre,
                                m.descripcion as modelo_nombre
                            FROM 
                                nota_electronica as n inner join nota_electronica_detalle as ned on n.id = ned.nota_id
                                inner join  productos as p on p.id = ned.producto_id
                                inner join  colores as co on co.id=ned.color_id
                                inner join tallas as t on t.id=ned.talla_id
                                inner join modelos as m on m.id=p.modelo_id
                            WHERE 
                                n.estado != 'ANULADO'
                                and n.tipo_nota = ?
                                and n.tipDocAfectado = ?";

                $bindings   =   [$tipo,$tipo_nota,$tipoDocAfectado];
                
                

                if($fecha_desde && $fecha_hasta)
                {
                    $consulta   .= " and n.fechaEmision between ? and ?";
                    $bindings[] =   $fecha_desde;
                    $bindings[] =   $fecha_hasta;
                }

                if ($user) {
                    $consulta   .= " and n.user_id = ?";
                    $bindings[] = $user;
                }

                $consulta       .=  " order by n.id asc";

                $resultado       =   DB::select($consulta,$bindings);

                return $resultado;
    }

    public function getDocsVenta($tipo_venta,$fecha_desde,$fecha_hasta,$user){
        $query = "select 
                cd.id,
                td.descripcion as tipo_doc,
                CONCAT(cd.serie, '-', cd.correlativo) AS numero,
                cd.total_pagar as total,
                cd.sunat as sunat,
                c.nombre as cliente,
                cd.created_at as fecha,
                cd.estado_pago as estado,
                cd.convertir as convertir,
                cd.tipo_venta as tipo,
                p.nombre as producto_nombre,
                co.descripcion as color_nombre,
                t.descripcion as talla_nombre,
                m.descripcion as modelo_nombre
                FROM 
                cotizacion_documento as cd inner join cotizacion_documento_detalles as cdd on cd.id = cdd.documento_id
                inner join  tabladetalles as td on td.id = cd.tipo_venta
                inner join  clientes as c on c.id = cd.cliente_id
                inner join  productos as p on p.id = cdd.producto_id
                inner join  colores as co on co.id=cdd.color_id
                inner join tallas as t on t.id=cdd.talla_id
                inner join modelos as m on m.id=p.modelo_id
                WHERE 
                    cd.estado != 'ANULADO' and cd.tipo_venta=?
            ";

            $bindings = [$tipo_venta];

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


    public function getGuiasRemision($tipo,$fecha_desde,$fecha_hasta,$user){
        $query       =   "select gr.id,
                            'GUÍA REMISIÓN ELECTRÓNICA' as tipo_doc,
                            CONCAT(gr.serie,'-',gr.correlativo) as numero,
                            '-' as total,
                            gr.sunat as sunat,
                            gr.cliente as cliente,
                            gr.created_at as fecha,
                            gr.estado_sunat as estado,
                            '0' as convertir,
                            ? as tipo,
                            p.nombre as producto_nombre,
                            co.descripcion as color_nombre,
                            t.descripcion as talla_nombre,
                            m.descripcion as modelo_nombre
                            from guias_remision as gr 
                            inner join guia_detalles as gd on gr.id=gd.guia_id
                            inner join productos as p on p.id=gd.producto_id
                            inner join colores as co on co.id=gd.color_id
                            inner join tallas as t on t.id=gd.talla_id
                            inner join modelos as m on m.id=p.modelo_id";


        $bindings = [$tipo];

        if ($fecha_desde && $fecha_hasta) {
            $query .= " AND gr.fecha_emision BETWEEN ? AND ?";
            $bindings[] = $fecha_desde;
            $bindings[] = $fecha_hasta;
        }

        if ($user) {
            $query .= " AND gr.user_id = ?";
            $bindings[] = $user;
        }

        $query .= " ORDER BY gr.id desc";

        $guias_remision = DB::select($query, $bindings);

        return $guias_remision;
    }


    public function getFacBolNotasCre($fecha_desde,$fecha_hasta,$user,$tipo){
        $query = "select 
                cd.id,
                td.descripcion AS tipo_doc,
                CONCAT(cd.serie, '-', cd.correlativo) AS numero,
                cd.total AS total,
                cd.sunat AS sunat,
                c.nombre AS cliente,
                cd.created_at AS fecha,
                cd.estado_pago AS estado,
                cd.convertir AS convertir,
                cd.tipo_venta AS tipo,
                p.nombre as producto_nombre,
                co.descripcion as color_nombre,
                t.descripcion as talla_nombre,
                m.descripcion as modelo_nombre
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
                n.id,
                'NOTA DE CRÉDITO' as tipo_doc,
                CONCAT(n.serie, '-', n.correlativo) AS numero,
                n.mtoImpVenta as total,
                n.sunat as sunat,
                n.cliente as cliente,
                n.created_at as fecha,
                n.estado as estado,
                '0' as convertir,
                ? AS tipo,
                p.nombre as producto_nombre,
                co.descripcion as color_nombre,
                t.descripcion as talla_nombre,
                m.descripcion as modelo_nombre
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

        $bindings = [$tipo,"0", "04"];

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


    public function getFacBolNotasVenta($fecha_desde,$fecha_hasta,$user){
        $query = "select 
                cd.id,
                td.descripcion as tipo_doc,
                CONCAT(cd.serie, '-', cd.correlativo) AS numero,
                cd.total_pagar as total,
                cd.sunat as sunat,
                c.nombre as cliente,
                cd.created_at as fecha,
                cd.estado_pago as estado,
                cd.convertir as convertir,
                cd.tipo_venta as tipo,
                p.nombre as producto_nombre,
                co.descripcion as color_nombre,
                t.descripcion as talla_nombre,
                m.descripcion as modelo_nombre
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
        ";

        $bindings = [];

        if ($fecha_desde && $fecha_hasta) {
            $query .= " AND cd.fecha_documento BETWEEN ? AND ?";
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
}
