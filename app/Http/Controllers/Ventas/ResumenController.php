<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Ventas\Resumen;
use App\Ventas\DetalleResumen;

use App\Greenter\Utils\Util;
use App\Ventas\Documento\Documento;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Ws\Services\SunatEndpoints;
use DateTime;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Auth;
use Throwable;


class ResumenController extends Controller
{
    //====== PERMISOS DE LA PANTALLA ======
    const PERMISO_RESUMENES   = 'venta.resumenes.index';
    const PERMISO_TODAS_SEDES = 'venta.resumenes.todas_sedes';

    //====== tabladetalles.id DE LA BOLETA DE VENTA ELECTRÓNICA ======
    const TIPO_VENTA_BOLETA = '128';

    //====== CUÁNTOS DÍAS HACIA ATRÁS MIRA EL BUSCADOR DE PENDIENTES ======
    const DIAS_PENDIENTES = 30;

    /**
     * El envío nocturno (comando resumenes:send) entra sin sesión. Todas las rutas
     * de la pantalla están detrás del middleware 'auth', así que si no hay usuario
     * la llamada viene de consola y no hay nada que autorizar.
     */
    private function esLlamadaDeConsola()
    {
        return !Auth::check();
    }

    private function autorizarResumenes()
    {
        if ($this->esLlamadaDeConsola()) {
            return;
        }

        $this->authorize('haveaccess', self::PERMISO_RESUMENES);
    }

    private function puedeTodasLasSedes()
    {
        return !$this->esLlamadaDeConsola()
            && Auth::user()->havePermission(self::PERMISO_TODAS_SEDES);
    }

    /**
     * Resuelve con qué sede se trabaja. Sin el permiso de todas las sedes, un
     * sede_id ajeno en la petición es un 403 y no un silencio: hasta ahora bastaba
     * cambiar el número de la URL para leer los pendientes de la sede de al lado.
     */
    private function sedeAutorizada($sede_id)
    {
        if ($this->esLlamadaDeConsola()) {
            return (int) $sede_id;
        }

        $propia = (int) Auth::user()->sede_id;

        if ($sede_id === null || $sede_id === '') {
            return $propia;
        }

        $pedida = (int) $sede_id;

        if ($pedida !== $propia && !$this->puedeTodasLasSedes()) {
            abort(403, 'NO TIENE PERMISO PARA OPERAR CON RESÚMENES DE OTRA SEDE');
        }

        return $pedida;
    }

    /**
     * Las sedes que el usuario puede elegir. Sin el permiso, sólo la suya, para que
     * el combo nunca ofrezca algo que el servidor va a rechazar.
     */
    private function sedesDisponibles()
    {
        $sedes = DB::table('empresa_sedes')
            ->select('id', 'nombre')
            ->where('estado', 'ACTIVO');

        if (!$this->puedeTodasLasSedes()) {
            $sedes->where('id', Auth::user()->sede_id);
        }

        return $sedes->orderBy('nombre')->get();
    }

    /**
     * Comprueba que un resumen ya grabado pertenece a una sede permitida.
     */
    private function autorizarResumenPorId($resumen_id)
    {
        if ($this->esLlamadaDeConsola() || !$resumen_id) {
            return;
        }

        $resumen = Resumen::find($resumen_id);

        if ($resumen) {
            $this->sedeAutorizada($resumen->sede_id);
        }
    }

    public function index(){

        $this->autorizarResumenes();

        $sede_id            =   Auth::user()->sede_id;
        $sedes              =   $this->sedesDisponibles();
        $puede_todas_sedes  =   $this->puedeTodasLasSedes();

        return view('ventas.resumenes.index',compact('sede_id','sedes','puede_todas_sedes'));
    }

    public function getResumenes(Request $request){

        $this->autorizarResumenes();

        $resumenes  =   DB::table('resumenes as r')
                        ->leftJoin('empresa_sedes as s', 's.id', '=', 'r.sede_id')
                        ->select('r.*', 's.nombre as sede_nombre');

        //======== SIN EL PERMISO DE TODAS LAS SEDES, SÓLO LA PROPIA =====
        if(!$this->puedeTodasLasSedes()){
            $resumenes->where('r.sede_id', Auth::user()->sede_id);
        }

        //======== FILTRO OPCIONAL DEL COMBO DE SEDE DEL LISTADO =====
        if($request->filled('sede_id')){
            $resumenes->where('r.sede_id', $this->sedeAutorizada($request->get('sede_id')));
        }

        return DataTables::of($resumenes)->toJson();
    }


    /**
     * La serie de boleta activa de una sede, o null si no tiene ninguna.
     */
    protected function serieBoleta($sede_id)
    {
        $series = DB::select('SELECT
                    enf.*
                    from empresa_numeracion_facturaciones as enf
                    inner join tabladetalles as td on td.id = enf.tipo_comprobante
                    where
                    enf.sede_id = ?
                    AND td.parametro = "B"
                    AND td.tabla_id = 21
                    AND enf.estado = "ACTIVO"',
                    [$sede_id]);

        return count($series) === 0 ? null : $series[0];
    }

