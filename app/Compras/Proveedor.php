<?php

namespace App\Compras;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    public $timestamps = true;
    protected $fillable =[
        'ruc',
        'dni',
        'descripcion',
        'tipo_documento',
        'tipo_persona',
        'direccion',
        'correo',
        'telefono',
        'web',
        'zona',

        'contacto',
        'celular_contacto',
        'telefono_contacto',
        'correo_contacto',

        'ruc_transporte',
        'transporte',
        'direccion_transporte',
        'direccion_almacen',
        'estado_transporte',

        'facebook',
        'instagram',

        'calidad',
        'celular_calidad',
        'telefono_calidad',
        'correo_calidad',
        'estado_documento',
        'estado',

    ];

    public function bancos()
    {
        return $this->hasMany('App\Compras\Banco');
    }
    public function ordenes()
    {
        return $this->hasMany('App\Compras\Orden');
    }
    public function documento(){
        return $this->dni==null ? $this->ruc : $this->dni;
    }
    public function tipodocumento(){
        return $this->dni==null ? 'DNI' : 'RUC';
    }
    public function getDocumento(): string
    {
        $resultado  =   '';
        if($this->tipo_documento == "RUC"){
            $resultado  =   $this->tipo_documento.': '.$this->ruc;
        }
        if($this->tipo_documento == "DNI"){
            $resultado  =   $this->tipo_documento.': '.$this->dni;
        }
        return $resultado;
    }
}
