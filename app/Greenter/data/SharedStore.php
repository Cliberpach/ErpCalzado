<?php

namespace App\Greenter\Data;

use App\Mantenimiento\Sedes\Sede;
use Illuminate\Http\Request;
use Greenter\Model\Company\Company;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Illuminate\Support\Facades\DB;

class SharedStore
{
    public function getCompany($sede_id): Company
    {
        //======= OBTENIENDO DATA DE EMPRESA =========
        $sede   =   Sede::find($sede_id);
        //====== NOTA COD LOCAL POR DEFECTO 0000 DE LA CENTRAL ======= //

        return (new Company())
            ->setRuc($sede->ruc)
            ->setNombreComercial($sede->razon_social)
            ->setRazonSocial($sede->razon_social)
            ->setAddress((new Address())
                ->setUbigueo($sede->distrito_id)
                ->setDistrito($sede->distrito_nombre)
                ->setProvincia($sede->provincia_nombre)
                ->setDepartamento($sede->departamento_nombre)
                ->setUrbanizacion($sede->urbanizacion)
                ->setCodLocal($sede->codigo_local)
                ->setDireccion($sede->direccion))
            ->setEmail($sede->correo)
            ->setTelephone($sede->telefono);
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
