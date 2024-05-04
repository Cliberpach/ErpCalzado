<?php

namespace App\Ventas\Documento;

use App\Events\NotifySunatEvent;
use App\Mantenimiento\Condicion;
use Illuminate\Database\Eloquent\Model;

use App\Mantenimiento\Tabla\Detalle as TablaDetalle;
use App\Ventas\CuentaCliente;
use App\Ventas\RetencionDetalle;
use Illuminate\Support\Facades\DB;

class Documento extends Model
{
    protected $table = 'cotizacion_documento';
    protected $guarded = [];
    // protected $fillable = [
    //     //DATOS DE LA EMPRESA
    //     'ruc_empresa',
    //     'empresa',
    //     'direccion_fiscal_empresa',
    //     'empresa_id',
    //     //CLIENTE
    //     'tipo_documento_cliente',
    //     'documento_cliente',
    //     'direccion_cliente',
    //     'cliente',
    //     'cliente_id',
    //     'condicion_id',
    //     'moneda',
    //     'numero_doc',
    //     'fecha_documento',
    //     'fecha_atencion',
    //     'fecha_vencimiento',
    //     'sub_total',
    //     'monto_embalaje',
    //     'monto_envio',
    //     'total',
    //     'total_igv',
    //     'total_pagar',
    //     'user_id',
    //     'igv',
    //     'igv_check',
    //     'tipo_venta',
    //     'cotizacion_venta',
    //     'sunat',
    //     'envio_sunat',

    //     'importe',
    //     'efectivo',
    //     'tipo_pago_id',

    //     'getCdrResponse',
    //     'regularize',
    //     'getRegularizeResponse',
        
    //     'correlativo',
    //     'banco_empresa_id',
    //     'serie',
    //     'ruta_comprobante_archivo',
    //     'nombre_comprobante_archivo',

    //     'estado',
    //     'estado_pago',
    //     'ruta_pago',

    //     'convertir',

    //     'contingencia',
    //     'correlativo_contingencia',
    //     'serie_contingencia',
    //     'getCdrResponse_contingencia',
    //     'getRegularizeResponse_contingencia'
    // ];

    public function retencion()
    {
        return $this->hasOne(RetencionDetalle::class, 'documento_id', 'id');
    }


    public function detalles()
    {
        return $this->hasMany('App\Ventas\Documento\Detalle', 'documento_id');
    }

    public function notas()
    {
        return $this->hasMany('App\Ventas\Nota', 'documento_id');
    }

    public function condicion()
    {
        return $this->belongsTo('App\Mantenimiento\Condicion', 'condicion_id');
    }
    public function tablaDetalles()
    {
        return $this->belongsTo('App\Mantenimiento\Tabla\Detalle', 'tipo_venta');
    }

    public function bancoPagado()
    {
        return $this->belongsTo('App\Mantenimiento\Empresa\Banco', 'banco_empresa_id');
    }

    public function empresaEntidad()
    {
        return $this->belongsTo('App\Mantenimiento\Empresa\Empresa', 'empresa_id');
    }

    public function clienteEntidad()
    {
        return $this->belongsTo('App\Ventas\Cliente', 'cliente_id');
    }


    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tipo_pago()
    {
        return $this->belongsTo('App\Ventas\TipoPago', 'tipo_pago_id');
    }

    public function nombreTipo(): string
    {
        $venta = tipos_venta()->where('id', $this->tipo_venta)->first();
        if (is_null($venta))
            return "-";
        else
            return strval($venta->nombre);
    }

