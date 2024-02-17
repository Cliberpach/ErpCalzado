<?php

use App\Parametro;
use Illuminate\Database\Seeder;

class ParametroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //CONSULTA DNI Y RUC
         $parametro = new Parametro();
         $parametro->http = 'https://dniruc.apisperu.com/api/v1/ruc/';
         $parametro->token = '?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6IkFETUlOSVNUUkFDSU9OQEFHUk9FTlNBTkNIQS5DT00ifQ.OCKjttuhSkWZkkZUxZFl-rkmXFD2uyrqEDoq7icEkHw';
         $parametro->save();
 
         $parametro = new Parametro();
         $parametro->http = 'https://dniruc.apisperu.com/api/v1/dni/';
         $parametro->token = '?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6IkFETUlOSVNUUkFDSU9OQEFHUk9FTlNBTkNIQS5DT00ifQ.OCKjttuhSkWZkkZUxZFl-rkmXFD2uyrqEDoq7icEkHw';
         $parametro->save();
 
         //FACTURACION ELECTRONICA
         //TOKEN LOGUEO
         $parametro = new Parametro();
         $parametro->usuario_proveedor = 'lester';
         $parametro->contra_proveedor =  'P@chekito321';
         $parametro->save();
    }
}
