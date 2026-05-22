<?php

namespace App\Http\Services\Mantenimiento\Empresa;

use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;

class EmpresaDto
{
    public function getDtoUpdate(array $data): array
    {
        $department =   Departamento::findOrFail($data['departamento']);
        $province   =   Provincia::findOrFail($data['provincia']);
        $district   =   Distrito::findOrFail($data['distrito']);

        return [

            // =====================
            // EMPRESA PRINCIPAL
            // =====================
            'ruc' => $data['ruc'] ?? null,
            'razon_social' => isset($data['razon_social'])
                ? mb_strtoupper($data['razon_social'], 'UTF-8')
                : null,

            'razon_social_abreviada' => isset($data['razon_social_abreviada'])
                ? mb_strtoupper($data['razon_social_abreviada'], 'UTF-8')
                : null,

            // =====================
            // LOGO
            // =====================
            'ruta_logo' => $data['ruta_logo'] ?? null,
            'nombre_logo' => $data['nombre_logo'] ?? null,
            'base64_logo' => $data['base64_logo'] ?? null,

            // =====================
            // DIRECCIONES
            // =====================
            'direccion_fiscal' => mb_strtoupper($data['direccion_fiscal'] ?? '', 'UTF-8') ?: null,
            'direccion_llegada' => mb_strtoupper($data['direccion_llegada'] ?? '', 'UTF-8') ?: null,

            // =====================
            // UBICACIÓN (TEXTOS)
            // =====================
            'departamento' => $department->nombre,
            'provincia' => $province->nombre,
            'distrito' => $district->nombre,

            'ubigeo' => $district->id,
            'urbanizacion' => $data['urbanizacion'] ?? null,
            'cod_local' => $data['cod_local'] ?? null,

            // =====================
            // UBICACIÓN (IDS REALES BD)
            // =====================
            'departamento_id' => $data['departamento'] ?? null,
            'provincia_id' => $data['provincia'] ?? null,
            'distrito_id' => $data['distrito'] ?? null,

            // =====================
            // CONTACTO
            // =====================
            'telefono' => $data['telefono'] ?? null,
            'celular' => $data['celular'] ?? null,
            'correo' => $data['correo'] ?? null,

            // =====================
            // REDES
            // =====================
            'facebook' => $data['facebook'] ?? null,
            'instagram' => $data['instagram'] ?? null,
            'web' => $data['web'] ?? null,

            // =====================
            // REPRESENTANTE
            // =====================
            'dni_representante' => $data['dni_representante'] ?? null,
            'nombre_representante' => $data['nombre_representante'] ?? null,

            // =====================
            // SUNARP
            // =====================
            'num_asiento' => $data['num_asiento'] ?? null,
            'num_partida' => $data['num_partida'] ?? null,

            // =====================
            // ESTADOS
            // =====================
            'estado_ruc' => $data['estado_ruc'] ?? 'ACTIVO',
            'estado_dni_representante' => $data['estado_dni_representante'] ?? 'VIGENTE',
            'estado' => $data['estado'] ?? null,
            'estado_fe' => $data['estado_fe'] ?? null,
            'condicion' => $data['condicion'] ?? true,

            // =====================
            // CONFIG
            // =====================
            'igv' => $data['igv'] ?? null,

            // =====================
            // MEDIDAS
            // =====================
            'alto_adhesivo' => $data['alto_adhesivo'] ?? null,
            'ancho_adhesivo' => $data['ancho_adhesivo'] ?? null,
            'ancho_pdf_bulto' => $data['ancho_pdf_bulto'] ?? null,
            'alto_pdf_bulto' => $data['alto_pdf_bulto'] ?? null,
        ];
    }
}
