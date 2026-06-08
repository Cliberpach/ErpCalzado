<?php

namespace App\Mantenimiento\Colaborador;

use App\Mantenimiento\Persona\Persona;
use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    protected $table    =   'colaboradores';
    protected $fillable = [
        'persona_id',
        'estado',
        'sede_id',
        'tipo_documento_id',
        'tipo_documento_nombre',
        'cargo_id',
        'nro_documento',
        'nombre',
        'direccion',
        'telefono',
        'dias_trabajo',
        'dias_descanso',
        'pago_mensual',
        'pago_dia',
    ];
    
    public $timestamps=true;
        
    public function persona()
    {
        return $this->belongsTo(Persona::class,'persona_id');
    }

    public function getBanco(): string
    {
        $banco = bancos()->where('simbolo', $this->tipo_banco)->first();
        if (is_null($banco))
            return "-";
        else
            return $banco->descripcion;
    }

    public function getArea(): string
    {
        $area = areas()->where('simbolo', $this->area)->first();
        if (is_null($area))
            return "-";
        else
            return $area->descripcion;
    }

    public function getCargo(): string
    {
        $cargo = cargos()->where('simbolo', $this->cargo)->first();
        if (is_null($cargo))
            return "-";
        else
            return $cargo->descripcion;
    }
}
