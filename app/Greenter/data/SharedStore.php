<?php

namespace App\Greenter\Data;

use Illuminate\Http\Request;
use Greenter\Model\Company\Company;
//use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Illuminate\Support\Facades\DB;

class SharedStore
{
    public function getCompany(): Company
    {
        //======= OBTENIENDO DATA DE EMPRESA =========
        $empresa    =   DB::select('select * from empresas as e where e.id=1')[0];
        //====== NOTA COD LOCAL POR DEFECTO 0000 DE LA CENTRAL ======= //

        return (new Company())
            ->setRuc($empresa->ruc)
            ->setNombreComercial($empresa->razon_social_abreviada)
            ->setRazonSocial($empresa->razon_social)
            ->setAddress((new Address())
                ->setUbigueo($empresa->ubigeo)
                ->setDistrito($empresa->distrito)
                ->setProvincia($empresa->provincia)
                ->setDepartamento($empresa->departamento)
                ->setUrbanizacion($empresa->urbanizacion)
                ->setCodLocal($empresa->cod_local)
                ->setDireccion($empresa->direccion_fiscal))
            ->setEmail($empresa->correo)
            ->setTelephone($empresa->celular);
    }

    public function getClientPerson(): Client
    {
        $client = new Client();
        $client->setTipoDoc('1')
            ->setNumDoc('48285071')
            ->setRznSocial('NIPAO GUVI')
            ->setAddress((new Address())
                ->setDireccion('Calle fusión 453, SAN MIGUEL - LIMA - PERU'));

        return $client;
    }

    public function getClient(): Client
    {
        $client = new Client();
        $client->setTipoDoc('6')
            ->setNumDoc('20000000001')
            ->setRznSocial('EMPRESA 1 S.A.C.')
            ->setAddress((new Address())
                ->setDireccion('JR. NIQUEL MZA. F LOTE. 3 URB.  INDUSTRIAL INFAÑTAS - LIMA - LIMA -PERU'))
            ->setEmail('client@corp.com')
            ->setTelephone('01-445566');

        return $client;
    }

    public function getSeller(): Client
    {
        $client = new Client();
        $client->setTipoDoc('1')
            ->setNumDoc('44556677')
            ->setRznSocial('VENDEDOR 1')
            ->setAddress((new Address())
                ->setDireccion('AV INFINITE - LIMA - LIMA - PERU'));

        return $client;
    }
}
