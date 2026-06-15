<?php

namespace App\Http\Services\Contabilidad\ConsultaSunat;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ConsultaSunatManager
{
    private function getToken(string $clientId, string $clientSecret): string
    {
        $cacheKey = 'cpe_token_' . md5($clientId);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(15)
            ->asForm()
            ->post("https://api-seguridad.sunat.gob.pe/v1/clientesextranet/{$clientId}/oauth2/token/", [
                'grant_type'    => 'client_credentials',
                'scope'         => 'https://api.sunat.gob.pe/v1/contribuyente/contribuyentes',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ]);

        if (!$response->successful()) {
            throw new Exception('Error al obtener token SUNAT CPE: ' . $response->body());
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            throw new Exception('SUNAT no devolvió access_token. Verifique las credenciales CPE.');
        }

        $ttl = max(60, ($data['expires_in'] ?? 3600) - 60);
        Cache::put($cacheKey, $data['access_token'], $ttl);

        return $data['access_token'];
    }

    private function validarUno(string $ruc, array $datos, string $token): array
    {
        $response = Http::timeout(15)
            ->withToken($token)
            ->get("https://api.sunat.gob.pe/v1/contribuyente/contribuyentes/{$ruc}/validarcomprobante", [
                'numRuc'       => $ruc,
                'codComp'      => $datos['codComp'],
                'numeroSerie'  => $datos['serie'],
                'numero'       => $datos['numero'],
                'fechaEmision' => $datos['fechaEmision'],
                'monto'        => $datos['monto'],
            ]);

        if (!$response->successful()) {
            return array_merge($datos, [
                'estadoCp'    => null,
                'descripcion' => 'Error HTTP ' . $response->status(),
                'error'       => true,
            ]);
        }

        $body     = $response->json();
        $estadoCp = $body['data']['estadoCp'] ?? null;

        return array_merge($datos, [
            'estadoCp'    => $estadoCp,
            'descripcion' => self::descripcionEstado($estadoCp),
            'error'       => false,
        ]);
    }

    public function validarLote(string $ruc, array $comprobantes, string $clientId, string $clientSecret): array
    {
        $token      = $this->getToken($clientId, $clientSecret);
        $resultados = [];

        foreach ($comprobantes as $i => $comp) {
            if ($i > 0) {
                usleep(200000); // 200ms entre llamadas
            }
            $resultados[] = $this->validarUno($ruc, $comp, $token);
        }

        return $resultados;
    }

    public static function descripcionEstado(?int $estado): string
    {
        $map = [
            0 => 'No existe en SUNAT',
            1 => 'Aceptado',
            2 => 'Anulado',
            3 => 'Autorizado',
            4 => 'No autorizado',
        ];

        return $map[$estado] ?? 'Sin respuesta';
    }
}
