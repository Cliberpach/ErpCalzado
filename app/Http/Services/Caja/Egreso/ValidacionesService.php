<?php

namespace App\Http\Services\Caja\Egreso;


use Exception;

class ValidacionesService
{

    public function validacionStore(array $data)
    {
        //======= VERIFICANDO SI EL USUARIO SE ENCUENTRA EN UNA CAJA CON MOV APERTURADO =====
        $movimientoUser     =   movimientoUser();

        if (count($movimientoUser)   === 0) {
           throw new Exception('DEBE PERTENECER A UNA CAJA CON MOVIMIENTO APERTURADO');
        }

        return $movimientoUser;
    }
}
