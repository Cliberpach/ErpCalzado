<?php

namespace App\Http\Services\Ventas\Clientes;

use App\Mantenimiento\Tabla\Detalle;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Models\Ventas\TipoCliente\TipoCliente;

class ClienteDto
{
    public function getDtoStore(array $data): array
    {
        $type_customer  = TipoCliente::findOrFail($data['type_customer']);
        $distrito       = Distrito::findOrFail($data['district']);
        $departamento   = Departamento::findOrFail($data['department']);
        $tipo_documento = Detalle::findOrFail($data['type_identity_document']);

        $dto = [];

        $dto['tipo_documento_id']       = $tipo_documento->id;
        $dto['tipo_documento']          = $tipo_documento->simbolo;

        $dto['documento']               = $data['nro_document'];
        $dto['tipo_cliente_id']         = $type_customer->id;
        $dto['tipo_cliente_nombre']     = $type_customer->nombre;

        $dto['nombre']                  = mb_strtoupper(trim($data['name']), 'UTF-8');

        $dto['codigo']                  = $distrito->id;
        $dto['zona']                    = $departamento->zona;

        $dto['departamento_id']         = $data['department'] ?? null;
        $dto['provincia_id']            = $data['province'] ?? null;
        $dto['distrito_id']             = $data['district'] ?? null;

        $dto['direccion']               = mb_strtoupper(trim($data['address']), 'UTF-8') ?? null;
        $dto['correo_electronico']      = $data['email'] ?? null;
        $dto['telefono_movil']          = $data['phone'] ?? null;

        //======== PAGE 2 ========
        $dto['direccion_negocio'] = $data['direccion_negocio'] ?? null;

        $dto['fecha_aniversario'] = (
            isset($data['fecha_aniversario']) && $data['fecha_aniversario'] !== '-'
        )
            ? $data['fecha_aniversario']
            : null;

        $dto['observaciones'] = $data['observaciones'] ?? null;

        $dto['facebook']  = $data['facebook'] ?? null;
        $dto['instagram'] = $data['instagram'] ?? null;
        $dto['web']       = $data['web'] ?? null;

        $dto['hora_inicio']  = $data['hora_inicio'] ?? null;
        $dto['hora_termino'] = $data['hora_termino'] ?? null;

        //========== PAGE 3 ========
        $dto['nombre_propietario'] = $data['nombre_propietario'] ?? null;

        $dto['direccion_propietario'] = $data['direccion_propietario'] ?? null;

        $dto['fecha_nacimiento_prop'] = (
            isset($data['fecha_nacimiento_prop']) && $data['fecha_nacimiento_prop'] !== '-'
        )
            ? $data['fecha_nacimiento_prop']
            : null;

        $dto['celular_propietario'] = $data['celular_propietario'] ?? null;

        $dto['correo_propietario'] = $data['correo_propietario'] ?? null;

        //$dto['url_logo'] = $data['url_logo'] ?? null;

        return $dto;
    }
}