    /**
     * Las boletas que todavía no están en ningún resumen activo.
     *
     * Un único sitio para los filtros, porque de aquí comen la pantalla y el
     * envío nocturno: lo que se excluya aquí queda excluido en los dos.
     *
     * cd.estado <> 'ANULADO' es el filtro que faltaba. En el sistema de
     * referencia, una boleta anulada a las 07:47 entró esa misma noche en el
     * resumen automático de las 23:40 y se declaró como válida; SUNAT la
     * aceptó y ahí sigue.
     */
    protected function comprobantesPendientes($serie, $fechaDesde, $fechaHasta)
    {
        return DB::select('SELECT
                    cd.id as documento_id,
                    cd.serie as documento_serie,
                    cd.correlativo as documento_correlativo,
                    cd.fecha_documento as documento_fecha,
                    cd.estado as documento_estado,
                    cd.tipo_venta_id as documento_tipo_venta_id,
                    td.parametro as documento_moneda,
                    cd.total_pagar as documento_total,
                    cd.total_igv as documento_igv,
                    cd.total as documento_subtotal,
                    cd.documento_cliente as documento_doc_cliente
                    FROM cotizacion_documento as cd
                    INNER JOIN tabladetalles as td on td.id = cd.moneda
                    WHERE
                    cd.fecha_documento >= ?
                    AND cd.fecha_documento <= ?
                    AND cd.anticipo_consumido_id IS NULL
                    AND cd.serie = ?
                    AND sunat = "0"
                    AND cd.estado <> "ANULADO"
                    AND td.tabla_id = 1
                    AND cd.id NOT IN (
                        SELECT
                            rd.documento_id
                        FROM
                            resumenes_detalles AS rd
                        WHERE rd.estado = "ACTIVO"
                    )
                    ORDER BY cd.fecha_documento, cd.correlativo',
                    [$fechaDesde, $fechaHasta, $serie]);
    }

    public function getComprobantes($fechaComprobantes,$sede_id){

        $this->autorizarResumenes();
        $sede_id    =   $this->sedeAutorizada($sede_id);

        $enf        =   $this->serieBoleta($sede_id);

        //====== SIN SERIE DE BOLETA NO HAY NADA QUE RESUMIR. SE AVISA EN VEZ DE
        //====== REVENTAR CON "Undefined offset: 0" AL INDEXAR UN RESULTADO VACÍO.
        //====== 'success' SIGUE SIENDO EL ARRAY DE COMPROBANTES: EL COMANDO Y EL JS
        //====== LO RECORREN TAL CUAL, ASÍ QUE AQUÍ VA VACÍO, NO false.
        if(!$enf){
            return response()->json([
                'success' => [],
                'fecha'   => $fechaComprobantes,
                'message' => 'LA SEDE NO TIENE UNA SERIE DE BOLETA ELECTRÓNICA ACTIVA. '
                           . 'CONFIGÚRELA EN MANTENIMIENTO → NUMERACIÓN DE COMPROBANTES.'
            ]);
        }

        //======= LAS PENDIENTES DE ESA FECHA, SIN ANULADAS ======
        $comprobantes   =   $this->comprobantesPendientes($enf->serie, $fechaComprobantes, $fechaComprobantes);

        return response()->json(['success'=>$comprobantes ,
        'fecha' => $fechaComprobantes]);

    }

/*
array:3 [
  "comprobantes"        => "[{"documento_id":8143,"documento_serie":"B001","documento_correlativo":4988,"documento_moneda":"PEN","documento_total":"70.00","documento_igv":"10.68","documento_subtotal":"59.32","documento_doc_cliente":71535599},{"documento_id":8144,"documento_serie":"B001","documento_correlativo":4989,"documento_moneda":"PEN","documento_total":"55.00","documento_igv":"8.39","documento_subtotal":"46.61","documento_doc_cliente":3373564},{"documento_id":8145,"documento_serie":"B001","documento_correlativo":4990,"documento_moneda":"PEN","documento_total":"178.00","documento_igv":"27.15","documento_subtotal":"150.85","documento_doc_cliente":41508474},{"documento_id":8149,"documento_serie":"B001","documento_correlativo":4991,"documento_moneda":"PEN","documento_total":"71.00","documento_igv":"10.83","documento_subtotal":"60.17","documento_doc_cliente":72794677},{"documento_id":8157,"documento_serie":"B001","documento_correlativo":4993,"documento_moneda":"PEN","documento_total":"175.00","documento_igv":"26.69","documento_subtotal":"148.31","documento_doc_cliente":43819263}]"
  "fecha_comprobantes"  => "2025-02-28"
  "sede_id"             => 1
]
*/
    /**
     * Todas las boletas pendientes de una sede, AGRUPADAS POR FECHA DE EMISIÓN.
     *
     * SUNAT no admite varias fechas en un mismo resumen: <cbc:ReferenceDate> es
     * un elemento de cabecera del RC y las líneas del detalle no llevan fecha.
     * Por eso se devuelve un grupo por fecha y de cada grupo sale un resumen
     * distinto; la pantalla lo dice antes de enviar nada.
     */
    public function getPendientes($sede_id, $dias = null)
    {
        $this->autorizarResumenes();
        $sede_id    =   $this->sedeAutorizada($sede_id);

        $enf        =   $this->serieBoleta($sede_id);

        if(!$enf){
            return response()->json([
                'success'    => false,
                'grupos'     => [],
                'anteriores' => 0,
                'message'    => 'LA SEDE NO TIENE UNA SERIE DE BOLETA ELECTRÓNICA ACTIVA. '
                              . 'CONFIGÚRELA EN MANTENIMIENTO → NUMERACIÓN DE COMPROBANTES.'
            ]);
        }

        //====== LA SERIE R SE COMPRUEBA AQUÍ, ANTES DE DEJAR CONTINUAR, Y NO AL
        //====== GUARDAR: ASÍ EL USUARIO NO LLEGA A ARMAR UN RESUMEN QUE NO PODRÁ
        //====== NUMERARSE. getCorrelativo() LANZA SI LA SEDE NO TIENE SERIE R.
        try {
            $correlativo    =   $this->getCorrelativo($sede_id);
        } catch (Throwable $th) {
            return response()->json([
                'success'    => false,
                'grupos'     => [],
                'anteriores' => 0,
                'message'    => $th->getMessage()
            ]);
        }

        $dias   =   ($dias !== null && (int) $dias > 0) ? (int) $dias : self::DIAS_PENDIENTES;
        $hasta  =   date('Y-m-d');
        $desde  =   date('Y-m-d', strtotime('-'.($dias - 1).' days'));

        $comprobantes   =   $this->comprobantesPendientes($enf->serie, $desde, $hasta);

        //====== UN GRUPO POR FECHA = UN RESUMEN POR FECHA ======
        $grupos =   [];
        foreach($comprobantes as $c){
            $fecha  =   substr($c->documento_fecha, 0, 10);

            if(!isset($grupos[$fecha])){
                $grupos[$fecha] = [
                    'fecha'        => $fecha,
                    'cantidad'     => 0,
                    'total'        => 0,
                    'comprobantes' => []
                ];
            }

            $grupos[$fecha]['cantidad']++;
            $grupos[$fecha]['total']          +=  (float) $c->documento_total;
            $grupos[$fecha]['comprobantes'][]  =  $c;
        }
        ksort($grupos);

        //====== LO ANTERIOR AL TOPE NO SE OCULTA: SE CUENTA Y SE AVISA ======
        $anteriores =   $this->comprobantesPendientes(
                            $enf->serie, '1900-01-01', date('Y-m-d', strtotime($desde.' -1 day')));

        return response()->json([
            'success'               => true,
            'serie'                 => $enf->serie,
            'serie_resumen'         => $correlativo->serie,
            'correlativo_siguiente' => $correlativo->correlativo,
            'dias'                  => $dias,
            'desde'                 => $desde,
            'hasta'                 => $hasta,
            'grupos'                => array_values($grupos),
            'anteriores'            => count($anteriores)
        ]);
    }

