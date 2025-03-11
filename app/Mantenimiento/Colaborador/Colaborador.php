<?php

namespace App\Mantenimiento\Colaborador;

use App\Mantenimiento\Persona\Persona;
use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    protected $table    =   'colaboradores';
    protected $guarded =    [''];
    
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
