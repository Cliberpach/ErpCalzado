<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Http\Services\Contabilidad\ConsultaSunat\ConsultaSunatManager;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConsultaSunatController extends Controller
{
    private ConsultaSunatManager $manager;

    public function __construct()
    {
        $this->manager = new ConsultaSunatManager();
    }

    public function index()
    {
        $this->authorize('haveaccess', 'contabilidad.sunat.index');
        return view('contabilidad.sunat.index');
    }

    public function indexIndividual()
    {
        $this->authorize('haveaccess', 'contabilidad.sunat.index');
        return view('contabilidad.sunat.individual');
    }

    public function validarIndividual(Request $request)
    {
        $this->authorize('haveaccess', 'contabilidad.sunat.index');

        try {
            $codComp  = $request->tipo_comprobante;
            $serie    = strtoupper(trim($request->serie ?? ''));
            $numero   = (int) $request->numero;
            $fechaRaw = $request->fecha_emision;
            $monto    = $request->monto;

            if (!$codComp || !$serie || !$numero || !$fechaRaw || $monto === null || $monto === '') {
                throw new Exception('Todos los campos son obligatorios.');
            }

            $fechaEmision = Carbon::createFromFormat('Y-m-d', $fechaRaw)->format('d/m/Y');
            $montoFmt     = number_format((float) $monto, 2, '.', '');

            $config = DB::select('
                SELECT gc.cpe_client_id, gc.cpe_client_secret, e.ruc
                FROM greenter_config AS gc
                INNER JOIN empresas AS e ON e.id = gc.empresa_id
                WHERE gc.empresa_id = 1 AND gc.modo = "PRODUCCION"
                LIMIT 1
            ');

            if (count($config) === 0) {
                throw new Exception('No se encontró configuración Greenter. Configure en Mantenimiento > Empresas.');
            }

            if (!$config[0]->cpe_client_id || !$config[0]->cpe_client_secret) {
                throw new Exception('Configure las credenciales CPE (client_id y client_secret) en Mantenimiento > Empresas > Greenter.');
            }

            $ruc          = $config[0]->ruc;
            $clientId     = $config[0]->cpe_client_id;
            $clientSecret = $config[0]->cpe_client_secret;

            $resultados = $this->manager->validarLote($ruc, [[
                'codComp'      => $codComp,
                'serie'        => $serie,
                'numero'       => (string) $numero,
                'fechaEmision' => $fechaEmision,
                'monto'        => $montoFmt,
            ]], $clientId, $clientSecret);

            return response()->json(['success' => true, 'data' => $resultados[0] ?? null]);

        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function validar(Request $request)
    {
        $this->authorize('haveaccess', 'contabilidad.sunat.index');

        try {
            $codComp      = $request->tipo_comprobante;
            $serie        = strtoupper(trim($request->serie ?? ''));
            $numeroDesde  = (int) $request->numero_desde;
            $numeroHasta  = (int) ($request->numero_hasta ?: $numeroDesde);

            if (!$codComp || !$serie || !$numeroDesde) {
                throw new Exception('Ingrese tipo de comprobante, serie y número.');
            }

            if ($numeroHasta < $numeroDesde) {
                $numeroHasta = $numeroDesde;
            }

            if (($numeroHasta - $numeroDesde) >= 50) {
                throw new Exception('El rango máximo permitido es 50 comprobantes por consulta.');
            }

            $config = DB::select('
                SELECT gc.cpe_client_id, gc.cpe_client_secret, e.ruc
                FROM greenter_config AS gc
                INNER JOIN empresas AS e ON e.id = gc.empresa_id
                WHERE gc.empresa_id = 1 AND gc.modo = "PRODUCCION"
                LIMIT 1
            ');

            if (count($config) === 0) {
                throw new Exception('No se encontró configuración Greenter. Configure en Mantenimiento > Empresas.');
            }

            if (!$config[0]->cpe_client_id || !$config[0]->cpe_client_secret) {
                throw new Exception('Configure las credenciales CPE (client_id y client_secret) en Mantenimiento > Empresas > Greenter.');
            }

            $ruc          = $config[0]->ruc;
            $clientId     = $config[0]->cpe_client_id;
            $clientSecret = $config[0]->cpe_client_secret;

            // Busca documentos locales para obtener fecha y monto
            $docsLocales = $this->getDocsLocales($codComp, $serie, $numeroDesde, $numeroHasta);

            $paraConsultar  = [];
            $noEncontrados  = [];

            for ($num = $numeroDesde; $num <= $numeroHasta; $num++) {
                if (isset($docsLocales[$num])) {
                    $doc = $docsLocales[$num];
                    $paraConsultar[] = [
                        'codComp'      => $codComp,
                        'serie'        => $serie,
                        'numero'       => (string) $num,
                        'fechaEmision' => $doc['fecha'],
                        'monto'        => $doc['monto'],
                    ];
                } else {
                    $noEncontrados[] = [
                        'serie'        => $serie,
                        'numero'       => $num,
                        'fechaEmision' => '-',
                        'monto'        => '-',
                        'estadoCp'     => null,
                        'descripcion'  => 'No encontrado en sistema local',
                        'error'        => false,
                        'localNotFound'=> true,
                    ];
                }
            }

            $resultados = [];

            if (!empty($paraConsultar)) {
                $resultados = $this->manager->validarLote($ruc, $paraConsultar, $clientId, $clientSecret);
            }

            foreach ($noEncontrados as $nf) {
                $resultados[] = $nf;
            }

            usort($resultados, fn($a, $b) => (int)$a['numero'] <=> (int)$b['numero']);

            return response()->json(['success' => true, 'data' => $resultados]);

        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    private function getDocsLocales(string $codComp, string $serie, int $desde, int $hasta): array
    {
        $result = [];

        if ($codComp === '01' || $codComp === '03') {
            $rows = DB::select('
                SELECT correlativo, fecha_documento, total_pagar
                FROM cotizacion_documento
                WHERE tipo_venta_codigo = ? AND serie = ? AND correlativo BETWEEN ? AND ?
                AND estado != "ANULADO"
            ', [$codComp, $serie, $desde, $hasta]);

            foreach ($rows as $row) {
                $result[(int) $row->correlativo] = [
                    'fecha' => Carbon::parse($row->fecha_documento)->format('d/m/Y'),
                    'monto' => number_format((float) $row->total_pagar, 2, '.', ''),
                ];
            }
        }

        // Notas de crédito (07)
        if ($codComp === '07') {
            $rows = DB::select('
                SELECT correlativo, fechaEmision, mtoImpVenta
                FROM nota_electronica
                WHERE serie = ? AND correlativo BETWEEN ? AND ?
                AND estado != "ANULADO"
            ', [$serie, $desde, $hasta]);

            foreach ($rows as $row) {
                $result[(int) $row->correlativo] = [
                    'fecha' => Carbon::parse($row->fechaEmision)->format('d/m/Y'),
                    'monto' => number_format((float) $row->mtoImpVenta, 2, '.', ''),
                ];
            }
        }

        return $result;
    }
}