    public function doc_convertido():string
    {
        $documento_convertido_id        =   $this->convertir;
        $doc_convertido                 =   DB::select('select cd.serie,cd.correlativo 
                                            from cotizacion_documento as cd
                                            where id=?',[$documento_convertido_id]);  
        
        if(count($doc_convertido)>0){
            return $doc_convertido[0]->serie.'-'.$doc_convertido[0]->correlativo;
        }else{
            return "-";
        }
    }

    public function usuario():string 
    {
        $usuario_id         =   $this->user_id;
        $usuario            =   DB::select('select u.usuario as usuario_nombre 
                                from users as u   
                                where u.id=?',[$usuario_id]);

        if(count($usuario)>0){
            return $usuario[0]->usuario_nombre;
        }else{
            return "-";
        }
    }

    public function descripcionTipo(): string
    {
        $venta = tipos_venta()->where('id', $this->tipo_venta)->first();
        if (is_null($venta))
            return "-";
        else
            return strval($venta->descripcion);
    }

    public function tipoOperacion(): string
    {
        $venta = tipos_venta()->where('id', $this->tipo_venta)->first();
        if (is_null($venta))
            return "-";
        else
            return strval($venta->operacion);
    }

    public function tipoDocumento(): string
    {
        $venta = tipos_venta()->where('id', $this->tipo_venta)->first();
        if (is_null($venta))
            return "-";
        else
            return strval($venta->simbolo);
    }

    public function nombreDocumento(): string
    {
        $venta = tipos_venta()->where('id', $this->tipo_venta)->first();
        if (is_null($venta))
            return "-";
        else
            return strval($venta->nombre);
    }

    public function formaPago(): string
    {
        $condicion = Condicion::where('id', $this->condicion_id)->first();
        if (is_null($condicion))
            return "-";
        else
            return strval($condicion->descripcion . ' ' . ($condicion->dias > 0 ? $condicion->dias . ' dias' : ''));
    }

    public function forma_pago(): string
    {
        $condicion = Condicion::where('id', $this->condicion_id)->first();
        if (is_null($condicion))
            return "-";
        else
            return strval($condicion->slug);
    }

    public function simboloMoneda(): string
    {
        $moneda = tipos_moneda()->where('id', $this->moneda)->first();
        if (is_null($moneda))
            return "-";
        else
            return $moneda->parametro;
    }


    public function tipoDocumentoCliente(): string
    {
        $documento = tipos_documento()->where('simbolo', $this->tipo_documento_cliente)->first();
        if (is_null($documento))
            return "-";
        else
            return $documento->parametro;
    }

    public function cuenta()
    {
        return $this->hasOne('App\Ventas\CuentaCliente', 'cotizacion_documento_id');
    }

    protected static function booted()
    {
        static::created(function (Documento $documento) {
            //CREAR CUENTA CLIENTE
            $condicion = Condicion::find($documento->condicion_id);
            if (strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO') {
                if ($documento->convertir == null || $documento->convertir == '') {
                    $cuenta_cliente = new CuentaCliente();
                    $cuenta_cliente->cotizacion_documento_id = $documento->id;
                    $cuenta_cliente->numero_doc = $documento->serie . ' - ' . $documento->correlativo;
                    $cuenta_cliente->fecha_doc  = $documento->fecha_documento;
                    $cuenta_cliente->monto      = $documento->total_pagar;
                    $cuenta_cliente->acta       = 'DOCUMENTO VENTA';
                    $cuenta_cliente->saldo      = $documento->total_pagar;
                    $cuenta_cliente->save();
                }
            }
        });

        static::updated(function (Documento $documento) {
            if ($documento->cuenta) {
                $cuenta_cliente = CuentaCliente::find($documento->cuenta->id);
                $cuenta_cliente->cotizacion_documento_id        = $documento->id;
                $cuenta_cliente->numero_doc                     = $documento->serie . ' - ' . $documento->correlativo;
                $cuenta_cliente->fecha_doc                      = $documento->fecha_documento;
                $cuenta_cliente->monto                          = $documento->total_pagar - $documento->notas->sum("mtoImpVenta");
                $cuenta_cliente->acta                           = 'DOCUMENTO VENTA';
                $cuenta_cliente->saldo                          = $documento->total_pagar - $documento->notas->sum("mtoImpVenta");
                $cuenta_cliente->update();

                if ($cuenta_cliente->saldo - $cuenta_cliente->detalles->sum('monto') > 0) {
                    $cuenta_cliente->saldo =  $cuenta_cliente->saldo - $cuenta_cliente->detalles->sum('monto');
                } else {
                    $cuenta_cliente->saldo = 0;
                    $cuenta_cliente->estado = 'PAGADO';
                }
                $cuenta_cliente->update();

                if ($documento->estado == 'ANULADO') {
                    $cuenta_cliente = CuentaCliente::find($documento->cuenta->id);
                    $cuenta_cliente->estado = 'ANULADO';
                    $cuenta_cliente->update();
                }
            } else {
                $condicion = Condicion::find($documento->condicion_id);
                if (strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO') {
                    if ($documento->convertir == null || $documento->convertir == '') {
                        $cuenta_cliente = new CuentaCliente();
                        $cuenta_cliente->cotizacion_documento_id = $documento->id;
                        $cuenta_cliente->numero_doc = $documento->serie . ' - ' . $documento->correlativo;
                        $cuenta_cliente->fecha_doc = $documento->fecha_documento;
                        $cuenta_cliente->monto = $documento->total - $documento->notas->sum("mtoImpVenta");
                        $cuenta_cliente->acta = 'DOCUMENTO VENTA';
                        $cuenta_cliente->saldo = $documento->total;
                        $cuenta_cliente->save();
                    }
                }
            }

            if ($documento->convertir && $documento->tipo_venta == 129) {
                $doc_convertido = Documento::find($documento->convertir);

                $condicion = Condicion::find($documento->condicion_id);
                if (strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO') {
                    if ($documento->cuenta) {
                        $cuenta_a_convertir = CuentaCliente::find($documento->cuenta->id);
                        //$cuenta_a_convertir->cotizacion_documento_id = $doc_convertido->id;
                        //$cuenta_a_convertir->numero_doc = $doc_convertido->serie . ' - ' . $doc_convertido->correlativo;
                        //$cuenta_a_convertir->fecha_doc = $doc_convertido->fecha_documento;
                        $cuenta_a_convertir->monto = $doc_convertido->total - $doc_convertido->notas->sum("mtoImpVenta");
                        $cuenta_a_convertir->acta = 'DOCUMENTO VENTA';
                        $cuenta_a_convertir->update();
                    } else {
                        $cuenta_cliente = new CuentaCliente();
                        $cuenta_cliente->cotizacion_documento_id = $documento->id;
                        $cuenta_cliente->numero_doc = $documento->serie . ' - ' . $documento->correlativo;
                        $cuenta_cliente->fecha_doc = $documento->fecha_documento;
                        $cuenta_cliente->monto = $documento->total - $documento->notas->sum("mtoImpVenta");
                        $cuenta_cliente->acta = 'DOCUMENTO VENTA';
                        $cuenta_cliente->saldo = $documento->total;
                        $cuenta_cliente->save();
                    }
                } else {
                    if ($documento->cuenta) {
                        $cuenta_a_convertir = CuentaCliente::find($documento->cuenta->id);
                        $cuenta_a_convertir->estado = 'ANULADO';
                        $cuenta_a_convertir->update();
                    }
                }
            }

            if ($documento->sunat == '2' || $documento->estado == 'ANULADO') {
                if ($documento->cuenta) {
                    $cuenta_cliente = CuentaCliente::find($documento->cuenta->id);
                    $cuenta_cliente->estado = 'ANULADO';
                    $cuenta_cliente->update();
                }
            }
        });

        static::deleted(function (Documento $documento) {
            //ANULAR CUENTA
            if ($documento->cuenta) {
                $cuenta_cliente = CuentaCliente::find($documento->cuenta->id);
                $cuenta_cliente->estado = 'ANULADO';
                $cuenta_cliente->update();
            }
        });
    }
}