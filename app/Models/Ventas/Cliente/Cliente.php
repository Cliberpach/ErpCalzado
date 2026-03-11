<?php

namespace App\Models\Ventas\Cliente;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table    = 'clientes';

    protected $fillable = [
        'tipo_documento_id',
        'tipo_documento',
        'documento',
        'nombre',
        'nombre_comercial',
        'codigo',
        'tipo_cliente_id',
        'tipo_cliente_nombre',

        'departamento_id',
        'provincia_id',
        'distrito_id',

        'direccion',
        'zona',
        'correo_electronico',
        'telefono_movil',
        'telefono_fijo',

        'moneda_credito',
        'limite_credito',

        'nombre_contacto',
        'telefono_contacto',
        'correo_electronico_contacto',

        'direccion_negocio',
        'fecha_aniversario',
        'observaciones',

        'facebook',
        'instagram',
        'web',

        'hora_inicio',
        'hora_termino',

        'nombre_propietario',
        'direccion_propietario',
        'fecha_nacimiento_prop',
        'celular_propietario',
        'correo_propietario',

        'crm',
        'agente_retencion',
        'tasa_retencion',
        'monto_mayor',

        'activo',
        'estado',

        'lat',
        'lng',

        'ruta_logo',
        'nombre_logo'
    ];
}
