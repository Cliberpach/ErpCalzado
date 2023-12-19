<?php

namespace App\Mantenimiento\Persona;

use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Vendedor\Vendedor;
use App\UserPersona;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';
    protected $fillable =[
        'tipo_documento',
        'documento',
        'codigo_verificacion',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'sexo',
        'estado_civil',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'direccion',
        'correo_electronico',
        'correo_corporativo',
        'telefono_movil',
        'telefono_fijo',
        'telefono_trabajo',
        'estado_documento',
        'estado'
    ];
    public function colaborador() {
        return $this->hasOne(Colaborador::class,'persona_id');
    }

    public function vendedor()
    {
        return $this->hasOne(Vendedor::class,'persona_id');
    }
    
    public function user_persona()
    {
        return $this->hasOne(UserPersona::class,'persona_id');
    }

    public function getDocumento(): string
    {
        return $this->tipo_documento . ': ' . $this->documento;
    }

    public function getApellidosYNombres(): string
    {
        return $this->apellido_paterno . ' ' . $this->apellido_materno . ', ' . $this->nombres;
    }

    public function getTipoDocumento(): string
    {
        return tipos_documento()->where('simbolo', $this->tipo_documento)->first()->descripcion;
    }

    public function getSexo(): string
    {
        return tipos_sexo()->where('simbolo', $this->sexo)->first()->descripcion;
    }

    public function getEstadoCivil(): string
    {
        if(estados_civiles()->where('simbolo', $this->estado_civil)->first()){
            return estados_civiles()->where('simbolo', $this->estado_civil)->first()->descripcion;
        }else{
            return '';
        }
    }

    public function getDepartamento(): string
    {
        return departamentos()->where('id', $this->departamento_id)->first()->nombre;
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
