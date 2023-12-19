<?php

namespace App\Ventas;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = [
        'tipo_documento',
        'documento',
        'nombre',
        'nombre_comercial',
        'codigo',
        'tabladetalles_id',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'direccion',
        'correo_electronico',
        'telefono_movil',
        'telefono_fijo',
        'moneda_credito',
        'direccion_negocio',
        'fecha_aniversario',
        'observaciones',
        'zona',

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
        'activo',
        'estado',

        'lat',
        'lng'
    ];

    public function cotizaciones()
    {
        return $this->hasMany('App\Ventas\Cotizacion');
    }

    public function detalle()
    {
        return $this->belongsTo('App\Mantenimiento\Tabla\Detalle', 'tabladetalles_id');
    }

    public function getDocumento(): string
    {
        return $this->tipo_documento . ': ' . $this->documento;
    }

    public function getDepartamento(): string
    {
        return departamentos()->where('id', $this->departamento_id)->first()->nombre;
    }

    public function getDepartamentoZona(): string
    {
        return departamentos()->where('id', $this->departamento_id)->first()->zona;
    }

    public function getProvincia(): string
    {
        return provincias($this->provincia_id)->first()->nombre;
    }

    public function getDistrito(): string
    {
        return distritos($this->distrito_id)->first()->nombre;
    }




}