    /**
     * Vuelve a comprobar contra la base cada documento que llega en la petición.
     *
     * El bloqueo del modal es una cortesía para el usuario; la garantía está
     * aquí. Se recalcula la lista de pendientes de esa sede y esa fecha y se
     * exige que todo lo recibido esté dentro: si algo se anuló entre la búsqueda
     * y el guardado, o si alguien manipuló el envío, no se graba nada.
     *
     * @throws Exception
     */
    protected function validarComprobantes($comprobantes, $sede_id, $fecha_comprobantes)
    {
        if (!is_array($comprobantes) || count($comprobantes) === 0) {
            throw new Exception('NO HAY COMPROBANTES EN EL RESUMEN.');
        }

        $enf = $this->serieBoleta($sede_id);

        if (!$enf) {
            throw new Exception('LA SEDE NO TIENE UNA SERIE DE BOLETA ELECTRÓNICA ACTIVA.');
        }

        $admisibles = [];
        foreach ($this->comprobantesPendientes($enf->serie, $fecha_comprobantes, $fecha_comprobantes) as $p) {
            $admisibles[(int) $p->documento_id] = true;
        }

        $rechazados = [];
        foreach ($comprobantes as $c) {
            $id = isset($c->documento_id) ? (int) $c->documento_id : 0;

            if (!isset($admisibles[$id])) {
                $rechazados[] = $id;
            }
        }

        if (count($rechazados) === 0) {
            return;
        }

        //====== SE DICE CUÁL Y POR QUÉ, QUE ES LO QUE FALTABA EN LA PANTALLA ======
        $detalle = [];
        foreach (DB::table('cotizacion_documento')
                    ->whereIn('id', $rechazados)
                    ->get(['id', 'serie', 'correlativo', 'estado', 'sunat']) as $d) {
            $detalle[] = $d->serie.'-'.$d->correlativo
                       . ' ('.($d->estado === 'ANULADO' ? 'ANULADA'
                              : ($d->sunat !== '0' ? 'YA ENVIADA A SUNAT' : 'NO ADMISIBLE')).')';
        }

        if (count($detalle) === 0) {
            $detalle[] = 'ID '.implode(', ', $rechazados);
        }

        throw new Exception('EL RESUMEN NO SE GRABÓ PORQUE ESTOS COMPROBANTES NO PUEDEN '
            .'DECLARARSE: '.implode('; ', $detalle).'. VUELVA A BUSCAR LOS PENDIENTES.');
    }

    public function store(Request $request){

        //====== FUERA DEL try: UN 403 NO PUEDE ACABAR CONVERTIDO EN JSON 200 ======
        $this->autorizarResumenes();
        $request->merge(['sede_id' => $this->sedeAutorizada($request->get('sede_id'))]);

        try {

            //===== INICIAR TRANSACCIÓN =====
            DB::beginTransaction();

            //======= VERIFICANDO SI RESUMENES ESTAN ACTIVOS ======

            $resumenIsActive =    $this->isActive($request->get('sede_id'))->getData();


            if(!$resumenIsActive->success){
               return $resumenIsActive;
            }

            //====== RECEPCIONANDO COMPROBANTES Y FECHA ======
            $comprobantes       =   json_decode($request->get('comprobantes'));
            $fecha_comprobantes =   $request->get('fecha_comprobantes');

            //====== EL LISTADO LLEGA DESDE EL NAVEGADOR, ASÍ QUE SE VUELVE A
            //====== CONSTRUIR CONTRA LA BASE Y SE EXIGE QUE CADA DOCUMENTO SIGA
            //====== SIENDO ADMISIBLE. UNA SOLA COMPROBACIÓN CUBRE TODAS LAS
            //====== CAUSAS: ANULADA, YA ENVIADA, YA RESUMIDA, DE OTRA FECHA, DE
            //====== OTRA SERIE O QUE NO ES UNA BOLETA.
            $this->validarComprobantes($comprobantes, $request->get('sede_id'), $fecha_comprobantes);

            //===== BUSCANDO CORRELATIVO DEL COMPROBANTE =====
            $datos_correlativo    =   $this->getCorrelativo($request->get('sede_id'));


            //==== GUARDANDO RESUMEN EN LA BD ====
            $resumen                        =   new Resumen();
            $resumen->serie                 =   $datos_correlativo->serie;
            $resumen->correlativo           =   $datos_correlativo->correlativo;
            $resumen->fecha_comprobantes    =   $fecha_comprobantes;
            $resumen->sede_id               =   $request->get('sede_id');
            if(!$request->has("command")){
                $resumen->registrador_id        =   Auth::user()->colaborador_id;
                $resumen->registrador_nombre    =   Auth::user()->usuario;
            }else{
                $resumen->registrador_id        =   1;
                $resumen->registrador_nombre    =   "ENVÍO AUTOMÁTICO";
            }
            $resumen->save();


            //===== GRABANDO DETALLE DEL RESUMEN ====
            foreach ($comprobantes as $comprobante) {
                $detalle_resumen                        =   new DetalleResumen();
                $detalle_resumen->resumen_id            =   $resumen->id;
                $detalle_resumen->documento_id          =   $comprobante->documento_id;
                $detalle_resumen->documento_serie       =   $comprobante->documento_serie;
                $detalle_resumen->documento_correlativo =   $comprobante->documento_correlativo;
                $detalle_resumen->documento_subtotal    =   $comprobante->documento_subtotal;
                $detalle_resumen->documento_igv         =   $comprobante->documento_igv;
                $detalle_resumen->documento_total       =   $comprobante->documento_total;
                $detalle_resumen->documento_doc_cliente =   $comprobante->documento_doc_cliente;
                $detalle_resumen->save();
            }

            //========= ACTUALIZANDO A INICIADO =======
            DB::table('empresa_numeracion_facturaciones')
            ->where('serie', $resumenIsActive->serie)
            ->where('sede_id', $request->get('sede_id'))
            ->where('emision_iniciada', '0')
            ->update([
                'emision_iniciada'  =>  '1',
                'updated_at'        =>  now()
            ]);

            DB::commit();


        } catch (Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }

        //===== ENVIANDO A SUNAT ======
        $request_send   =   new Request();
        $request_send->merge([
            'resumen_id' => $resumen->id
        ]);

        $res            =   $this->sendSunat($request_send);

        $res_decode     =   $res->getData();

        if(!$res_decode->success){
            return response()->json([
                'success'       =>  true,
                'success_sunat' =>  false,
                'message'       =>  'RESÚMEN DE BOLETAS REGISTRADO, PERO NO PUDO ENVIARSE A SUNAT',
                'error'         =>  $res_decode->message,
                'id'            =>  $resumen->id
            ]);
        }else{
            return response()->json([
                'success'       =>  true,
                'success_sunat' =>  true,
                'message'       =>  'RESÚMEN DE BOLETAS REGISTRADO, Y ENVIADO A SUNAT',
                'error'         =>  '',
                'id'            =>  $resumen->id
            ]);
        }

    }

