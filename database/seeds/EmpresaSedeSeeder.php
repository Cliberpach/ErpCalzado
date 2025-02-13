<?php

use App\Mantenimiento\Empresa\Numeracion;
use App\Mantenimiento\Sedes\Sede;
use Illuminate\Database\Seeder;

class EmpresaSedeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sede                           = new Sede();
        $sede->empresa_id               = 1;
        $sede->ruc                      = '10802398307';
        $sede->razon_social             = 'SISCOM FAC';
        $sede->direccion                = 'AV ESPAÑA 1319';
        $sede->telefono                 = '995432164';
        $sede->correo                   = 'sedeprincipal@gmail.com';
        $sede->departamento_id          = '13';
        $sede->provincia_id             = '1301';
        $sede->distrito_id              = '130101';
        $sede->departamento_nombre      = 'LA LIBERTAD';
        $sede->provincia_nombre         = 'TRUJILLO';
        $sede->distrito_nombre          = 'TRUJILLO';
        $sede->codigo_local             =  '0000';
        $sede->tipo_sede                =  'PRINCIPAL';
        $sede->serie                    =   '001';
        $sede->save();


        Numeracion::create([
            'empresa_id' => 1,
            'serie' => 'F001',
            'tipo_comprobante' => 127,
            'numero_iniciar' => 1,
            'emision_iniciada' => 1,
            'sede_id'=>1
        ]);

        Numeracion::create([
            'empresa_id' => 1,
            'serie' => 'B001',
            'tipo_comprobante' => 128,
            'numero_iniciar' => 1,
            'emision_iniciada' => 1,
            'sede_id'=>1
        ]);

        Numeracion::create([
            'empresa_id' => 1,
            'serie' => 'N001',
            'tipo_comprobante' => 129,
            'numero_iniciar' => 1,
            'emision_iniciada' => 1,
            'sede_id'=>1
        ]);
    }
}
