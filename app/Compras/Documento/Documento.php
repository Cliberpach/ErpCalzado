<?php

namespace App\Compras\Documento;

use Illuminate\Database\Eloquent\Model;
use App\Compras\CuentaProveedor;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;

class Documento extends Model
{
    protected $table = 'compra_documentos';
    public $timestamps = true;
    protected $fillable = [
            'id',
            'fecha_emision',
            'fecha_entrega',
            'empresa_id',
            'proveedor_id',
            'modo_compra',
            'condicion_id',
            'numero_doc',
            'moneda',
            'observacion',
            'igv',
            'igv_check',
            'tipo_cambio',

            'tipo_compra',
            'orden_compra',
            'tipo_pago',

            'sub_total',
            'total_igv',
            'percepcion',
            'total',
            'total_pagar',

            'sub_total_soles',
            'total_igv_soles',
            'percepcion_soles',
            'total_soles',

            'sub_total_dolares',
            'total_igv_dolares',
            'percepcion_dolares',
            'total_dolares',

            'serie_tipo',
            'numero_tipo',


            'estado',
            'enviado',
            'usuario_id',

        ];

    public function empresa()
    {
        return $this->belongsTo('App\Mantenimiento\Empresa\Empresa');
    }

    public function notas()
    {
        return $this->hasMany('App\Compras\Nota', 'documento_id');
    }

    public function proveedor()
    {
        return $this->belongsTo('App\Compras\Proveedor');
    }

    public function usuario()
    {
        return $this->belongsTo('App\User','usuario_id');
    }

    public function cuenta()
    {
        return $this->hasOne('App\Compras\CuentaProveedor','compra_documento_id');
    }

    public function lotes()
    {
        return $this->hasMany('App\Almacenes\LoteProducto','compra_documento_id');
    }

    public function detalles()
    {
        return $this->hasMany('App\Compras\Documento\Detalle','documento_id');
    }

    public function condicion()
    {
        return $this->belongsTo('App\Mantenimiento\Condicion', 'condicion_id');
    }

    protected static function booted()
    {
        static::created(function(Documento $documento){
            $condicion = Condicion::find($documento->condicion_id);
            if(strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO')
            {
                $cuenta_proveedor = new CuentaProveedor();
                $cuenta_proveedor->compra_documento_id = $documento->id;
                $cuenta_proveedor->fecha_doc = $documento->fecha_emision;
                $cuenta_proveedor->saldo = $documento->total;
                $cuenta_proveedor->monto = $documento->total;
                $cuenta_proveedor->acta = 'DOCUMENTO COMPRA';
                $cuenta_proveedor->save();
            }
        });

        static::updated(function(Documento $documento){
            if($documento->cuenta)
            {
                $cuenta_proveedor = CuentaProveedor::find($documento->cuenta->id);
                $condicion = Condicion::find($documento->condicion_id);
                if(strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO')
                {
                    $cuenta_proveedor->compra_documento_id = $documento->id;
                    $cuenta_proveedor->fecha_doc = $documento->fecha_emision;
                    $cuenta_proveedor->monto = $documento->total - $documento->notas->sum("mtoImpVenta");
                    $cuenta_proveedor->acta = 'DOCUMENTO COMPRA';
                    $cuenta_proveedor->update();
                }
                else
                {
                    $cuenta_proveedor->estado = 'ANULADO';
                    $cuenta_proveedor->update();
                }
            }
            else
            {
                $condicion = Condicion::find($documento->condicion_id);
                if(strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO')
                {
                    $cuenta_proveedor = new CuentaProveedor();
                    $cuenta_proveedor->compra_documento_id = $documento->id;
                    $cuenta_proveedor->fecha_doc = $documento->fecha_emision;
                    $cuenta_proveedor->monto = $documento->total - $documento->notas->sum("mtoImpVenta");
                    $cuenta_proveedor->acta = 'DOCUMENTO COMPRA';
                    $cuenta_proveedor->save();
                }
            }

            if ($documento->estado == 'ANULADO') {
                if ($documento->cuenta) {
                    $cuenta_proveedor = CuentaProveedor::find($documento->cuenta->id);
                    $cuenta_proveedor->estado = 'ANULADO';
                    $cuenta_proveedor->update();
                }
            }
        });

        static::deleted(function(Documento $documento){
            //ANULAR LOTE producto
            if($documento->cuenta)
            {
                $cuenta_proveedor = CuentaProveedor::find($documento->cuenta->id);
                $cuenta_proveedor->delete();
            }

        });

    }

}
