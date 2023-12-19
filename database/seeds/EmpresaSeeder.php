<?php

use Illuminate\Database\Seeder;

use App\Compras\Proveedor;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Empresa\Facturacion;
use App\Mantenimiento\Empresa\Numeracion;
use App\Ventas\Cliente;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Agroensancha S.R.L
        /*En Local*/
        $empresa = new Empresa();
        $empresa->ruc = '10802398307';
        $empresa->razon_social = 'SISCOM FAC';
        $empresa->razon_social_abreviada = 'SISCOM FAC';
        $empresa->direccion_fiscal = 'AV ESPAÑA 1319';
        $empresa->direccion_llegada = 'TRUJILLO';
        $empresa->dni_representante = '70004110';
        $empresa->nombre_representante = 'NOMBRE APELLIDOPAT APELLIDOMAT';
        $empresa->num_asiento = 'A00001';
        $empresa->ubigeo = '130102';
        $empresa->num_partida = '11036086';
        $empresa->estado_ruc = 'ACTIVO';
        $empresa->estado_fe= '1';
        $empresa->save();

        $facturacion = new Facturacion();
        $facturacion->empresa_id = $empresa->id; //RELACION CON LA EMPRESA
        $facturacion->fe_id = 1095; //ID EMPRESA API
        $facturacion->sol_user = 'CLIBERPA';
        $facturacion->sol_pass = 'P1lester';
        $facturacion->plan = 'free';
        $facturacion->ambiente = 'beta';
        $facturacion->certificado =  null;
        $facturacion->token_code =  'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzM0NjcxMzgsImV4cCI6NDc4NzA2NzEzOCwidXNlcm5hbWUiOiJMZXN0ZXIiLCJjb21wYW55IjoiMTA4MDIzOTgzMDcifQ.YjQK8uvUFn8glmKHwDdPXfhqCIBUU51Rl5hF1OKZ9BC0QDcbPFelunk_mXws9k6wqrXvISitKwVltlpdPfrbx9NoU0sygEhIyr4EanYYdthvtRj18X_bki_fk90sRi1AKf0rXHObVGeXZtdAYIwvYQRy_PmUORJlmJf_K6EYpO6tFib529Eqzs0DaiOVR4k21nCI3u7RDUFlABJMv75IpS24jL9WmtwptkswuskpotC4tbr6FUll7Yk1lG3kniFqf60G0nA30HUpctmjQY7oPCjEySLsjGYqnE78l7r5bdHi9TTUaRr3U4gsdvO39Uzw_TmOm9PxArYd2z19iBoQ3eoF-pYBk3V8xjUCy3-zXzE_2aq3jzZvMoUy7L89iXw2zODca3JcszM_BM2gxx97ulTm62lGPYiPLW1hLath3HvwyYNGH6Xihd9I-xNNwK3MGiNnbbmNqKh5FPGK-DIBLfnm4y0QJil0lM89jXjaaTeNOHuN8By45mKrzG6jZSxY8pG-YoncHMRMRwzMXu6SxjQgWuDvXk53BMnw3xOtvA1QwslJmnhblpiG9-_AAWDSQuQXmz4mQaK375aSGLc8QHXjarKuq6ToXVoF29hBh9CWuXt7F_5wa54Xbq6J_EPNtu4vdG3vrul_Q2zSuMMQRZygjDIJd8mT37200Ft3CLc';
        $facturacion->save();


        /*En Servidor
        $empresa = new Empresa();
        $empresa->ruc = '20608741578';
        $empresa->razon_social = 'CORPORACION DE REPUESTOS ELECTROMOTRICES VALVERDE E.I.R.L.';
        $empresa->razon_social_abreviada = 'CORPORACION DE REPUESTOS ELECTROMOTRICES VALVERDE E.I.R.L.';
        $empresa->direccion_fiscal = 'AV. CESAR VALLEJO NRO. 1717 URB. EL PALOMAR LA LIBERTAD - TRUJILLO - TRUJILLO';
        $empresa->direccion_llegada = 'AV. CESAR VALLEJO NRO. 1717 URB. EL PALOMAR LA LIBERTAD - TRUJILLO - TRUJILLO';
        $empresa->dni_representante = '76682608';
        $empresa->nombre_representante = 'NOMBRE APELLIDOPAT APELLIDOMAT';
        $empresa->num_asiento = 'A00001';
        $empresa->num_partida = '11036086';
        $empresa->ubigeo = '13';
        $empresa->nombre_logo = 'corvalperu.jpg';
        $empresa->ruta_logo = 'public/empresas/logos/oBeXUrV1fBtySgntsQ3uGwlw7Src40d6SEghrKxc.jpg';
        $empresa->estado_ruc = 'ACTIVO';
        $empresa->estado_fe= '1';
        $empresa->save();

        $facturacion = new Facturacion();
        $facturacion->empresa_id = $empresa->id; //RELACION CON LA EMPRESA
        $facturacion->fe_id = 1237; //ID EMPRESA API
        $facturacion->sol_user = 'USUARIO1';
        $facturacion->sol_pass = 'MiUsuario123';
        $facturacion->plan = 'free';
        $facturacion->ambiente = 'beta';
        $facturacion->certificado =  null;
        $facturacion->token_code =  'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Mzg5MjM3MjgsImV4cCI6NDc5MjUyMzcyOCwidXNlcm5hbWUiOiJMZXN0ZXIiLCJjb21wYW55IjoiMjA2MDg3NDE1NzgifQ.MfETLeOy-ArWZFyoESgTcqygN2VDj55xkuXPwlbKGAJe9NTG8-UQPNQy7BgFrWEWhnKfgw6qoXFO6xEESHfAKEAnBWZQ3a2EK2TpV4ebhlyoHMgOiM3qTj48yG5HDBgSraUIpjqFBn26Vw1hv33PFo1yzvAJtBQ5C1v_CJr6xfs3cHSHeBwVp6yNIUeN5sxHuGSVUpoZ_KUnTg9QmXCf6fdzU_w9IRH7HO44Rbs5MLPUOpB-OzPMXM12nexkPbdJ0rGxoSoPyzABkWy_yTVEBboQINfYoX94L0Ffp5MYNGY-dsWfaGPLyg_LJbeufYvi36woMOnNfuqQguixr9bgxL79Xba-x7b0kHl-qSMZYbx8CliP_M8AbbNlo-lO_tzJzDTtAbOvHUpuIxITOnyVFXSZtzSOdsgFIgr9QVDMp4WJEemH20QmjTpWttqCtUbKypYqcZHogwwGJvsqEH3op8NmOUQFqJkObgVeX93HhO2lO-PTZRAdE2VUYyVaDLV14cDKHRnKzQhfJuAp6wdyS9h9QdPYqov990FRJNdOffLF5cgqPgOZ6IBUl3l5x7-lgmbmYDWEXEYKer0F0Z00C39a7JqjtP_7hiOTBjn1G5COn0MqAeH0BA-pkh_GIpuI4I7w7XEury6JlctgFlWjWNrfA_bXVinva17FhyIrR_8';
        $facturacion->save();*/


        Numeracion::create([
            'empresa_id' => $empresa->id,
            'serie' => 'F001',
            'tipo_comprobante' => 127,
            'numero_iniciar' => 1,
            'emision_iniciada' => 1,
        ]);

        Numeracion::create([
            'empresa_id' => $empresa->id,
            'serie' => 'B001',
            'tipo_comprobante' => 128,
            'numero_iniciar' => 1,
            'emision_iniciada' => 1,
        ]);

        Numeracion::create([
            'empresa_id' => $empresa->id,
            'serie' => 'N001',
            'tipo_comprobante' => 129,
            'numero_iniciar' => 1,
            'emision_iniciada' => 1,
        ]);



        $proveedor = new Proveedor();
        $proveedor->descripcion = 'PROVEEDORES VARIOS';
        $proveedor->tipo_documento = 'RUC';
        $proveedor->ruc = '11111111111';
        $proveedor->tipo_persona = 'PERSONA JURIDICA';
        $proveedor->direccion = 'Jr. Puerto Inca Nro. 250 Dpto. 402';
        $proveedor->correo = 'CCUBAS@UNITRU.EDU.PE';
        $proveedor->telefono = '043313520';
        $proveedor->zona = 'NOROESTE';
        $proveedor->contacto = 'CARLOS CUBAS';
        $proveedor->telefono_contacto = '950837445';
        $proveedor->correo_contacto = 'CCUBAS@UNITRU.EDU.PE';
        $proveedor->transporte = 'SEDACHIMBOTE S.A.';
        $proveedor->ruc_transporte = '20136341066';
        $proveedor->direccion_transporte = 'JR. LA CALETA NRO. 176 A.H.  MANUEL SEOANE CORRALES - ANCASH - SANTA - CHIMBOTE';

        $proveedor->estado_transporte = 'ACTIVO';
        $proveedor->estado_documento = 'ACTIVO';
        $proveedor->save();

        $cliente = new Cliente();
        $cliente->tipo_documento = 'DNI';

        $cliente->documento = '99999999';
        $cliente->tabladetalles_id = 121;
        $cliente->nombre = 'CLIENTES VARIOS';
        $cliente->codigo = null;
        $cliente->zona = 'NORTE';

        $cliente->departamento_id = '13';
        $cliente->provincia_id = '1301';
        $cliente->distrito_id = '130101';
        $cliente->direccion = 'DIRECCION TRUJILLO';
        $cliente->correo_electronico = null;
        $cliente->telefono_movil = '999999999';
        $cliente->telefono_fijo = null;
        $cliente->save();

    }
}
