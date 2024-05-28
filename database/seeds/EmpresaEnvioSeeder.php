<?php

use Illuminate\Database\Seeder;
use App\Mantenimiento\MetodoEntrega\MetodoEntrega;
use App\Mantenimiento\MetodoEntrega\EmpresaEnvioSede;
use App\Mantenimiento\Empresa\Empresa;

class EmpresaEnvioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $empresa        =   Empresa::first();

        $empresa_envio  =   new MetodoEntrega();
        $empresa_envio->empresa     =   $empresa->razon_social;
        $empresa_envio->tipo_envio  =   'RECOJO EN TIENDA';
        $empresa_envio->save();
        
        //===== SEDE =========
        $sede_envio     =   new EmpresaEnvioSede();
        $sede_envio->empresa_envio_id   =   $empresa_envio->id;
        $sede_envio->direccion          =   $empresa->direccion_fiscal;
        $sede_envio->departamento       =   "LA LIBERTAD";
        $sede_envio->provincia          =   "TRUJILLO";
        $sede_envio->distrito           =   "TRUJILLO";
        $sede_envio->save();
    }
}
