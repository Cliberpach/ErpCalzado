<?php

namespace App\Http\Services\Caja\Egreso;


use Exception;

class EgresoManager
{

    private EgresoService $s_egreso;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->s_egreso    =   new EgresoService();
    }

    public function store(array $data){
        $this->s_egreso->store($data);
    }

    public function update(array $data,int $id){
        $this->s_egreso->update($data,$id);
    }

    public function destroy(int $id){
        $this->s_egreso->destroy($id);
    }
}