    public function isActive($sede_id){

        //====== FUERA DEL try: UN 403 NO PUEDE ACABAR CONVERTIDO EN JSON 200 ======
        $this->autorizarResumenes();
        $sede_id    =   $this->sedeAutorizada($sede_id);

        try {


            //===== 186 EN PRODUCCION - 190 EN LOCALHOST =====
            $resumenActive  =   DB::table('empresa_numeracion_facturaciones as enf')
                                ->select('enf.serie')
                                ->join('tabladetalles as td', 'td.id', '=', 'enf.tipo_comprobante')
                                ->where('td.parametro', "R")
                                ->where('td.tabla_id', 21)
                                ->where('enf.sede_id',$sede_id)
                                ->where('enf.estado','ACTIVO')
                                ->get();

            if(count($resumenActive) === 0){
                throw new Exception("RESÚMENES NO ESTÁ ACTIVO EN LA SEDE!!!");
            }

            return response()->json([
            'success'   =>  true,
            'message'   =>  "RESÚMENES ACTIVO EN LA SEDE COMO: ".$resumenActive[0]->serie,
            'serie'     =>  $resumenActive[0]->serie]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }


    }

    //====== AYUDANTES INTERNOS: SIN RUTA Y FUERA DEL ALCANCE HTTP, ASÍ QUE LA
    //====== AUTORIZACIÓN LA HACE SIEMPRE EL MÉTODO PÚBLICO QUE LOS LLAMA.
    protected function getCorrelativo($sede_id){


        //===== EL ÚLTIMO RESUMEN DE LA SEDE, POR CORRELATIVO Y NO POR FECHA.
        //===== latest() ordena por created_at, que tiene resolución de SEGUNDO:
        //===== generando varios resúmenes seguidos (uno por fecha pendiente) los
        //===== empates se resolvían al azar y se repetía el correlativo.
        $ultimoResumen  =   DB::table('resumenes')
                            ->where('sede_id',$sede_id)
                            ->orderByDesc('correlativo')
                            ->orderByDesc('id')
                            ->first();

        //======== EN CASO YA EXISTAN RESÚMENES GENERADOS =====
        if($ultimoResumen){
            $correlativo    =   $ultimoResumen->correlativo + 1;
            return (object)['serie'=>$ultimoResumen->serie,'correlativo'=>$correlativo];
        }

        //===== BUSCAMOS EL REGISTRO DEL COMPROBANTE RESÚMENES =======
        //===== 186 EN PRODUCCION - 190 EN LOCALHOST =====
        $series =    DB::select('select
                            enf.numero_iniciar,
                            enf.serie
                            from empresa_numeracion_facturaciones as enf
                            inner join tabladetalles as td on td.id = enf.tipo_comprobante
                            where
                            td.parametro = "R"
                            AND td.tabla_id = 21
                            AND enf.estado = "ACTIVO"
                            AND enf.sede_id = ?',
                            [$sede_id]);

        //====== AVISO LEGIBLE EN VEZ DE "Undefined offset: 0": EL catch DE store()
        //====== DEVUELVE EL MENSAJE AL USUARIO Y DESHACE LA TRANSACCIÓN.
        if(count($series) === 0){
            throw new Exception('LA SEDE NO TIENE UNA SERIE DE RESUMEN (R) ACTIVA. '
                .'CONFIGÚRELA EN MANTENIMIENTO → NUMERACIÓN DE COMPROBANTES ANTES DE CONTINUAR.');
        }

        $enf    =    $series[0];

        return (object)['serie'=>$enf->serie,'correlativo'=>$enf->numero_iniciar];
    }


/*
array:1 [
  "resumen_id" => 6
]
*/

//========= SUMMARY RESULT =====
/*
Greenter\Model\Response\SummaryResult {#3127 // app\Http\Controllers\Market\Ventas\ResumenController.php:306
  #success: true
  #error: null
  #ticket: "1740176422672"
}
*/
    /**
     * El código de tipo de documento que se declara para una línea del resumen.
     *
     * Sale de cotizacion_documento.tipo_venta_id, no de un literal. Hasta ahora
     * era '03' fijo y sólo era correcto de rebote, porque la consulta miraba una
     * tabla y una serie; cualquier cambio en ella habría declarado el documento
     * equivocado sin avisar.
     *
     * Si algo que no es una boleta llega hasta aquí se aborta el envío ENTERO:
     * más vale no declarar nada que declarar mal una línea.
     *
     * @throws Exception
     */
    protected function tipoDocumentoDeclarable($comprobante)
    {
        if ((string) $comprobante->documento_tipo_venta_id === self::TIPO_VENTA_BOLETA) {
            return '03';
        }

        throw new Exception('EL RESUMEN CONTIENE UN DOCUMENTO QUE NO ES UNA BOLETA: '
            .$comprobante->documento_serie.'-'.$comprobante->documento_correlativo
            .' (tipo_venta_id '.$comprobante->documento_tipo_venta_id.'). '
            .'NO SE ENVÍA NADA.');
    }

    /**
     * El estado con el que se declara la línea (catálogo 19 de SUNAT).
     *
     * Sólo se emite '1' (adición), y sólo para un documento ACTIVO. Los estados
     * '2' (modificación) y '3' (anulación) exigen que el comprobante se haya
     * informado antes, y este sistema no guarda en qué resumen se informó cada
     * boleta de forma fiable, así que emitirlos a ciegas sería peor que no
     * emitirlos.
     *
     * Una boleta anulada ya no debería llegar aquí: la filtra
     * comprobantesPendientes() y la rechaza validarComprobantes(). Si aun así
     * aparece — porque se anuló entre el guardado y el envío — se aborta en vez
     * de declararla como alta, que es exactamente lo que pasó en el sistema de
     * referencia con la boleta B001-6968.
     *
     * @throws Exception
     */
    protected function estadoDeclarable($comprobante)
    {
        if ($comprobante->documento_estado === 'ACTIVO') {
            return '1';
        }

        throw new Exception('EL RESUMEN CONTIENE LA BOLETA '
            .$comprobante->documento_serie.'-'.$comprobante->documento_correlativo
            .', QUE ESTÁ '.$comprobante->documento_estado.'. NO SE ENVÍA NADA: '
            .'ELIMINE EL RESUMEN Y VUELVA A GENERARLO.');
    }

    public function sendSunat(Request $request){

        //====== FUERA DEL try: UN 403 NO PUEDE ACABAR CONVERTIDO EN JSON 200 ======
        $this->autorizarResumenes();
        $this->autorizarResumenPorId($request->get('resumen_id'));

        try {

            if(!$request->get('resumen_id')){
                throw new Exception("NO EXISTE EL RESUMEN ID EN LA PETICIÓN ENVIAR A SUNAT!!!");
            }

            //====== COMPROBANDO SI EXISTE EL RESUMEN ======
            $resumen   =   Resumen::find($request->get('resumen_id'));
            if(!$resumen){
                throw new Exception("EL RESUMEN NO EXISTE EN LA BD!!!");
            }

            //======= COMPROBANDO QUE NO SE HAYA ENVIADO ANTES =======
            if($resumen->send_sunat == '1'){
                throw new Exception("ESTE RESUMEN YA FUE ENVIADO A SUNAT!!!");
            }

            //===== OBTENIENDO COMPROBANTES, CON EL TIPO Y EL ESTADO DEL DOCUMENTO =====
            //===== EL JOIN ES LO QUE PERMITE QUE tipoDoc SALGA DEL DATO Y NO DE UN
            //===== LITERAL: resumenes_detalles NO GUARDA EL TIPO DE COMPROBANTE.
            $comprobantes   =   DB::select('SELECT
                                rd.*,
                                cd.tipo_venta_id AS documento_tipo_venta_id,
                                cd.estado        AS documento_estado
                                FROM resumenes_detalles AS rd
                                INNER JOIN cotizacion_documento AS cd ON cd.id = rd.documento_id
                                WHERE rd.resumen_id = ?',
                                [$request->get('resumen_id')]);

            if(count($comprobantes) === 0){
                throw new Exception("EL DETALLE DEL RESUMEN ESTÁ VACÍO!!!");
            }


            $util = Util::getInstance();

            //====== CONSTRUYENDO DETALLES =====
            $detalles_send  =   [];
            foreach ($comprobantes as $comprobante) {

                //====== ESTADOS ======
                //====== 1:NUEVO ==== "2:MODIFICAR" ======= 3:ANULAR =====
                //====== PARA 2,3 DEBE ESTAR PREVIAMENTE INFORMADO EL COMPROBANTE ======
                $detalle = new SummaryDetail();
                $detalle->setTipoDoc($this->tipoDocumentoDeclarable($comprobante))
                    ->setSerieNro($comprobante->documento_serie.'-'.$comprobante->documento_correlativo)
                    ->setEstado($this->estadoDeclarable($comprobante))
                    ->setClienteTipo('1')
                    ->setClienteNro($comprobante->documento_doc_cliente)
                    ->setTotal($comprobante->documento_total)
                    ->setMtoOperGravadas($comprobante->documento_subtotal)
                    ->setMtoOperInafectas(0)
                    ->setMtoOperExoneradas(0)
                    ->setMtoOperExportacion(0)
                    ->setMtoOtrosCargos(0)
                    ->setMtoIGV($comprobante->documento_igv);

                $detalles_send[]    =   $detalle;
            }

            //====== CONSTRUYENDO RESUMEN ====
            $sum = new Summary();
            // FECHA GENERACIÓN MENOR QUE FECHA RESUMEN
            $sum->setFecGeneracion(new DateTime($resumen->fecha_comprobantes))
                    ->setFecResumen(new DateTime($resumen->fecha_comprobantes))
                    ->setCorrelativo($resumen->correlativo)
                    ->setCompany($util->shared->getCompany($resumen->sede_id))
                    ->setDetails($detalles_send);

            //====== GUARDANDO NAME DEL SUMMARY =======
            $resumen->summary_name  =   $sum->getName();
            $resumen->update();

            $see    =   $this->controlConfiguracionGreenter($util);


            $res = $see->send($sum);

            //==== GUARDANDO XML ====
            $util->writeXml($sum, $see->getFactory()->getLastXml(),"RESUMEN",null);
            //$resumen->ruta_xml    =    __DIR__.'/../../../Greenter/files/resumenes_xml/'.$sum->getName().'.xml';
            $resumen->ruta_xml      =   'storage/greenter/resumenes/xml/'.$sum->getName().'.xml';

            //==== VERIFICANDO SI SE ENVIÓ A SUNAT ====
            $envioSunat     =   $res->isSuccess();
            $message_envio  =   '';
            $ticket         =   null;

            if($envioSunat){

                $message_envio          =   "ENVIADO A SUNAT";
                $ticket                 =   $res->getTicket();
                $resumen->send_sunat    =   1;
                $resumen->ticket        =   $ticket;
                $resumen->regularize    =   0;

                //======= ACTUALIZANDO DOCUMENTOS DE VENTA COMO ENVIADOS A SUNAT =========
                $detalles   =   DB::select('select
                                rd.*
                                from resumenes_detalles as rd
                                where rd.resumen_id = ?',[$resumen->id]);

                foreach ($detalles as $detalle) {
                    DB::table('cotizacion_documento')
                    ->where('id',$detalle->documento_id)
                    ->update([
                        'resumen_id'        =>  $resumen->id,
                        'sunat'             =>  '1',
                        'cdr_response_code' =>  'EN ESPERA'
                    ]);
                }

            }else{
                $message_envio          =   "OCURRIO UN ERROR EN EL ENVÍO A SUNAT";
                $resumen->send_sunat    =   0;
                $resumen->regularize    =   1;

                $exception  =   '';
                //===== OBTENIENDO ERRORES =====
                if($res->getError()){
                    $error                  =   'CODE: '.$res->getError()->getCode().' - '.'MESSAGE: '.$res->getError()->getMessage();
                    $resumen->response_error=   $error;
                    $exception              =   $error;
                }

                throw new Exception($exception);


            }

            $resumen->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ENVIADO A SUNAT CON ÉXITO!!!',
            'id'=>$request->get('resumen_id')]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success'   =>  false,
                'message'   =>  $th->getMessage(),
                'id'        =>  $request->get('resumen_id'),
                'line'      =>  $th->getLine(),
                'th'        =>  $th->getFile()]);
        }
    }


    protected function controlConfiguracionGreenter($util){
        //==== OBTENIENDO CONFIGURACIÓN DE GREENTER ======
        $greenter_config    =   DB::select('select
                                gc.ruta_certificado,
                                gc.id_api_guia_remision,
                                gc.modo,
                                gc.clave_api_guia_remision,
                                e.ruc,e.razon_social,
                                e.direccion_fiscal,
                                e.ubigeo,
                                e.direccion_llegada,
                                gc.sol_user,gc.sol_pass
                                from greenter_config as gc
                                inner join empresas as e on e.id = gc.empresa_id
                                inner join configuracion as c on c.propiedad = gc.modo
                                where gc.empresa_id=1 and c.slug="AG"');


        if(count($greenter_config) === 0){
            throw new Exception('NO SE ENCONTRÓ NINGUNA CONFIGURACIÓN PARA GREENTER');
        }

        if(!$greenter_config[0]->sol_user){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_USER');
        }
        if(!$greenter_config[0]->sol_pass){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_PASS');
        }
        if ($greenter_config[0]->modo !== "BETA" && $greenter_config[0]->modo !== "PRODUCCION") {
            throw new Exception('NO SE HA CONFIGURADO EL AMBIENTE BETA O PRODUCCIÓN PARA GREENTER');
        }

        $see    =   null;
        if($greenter_config[0]->modo === "BETA"){
            //===== MODO BETA ======
            $see = $util->getSee(SunatEndpoints::FE_BETA,$greenter_config[0]);
        }

        if($greenter_config[0]->modo === "PRODUCCION"){
            //===== MODO PRODUCCION ======
            $see = $util->getSee(SunatEndpoints::FE_PRODUCCION,$greenter_config[0]);
        }

        if(!$see){
            throw new Exception('ERROR EN LA CONFIGURACIÓN DE GREENTER, SEE ES NULO');
        }

        return $see;
    }


/*
array:1 [
  "resumen_id" => "340"
]
*/
    public function consultarTicket(Request $request){

        //====== FUERA DEL try: UN 403 NO PUEDE ACABAR CONVERTIDO EN JSON 200 ======
        $this->autorizarResumenes();
        $this->autorizarResumenPorId($request->get('resumen_id'));

        try {
            DB::beginTransaction();

            $message        =   "";

            $resumen_id     =   $request->get('resumen_id');

            $resumen        =   Resumen::findOrFail($resumen_id);
            $ticket         =   $resumen->ticket;
            $summary_name   =   $resumen->summary_name;

            $util = Util::getInstance();

            //===== INICIAR ENDPOINTS SUNAT ====
            //$see = $util->getSee(SunatEndpoints::FE_BETA);
            //$see = $util->getSee(SunatEndpoints::FE_PRODUCCION);

            $see            =   $this->controlConfiguracionGreenter($util);

            $res_ticket     =   $see->getStatus($ticket);

            $code_estado    =   $res_ticket->getCode();
            $cdr            =   $res_ticket->getCdrResponse();

            //========= GUARDANDO STATUS RESULT RES =====
            $resumen->code_estado                   =   $res_ticket->getCode();
            $resumen->status_result_success         =   $res_ticket->isSuccess();
            if($res_ticket->getError()){
                $resumen->status_result_error_code      =   $res_ticket->getError()->getCode();
                $resumen->status_result_error_message   =   $res_ticket->getError()->getMessage();
                $resumen->regularize                    =   1;
            }

            //====== ENVIO CORRECTO Y CDR RECIBIDO ======
            if($code_estado == 0){
                //===== GUARDANDO CDR ======
                $util->writeCdr(null, $res_ticket->getCdrZip(), "RESUMEN",$summary_name);
                //$resumen->ruta_cdr    =   __DIR__.'/../../../Greenter/files/resumenes_cdr/'.$summary_name.'.zip';
                $resumen->ruta_cdr      =   'storage/greenter/resumenes/cdr/'.$summary_name.'.zip';

                //==== GUARDANDO DATOS DEL CDR ====
                if($res_ticket->getCdrResponse()){

                    $resumen->cdr_response_id           =   $res_ticket->getCdrResponse()->getId();
                    $resumen->cdr_response_code         =   $res_ticket->getCdrResponse()->getCode();
                    $resumen->cdr_response_description  =   $res_ticket->getCdrResponse()->getDescription();
                    $resumen->cdr_response_notes        =   implode(" | ", $res_ticket->getCdrResponse()->getNotes());

                    DB::table('cotizacion_documento')
                    ->where('resumen_id',$resumen_id)
                    ->update([
                        'cdr_response_code'    =>  $res_ticket->getCdrResponse()->getCode()
                    ]);
                }

                //====== GUARDANDO ESTADO DEL TICKET ====
                $message    =   "ENVÍO CORRECTO Y CDR RECIBIDO";
            }

            //===== ENVÍO CON ERRORES Y CDR RECIBIDO =====
            //===== RECHAZADO POR SUNAT =====
            if($code_estado == 99){

                if($res_ticket->getCdrResponse()){

                    //===== GUARDANDO CDR ======
                    $util->writeCdr(null, $res_ticket->getCdrZip(),"RESUMEN",$summary_name);
                    $resumen->ruta_cdr                  =   'storage/greenter/resumenes/cdr/'.$summary_name.'.zip';

                    //==== GUARDANDO DATOS DEL CDR ====
                    $resumen->cdr_response_id           =   $res_ticket->getCdrResponse()->getId();
                    $resumen->cdr_response_code         =   $res_ticket->getCdrResponse()->getCode();
                    $resumen->cdr_response_description  =   $res_ticket->getCdrResponse()->getDescription();
                    $resumen->cdr_response_notes        =   implode(" | ", $res_ticket->getCdrResponse()->getNotes());

                    //====== ESTE UPDATE ESTABA FUERA DEL if: CON 99 Y SIN CDR LLAMABA
                    //====== getCode() SOBRE null, LANZABA, Y LA CONSULTA ENTERA SE IBA AL
                    //====== catch. POR ESO LA RAMA DE ABAJO NUNCA LLEGABA A EJECUTARSE.
                    DB::table('cotizacion_documento')
                    ->where('resumen_id',$resumen_id)
                    ->update([
                        'cdr_response_code'    =>  $res_ticket->getCdrResponse()->getCode()
                    ]);

                    $message    =   "ENVÍO CON ERRORES Y CDR RECIBIDO";

                }else{

                    //===== RECHAZADO Y SIN CDR: NO SE ESCRIBE ruta_cdr NI SE LLAMA A
                    //===== writeCdr, QUE DEJARÍA UN .zip VACÍO APUNTADO DESDE LA BASE.
                    //===== QUEDA CONSTANCIA DEL RECHAZO EN LA CABECERA Y EN LOS
                    //===== DOCUMENTOS, QUE ES LO QUE ANTES SE PERDÍA.
                    $message    =   "ENVÍO CON ERRORES, SIN CDR";

                    $resumen->cdr_response_code         =   'SIN CDR';
                    $resumen->cdr_response_description  =   'SUNAT RECHAZÓ EL RESUMEN (CÓDIGO 99) '
                                                            .'Y NO DEVOLVIÓ CDR.';

                    DB::table('cotizacion_documento')
                    ->where('resumen_id',$resumen_id)
                    ->update([
                        'cdr_response_code'    =>  'SIN CDR'
                    ]);
                }
            }

            //======= EN PROCESO =======
            if($code_estado == 98){

                $message    =   "ENVÍO EN PROCESO";

                DB::table('cotizacion_documento')
                ->where('resumen_id',$resumen_id)
                ->update([
                    'cdr_response_code'    =>  'EN ESPERA'
                ]);

            }

            //====== RESPUESTA DEFINITIVA: SE BORRA LO QUE YA NO APLICA ======
            //====== Los campos de error los escribe esta misma consulta cuando
            //====== SUNAT contesta 98 o falla la red, y nadie los limpiaba. Al
            //====== llegar una respuesta definitiva quedaban los dos mensajes a
            //====== la vez y el listado pintaba "ACEPTADO" y en rojo "0098 EL
            //====== PROCESAMIENTO AUN NO HA TERMINADO". response_error es del
            //====== envio: si hay CDR, el envio funciono.
            if($code_estado == 0 || $code_estado == 99){
                $resumen->status_result_error_code      =   null;
                $resumen->status_result_error_message   =   null;
                $resumen->response_error                =   null;
                $resumen->regularize                    =   0;
            }

            $resumen->update();

            //======= ARCHIVO YA PRESENTADO ANTERIORMENTE ========
            if($resumen->cdr_response_code == 2223 ){
                //======== MARCAR COMO ENVIADO =======
                $resumen->regularize    =   0;
                $resumen->send_sunat    =   1;
                $resumen->code_estado   =   0;
                $resumen->update();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>$message]);

        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    /**
     * Descarga el XML o el CDR de un resumen.
     *
     * La fila de la base y el archivo en disco pueden no ir juntos: si el
     * archivo se pierde, Response::download lanzaba un 500 con la traza. Ahora
     * se vuelve al listado con un mensaje que dice qué falta y dónde se esperaba.
     */
    protected function descargarArchivoResumen($resumen_id, $campo, $etiqueta)
    {
        $resumen = Resumen::find($resumen_id);

        if (!$resumen) {
            return back()->with('message_error', 'EL RESUMEN SOLICITADO NO EXISTE.');
        }

        $ruta = $resumen->{$campo};

        if (!$ruta) {
            return back()->with('message_error',
                'EL RESUMEN '.$resumen->serie.'-'.$resumen->correlativo.' NO TIENE '.$etiqueta.' GUARDADO. '
                .'SI YA SE ENVIÓ, CONSULTE EL TICKET PARA QUE SUNAT DEVUELVA LA RESPUESTA.');
        }

        if (!is_file($ruta)) {
            return back()->with('message_error',
                'NO SE ENCUENTRA EL ARCHIVO '.$etiqueta.' DEL RESUMEN '
                .$resumen->serie.'-'.$resumen->correlativo.'. SE ESPERABA EN: '.$ruta.'. '
                .'LA FILA EXISTE EN LA BASE PERO EL ARCHIVO NO ESTÁ EN EL SERVIDOR.');
        }

        return Response::download($ruta, basename($ruta), ['Content-Type' => 'text/xml']);
    }

    public function getXml($resumen_id){

        $this->autorizarResumenes();
        $this->autorizarResumenPorId($resumen_id);

        return $this->descargarArchivoResumen($resumen_id, 'ruta_xml', 'XML');
    }

    public function getCdr($resumen_id){

        $this->autorizarResumenes();
        $this->autorizarResumenPorId($resumen_id);

        return $this->descargarArchivoResumen($resumen_id, 'ruta_cdr', 'CDR');
    }


    public function getDetallesResumen($resumen_id){

        //====== FUERA DEL try: UN 403 NO PUEDE ACABAR CONVERTIDO EN JSON 200 ======
        $this->autorizarResumenes();
        $this->autorizarResumenPorId($resumen_id);

        try {
            $resumen_detalle    =   DB::select('select rd.resumen_id, rd.documento_serie,rd.documento_correlativo,
                                    rd.documento_total,rd.documento_igv,rd.documento_subtotal,cd.cliente,
                                    rd.documento_doc_cliente,cd.created_at as fecha
                                    from resumenes_detalles as rd
                                    inner join cotizacion_documento as cd on cd.id=rd.documento_id
                                    where rd.resumen_id=?',[$resumen_id]);

            return response()->json(['success'=>true,'resumen_detalle'=>$resumen_detalle]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>'ERROR AL OBTENER EL DETALLE DEL RESÚMEN',
            'exception'=>$th->getMessage()]);
        }
    }

    /**
     * Deshace un resumen que NUNCA llegó a SUNAT.
     *
     * Un resumen aceptado no se toca: sus boletas ya están declaradas y
     * liberarlas haría que el siguiente envío las declarase por segunda vez.
     * Dejar sin efecto lo ya informado es una comunicación de baja ante SUNAT,
     * otro documento que este sistema no emite; borrar la fila aquí no lo haría.
     *
     * El borrado es lógico en los dos niveles. En el detalle, poner
     * resumenes_detalles.estado = 'ANULADO' basta para liberar los documentos:
     * la consulta de pendientes excluye sólo los detalles 'ACTIVO'. Esa columna
     * existía desde el principio y no la escribía nadie.
     */
    public function destroy(Request $request)
    {
        //====== FUERA DEL try: UN 403 NO PUEDE ACABAR CONVERTIDO EN JSON 200 ======
        $this->autorizarResumenes();
        $this->autorizarResumenPorId($request->get('resumen_id'));

        try {
            DB::beginTransaction();

            $resumen = Resumen::find($request->get('resumen_id'));

            if (!$resumen) {
                throw new Exception('EL RESUMEN NO EXISTE EN LA BD!!!');
            }

            if ($resumen->estado === 'ANULADO') {
                throw new Exception('ESTE RESUMEN YA ESTÁ ANULADO.');
            }

            if ((int) $resumen->send_sunat === 1) {
                throw new Exception('ESTE RESUMEN YA FUE ENVIADO A SUNAT Y NO SE PUEDE ELIMINAR. '
                    .'SUS BOLETAS YA ESTÁN DECLARADAS Y VOLVER A ENVIARLAS SERÍA DECLARARLAS '
                    .'DOS VECES. PARA DEJARLAS SIN EFECTO HAY QUE EMITIR LA COMUNICACIÓN DE '
                    .'BAJA ANTE SUNAT.');
            }

            $documentos = DB::table('resumenes_detalles')
                            ->where('resumen_id', $resumen->id)
                            ->pluck('documento_id')
                            ->toArray();

            DB::table('resumenes_detalles')
                ->where('resumen_id', $resumen->id)
                ->update(['estado' => 'ANULADO', 'updated_at' => now()]);

            //====== LOS DOCUMENTOS VUELVEN A ESTAR PENDIENTES ======
            if (count($documentos) > 0) {
                DB::table('cotizacion_documento')
                    ->whereIn('id', $documentos)
                    ->update([
                        'sunat'             => '0',
                        'resumen_id'        => null,
                        'cdr_response_code' => null,
                        'updated_at'        => now()
                    ]);
            }

            //====== RASTRO EN LA CABECERA. EL CORRELATIVO QUEDA CONSUMIDO: ES MÁS
            //====== SEGURO QUE REUTILIZARLO, PORQUE EL NOMBRE DEL XML DE SUNAT SE
            //====== FORMA CON ÉL.
            $resumen->estado             = 'ANULADO';
            $resumen->anulado_por_id     = Auth::user()->colaborador_id;
            $resumen->anulado_por_nombre = Auth::user()->usuario;
            $resumen->anulado_at         = now();
            $resumen->motivo_anulacion   = substr((string) $request->get('motivo'), 0, 191);
            $resumen->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'RESUMEN '.$resumen->serie.'-'.$resumen->correlativo.' ELIMINADO. SE '
                           .'LIBERARON '.count($documentos).' BOLETA(S) PARA VOLVER A RESUMIRLAS.'
            ]);

        } catch (Throwable $th) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

}
