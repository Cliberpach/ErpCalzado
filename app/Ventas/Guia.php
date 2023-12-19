<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class Guia extends Model
{
    protected $table = 'guias_remision';
    protected $fillable = [
        'documento_id',
        'nota_salida_id',
        'cantidad_productos',
        'peso_productos',

        'tienda_id',

        'direccion_llegada',
        // 'direccion_partida', //EMPRESA DOCUMENTO DE VENTA PLANO

        //OFICINA
        'ruc_transporte_oficina',
        'nombre_transporte_oficina',
        //DOMICILIO
        'ruc_transporte_domicilio',
        'nombre_transporte_domicilio',

        'observacion',
        'ubigeo_llegada',
        'ubigeo_partida',
        'estado',
        'sunat',
        'correlativo',
        'serie',
        'ruta_comprobante_archivo',
        'nombre_comprobante_archivo',
        'dni_conductor',
        'placa_vehiculo',

        'fecha_emision',
        'ruc_empresa',
        'empresa',
        'empresa_id',
        'direccion_empresa',

        'motivo_traslado',

        'tipo_documento_cliente',
        'documento_cliente',
        'cliente',
        'cliente_id',
        'user_id',

        'getCdrResponse',
        'regularize',
        'getRegularizeResponse',
    ];

    public function documento()
    {
        return $this->belongsTo('App\Ventas\Documento\Documento','documento_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleGuia::class,'guia_id');
    }

    public function tienda()
    {
        return $this->belongsTo('App\Ventas\Tienda','tienda_id');
    }

    public function clienteEntidad()
    {
        return $this->belongsTo('App\Ventas\Cliente', 'cliente_id');
    }

    public function codTraslado(): string
    {
        $motivo = motivo_traslado()->where('id', $this->motivo_traslado)->first();
        if (is_null($motivo))
            return "01";
        else
            return strval($motivo->simbolo);
    }

    public function desTraslado(): string
    {
        $motivo = motivo_traslado()->where('id', $this->motivo_traslado)->first();
        if (is_null($motivo))
            return "VENTA";
        else
            return strval($motivo->descripcion);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tipoDocumentoCliente(): string
    {
        $documento = tipos_documento()->where('simbolo', $this->tipo_documento_cliente)->first();
        if (is_null($documento))
            return "-";
        else
            return $documento->parametro;
    }
}
