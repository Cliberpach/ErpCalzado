<?php

use Illuminate\Database\Seeder;
use App\Mantenimiento\Greenter\GreenterConfig;

class GreenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $greenter_config                            =   new GreenterConfig();
        $greenter_config->empresa_id                =   1;
        $greenter_config->ruta_certificado          =   'greenter/certificado/certificate_test.pem';
        $greenter_config->nombre_certificado        =   'certificate_test.pem';
        $greenter_config->id_api_guia_remision      =   'test-85e5b0ae-255c-4891-a595-0b98c65c9854';
        $greenter_config->clave_api_guia_remision   =   'test-Hty/M6QshYvPgItX2P0+Kw==';
        $greenter_config->modo                      =   'PRODUCCION';   
        $greenter_config->sol_user                  =   'CLIBERPA';
        $greenter_config->sol_pass                  =   'P1lester';
        $greenter_config->save();

        $greenter_config                            =   new GreenterConfig();
        $greenter_config->empresa_id                =   1;
        $greenter_config->ruta_certificado          =   'greenter/certificado/certificate_test.pem';
        $greenter_config->nombre_certificado        =   'certificate_test.pem';
        $greenter_config->id_api_guia_remision      =   'test-85e5b0ae-255c-4891-a595-0b98c65c9854';
        $greenter_config->clave_api_guia_remision   =   'test-Hty/M6QshYvPgItX2P0+Kw==';
        $greenter_config->modo                      =   'BETA';   
        $greenter_config->sol_user                  =   'MODDATOS';
        $greenter_config->sol_pass                  =   'MODDATOS';
        $greenter_config->save();
    }
}
