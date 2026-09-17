<?php

namespace App\Http\Middleware;

use App\Classes\NombreArchivoPdf;
use App\Http\Services\Caja\CajaMovimiento\CajaMovimientoManager;
use App\Http\Services\Caja\CajaMovimiento\CajaMovimientoService;
use App\Ventas\Cotizacion;
use App\Ventas\Documento\Documento;
use App\Ventas\Guia;
use App\Ventas\Nota;
use App\Ventas\Pedido;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Pone el nombre del archivo al final de la URL de los PDF de documentos.
 *
 * El visor de Adobe Acrobat en Chrome ignora el Content-Disposition y sugiere
 * al guardar el último tramo de la URL. Las pantallas que ya saben el nombre
 * lo ponen ellas; para las que abren la URL corta (los .vue de ventas y los
 * enlaces posteriores a registrar un documento) se resuelve aquí: la primera
 * petición sólo carga lo justo para armar el nombre y responde un 302 a la
 * misma URL con el nombre al final.
 *
 * Si la petición ya trae nombre se deja pasar tal cual, así que no hay bucle
 * y el PDF se genera una sola vez. Si el nombre no se puede armar, también se
 * deja pasar: nunca se rompe la descarga.
 */
class NombreArchivoPdfEnUrl
{
    public function handle(Request $request, Closure $next)
    {
        $nombre = $this->nombreParaRedirigir($request);

        if ($nombre === null) {
            return $next($request);
        }

        $destino = rtrim($request->url(), '/') . '/' . $nombre . '.pdf';
        $query   = $request->getQueryString();

        return redirect($query === null ? $destino : $destino . '?' . $query, 302);
    }

    /**
     * Devuelve el nombre con el que hay que redirigir, o null para dejar pasar
     * la petición sin tocarla.
     */
    private function nombreParaRedirigir(Request $request)
    {
        $ruta = $request->route();

        if (!$ruta || !$request->isMethod('GET')) {
            return null;
        }

        // Sólo rutas preparadas para llevar el nombre, y sólo si aún no lo trae.
        if (strpos($ruta->uri(), '{nombre?}') === false || $ruta->parameter('nombre')) {
            return null;
        }

        try {
            $nombre = $this->resolver($request, $ruta);
        } catch (\Throwable $e) {
            return null; // Ante cualquier problema, que el PDF salga como siempre.
        }

        return $nombre === '' ? null : $nombre;
    }

    /** Carga lo mínimo de cada documento para poder nombrarlo. */
    private function resolver(Request $request, $ruta)
    {
        $id = $ruta->parameter('id');

        switch ($ruta->getName()) {
            case 'Caja.reporte.movimiento':
                return $this->reporteCaja($id, CajaMovimientoService::REPORTE_DINERO);

            case 'Caja.reporte.productos':
                return $this->reporteCaja($id, CajaMovimientoService::REPORTE_CANTIDADES);

            case 'ventas.documento.comprobante':
                $documento = Documento::find($id);
                return $documento ? NombreArchivoPdf::documentoVenta($documento) : null;

            case 'ventas.notas.show':
                $nota = Nota::find($id);
                return $nota ? NombreArchivoPdf::notaElectronica($nota) : null;

            case 'ventas.guiasremision.show':
                $guia = Guia::find($id);
                return $guia ? NombreArchivoPdf::guia($guia) : null;

            case 'ventas.cotizacion.reporte':
                $cotizacion = Cotizacion::find($id);
                return $cotizacion
                    ? NombreArchivoPdf::cotizacion($cotizacion, optional($cotizacion->cliente)->documento)
                    : null;

            case 'pedidos.pedido.reporte':
                $pedido = Pedido::find($id);
                return $pedido
                    ? NombreArchivoPdf::pedido($pedido, optional($pedido->cliente)->documento)
                    : null;

            case 'consultarComprobante.pdf':
                return $this->comprobanteConsultado($request);
        }

        return null;
    }

    private function reporteCaja($id, $tipo)
    {
        if (!$id) {
            return null;
        }

        return (new CajaMovimientoManager())->nombreArchivoReporte((int) $id, $tipo);
    }

    /**
     * El buscador público identifica el comprobante por sus datos, no por id.
     * Aquí se repite esa búsqueda pidiendo sólo las columnas del nombre, para
     * no cargar el documento entero en la petición que únicamente redirige.
     */
    private function comprobanteConsultado(Request $request)
    {
        $tipoDoc = $request->get('tipo_doc');

        // La rama de notas (07) de este buscador consulta una columna que la
        // tabla no tiene; se deja intacta y sin redirección.
        if ($tipoDoc === null || $tipoDoc === '07') {
            return null;
        }

        foreach (['fecha_emision', 'serie', 'correlativo', 'doc_cliente', 'monto_total'] as $campo) {
            if ($request->get($campo) === null) {
                return null;
            }
        }

        $fila = DB::table('cotizacion_documento')
            ->select(
                'tipo_venta_codigo',
                'serie',
                'correlativo',
                'documento_cliente',
                'contingencia',
                'serie_contingencia',
                'correlativo_contingencia'
            )
            ->where('tipo_venta_codigo', $tipoDoc)
            ->whereDate('created_at', $request->get('fecha_emision'))
            ->where('serie', $request->get('serie'))
            ->where('correlativo', $request->get('correlativo'))
            ->where('documento_cliente', $request->get('doc_cliente'))
            ->whereRaw('ROUND(total_pagar, 2) = ?', [$request->get('monto_total')])
            ->first();

        return $fila ? NombreArchivoPdf::documentoVenta($fila) : null;
    }
}
