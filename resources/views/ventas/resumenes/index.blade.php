@extends('layout')
@section('content')
@section('ventas-active', 'active')
@section('resumenes-active', 'active')

@include('ventas.resumenes.modal_add')
@include('ventas.resumenes.modal_detalles')

<style>
    .loader {
     display: inline-block;
     font-size: 48px;
     font-family: Arial, Helvetica, sans-serif;
     font-weight: bold;
     color: #FFF;
     position: relative;
   }
   .loader::before {
     content: '';
     position: absolute;
     left: 34px;
     bottom: 8px;
     width: 30px;
     height: 30px;
     border-radius: 50%;
     border: 5px solid #FFF;
     border-bottom-color: #FF3D00;
     box-sizing: border-box;
     animation: rotation 0.6s linear infinite;
   }

   @keyframes rotation {
     0% {
       transform: rotate(0deg);
     }
     100% {
       transform: rotate(360deg);
     }
   }

   .loader-container {
        position: fixed;
        top: 50%;
        left: 50%;
        width: 100%; /* Ancho completo */
        height: 100%; /* Altura completa */
        transform: translate(-50%, -50%); /* Centrar vertical y horizontalmente */
        background-color: rgba(37, 36, 36, 0.7); /* Fondo semitransparente */
        display: none; /* Ocultar inicialmente */
        justify-content: center;
        align-items: center;
        z-index: 9999; /* Asegurar que esté sobre el modal */
    }
</style>


<div class="loader-container">
    <span class="loader">L &nbsp; ading</span>
</div>

<div class="row wrapper border-bottom white-bg page-heading">
    @csrf
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Resúmenes</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Resúmenes</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button type="button" class="btn btn-block btn-w-m btn-primary m-t-md" onclick="openModalResumenes()">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox ">
                <div class="ibox-content">
                    @if($puede_todas_sedes)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filtro_sede">Sede</label>
                            <select id="filtro_sede" class="form-control">
                                <option value="">TODAS LAS SEDES</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="table-responsive">

                        <table class="table dataTables-gui table-striped table-bordered table-hover"
                        style="text-transform:uppercase" id="table-resumenes" width="100%">
                            <thead>

                                <tr>
                                    <th class="text-center">NRO</th>
                                    <th class="text-center">FEC.EMISIÓN</th>
                                    <th class="text-center">FEC. REFERENCIA</th>
                                    <th class="text-center">IDENTIFICADOR</th>
                                    <th class="text-center">SEDE</th>

                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">TICKET</th>
                                    <th class="text-center">RESPUESTA SUNAT</th>
                                    <th class="text-center">DESCARGAS</th>
                                    <th class="text-center">ACCIONES</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@stop
@push('styles')
<style>
    .swal2-container {
        z-index: 9999 !important;
    }
</style>
@endpush


@push('scripts')


<script>
    const btnGetComprobantes            =   document.querySelector('#btn-get-comprobantes');
    const contenedorGrupos              =   document.querySelector('#grupos_pendientes');
    let tableResumenes  = null;

    let gruposPendientes                =   [];

    //====== LA SEDE CON LA QUE SE OPERA. SIN EL PERMISO EL <select> VA deshabilitado
    //====== Y SIEMPRE DEVUELVE LA PROPIA; EL SERVIDOR LO VUELVE A COMPROBAR. ======
    function sedeSeleccionada(){
        const combo = document.querySelector('#sede_resumen');
        return (combo && combo.value) ? combo.value : @json($sede_id);
    }

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        cargarDataTable();
        loadDataTableDetallesResumen();
    })

    function events(){
        //====== CONSULTAR RESUMEN =======
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('btn-consultar-resumen')){
                const resumen_id    =   e.target.getAttribute('data-resumen-id');

                consultarResumen(resumen_id);
            }
            if(e.target.classList.contains('btn-reenviar-resumen')){
                const resumen_id    =   e.target.getAttribute('data-resumen-id');

                enviarResumen(resumen_id);
            }
            if(e.target.classList.contains('btn-detalle-resumen')){
                const resumen_id    =   e.target.getAttribute('data-resumen-id');
                verDetalleResumen(resumen_id);
            }
            if(e.target.classList.contains('btn-eliminar-resumen')){
                const resumen_id    =   e.target.getAttribute('data-resumen-id');
                confirmarEliminarResumen(resumen_id);
            }
        })

        //======= GUARDAR RESUMEN ======
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('btn-guardar-resumen')){
               const valido   = validaciones();
                if(valido){
                    const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                    });
                    swalWithBootstrapButtons.fire({
                    title: "Desea registrar y enviar un nuevo resúmen de boletas?",
                    text: "Acción no reversible!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí!",
                    cancelButtonText: "No, cancelar!",
                    reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {

                            Swal.fire({
                                title: 'REGISTRANDO RESUMEN Y ENVIANDO A SUNAT...',
                                text: 'Por favor, espere mientras se procesa la solicitud.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            saveSendResumen();

                        } else if (
                            /* Read more about handling dismissals below */
                            result.dismiss === Swal.DismissReason.cancel
                        ) {
                            swalWithBootstrapButtons.fire({
                            title: "Operación cancelada",
                            text: "No se realizaron acciones",
                            icon: "error"
                            });
                        }
                    });
                }
            }
        })

        //===== ELIMINAR COMPROBANTE DEL CARRITO ======
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('btn-delete-document')){
                const documento_id  =   e.target.getAttribute('data-documento-id');
                const indice        =   parseInt(e.target.getAttribute('data-indice'),10);
                eliminarComprobante(indice, documento_id);
                pintarGrupos();
            }
        })

        //====== EL FILTRO DE SEDE RECARGA EL LISTADO ======
        const filtroSede = document.querySelector('#filtro_sede');
        if(filtroSede){
            filtroSede.addEventListener('change', () => {
                if(tableResumenes){ tableResumenes.ajax.reload(); }
            });
        }

        //====== OBTENER COMPROBANTES BOLETAS ======
        //====== CAMBIAR DE SEDE INVALIDA LO YA BUSCADO: SON OTRAS SERIES ======
        const comboSede = document.querySelector('#sede_resumen');
        if(comboSede){
            comboSede.addEventListener('change', async () => {
                gruposPendientes = [];
                pintarGrupos();
                await isActive();
            });
        }

        btnGetComprobantes.addEventListener('click', buscarPendientes);

        //====== MARCAR O DESMARCAR UNA FECHA ENTERA ======
        document.addEventListener('change',(e)=>{
            if(e.target.classList.contains('chk-grupo')){
                const i = parseInt(e.target.getAttribute('data-indice'),10);
                if(gruposPendientes[i]){ gruposPendientes[i].seleccionado = e.target.checked; }
                pintarGrupos();
            }
        })
    }

    //====== REENVIAR RESUMEN ========
     //====== REENVIAR RESUMEN ========
     async function enviarResumen(resumen_id){

        try {
            mostrarAnimacion();

            const url       =   '{{route('ventas.resumenes.enviarSunat')}}';
            const res  =    await axios.post(url,{
                                'resumen_id': resumen_id
                            });

            console.log(res);
            if(res.data.success){
                tableResumenes.ajax.reload();
                toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
            }

        } catch (error) {

            toastr.error(error.response.data, 'ERROR EN LA PETICIÓN ENVIAR RESUMEN A SUNAT', {
                timeOut: 0
            });
        }finally{
            ocultarAnimacion();
        }
    }


    //======== CONSULTAR RESUMEN ======
    async function consultarResumen(resumen_id){

        try {
            mostrarAnimacion();

            const url       =   `/ventas/resumenes/consultar`;
            const res       =   await axios.post(url,{
                resumen_id
            });

            console.log(res);


            if(res.data.success){
                tableResumenes.ajax.reload(null, false);
                toastr.success(res.data.message,'CONSULTA COMPLETADA',{timeOut:0});
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
            }


        } catch (error) {
            console.error('Error al consultar el estado del ticket:', error);
            toastr.error(error,'CONSULTA INCORRECTA');

        }finally{
            ocultarAnimacion();
        }
    }

    //========== ACTUALIZAR DATATABLE ==============
    function actualizarDataTable(resumen_id,res_consulta) {

        const rowIndex = tableResumenes.rows().indexes().filter(function(index) {
            return tableResumenes.row(index).data().id == resumen_id;
        });

        if (rowIndex.length > 0) {
            tableResumenes.row(rowIndex[0]).data(res_consulta).draw();
        } else {
           toastr.error('NO SE ENCONTRÓ LA FILA CON LOS DATOS RESPECTIVOS','RECARGUE LA PÁGINA PARA CORREGIR');
        }
        //tableResumenes.row().data(res_consulta).draw();
    }

    function cargarDataTable(){
        const getResumenesUrl = "{{ route('ventas.resumenes.getResumenes') }}";

        tableResumenes = new DataTable('#table-resumenes',
        {
            processing:true,
            serverSide: true,
            ajax: {
                url: getResumenesUrl,
                type: 'GET',
                data: function(d){
                    const filtro = document.querySelector('#filtro_sede');
                    if(filtro && filtro.value){ d.sede_id = filtro.value; }
                }
            },
            columns: [
                { data: 'id'},
                { data: 'created_at' },
                { data: 'fecha_comprobantes' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return data.serie + '-' + data.correlativo;
                    }
                },
                { data: 'sede_nombre', defaultContent: '-' },
                {
                    data: null,
                    render: function(data, type, row) {

                        //======= ANULADO EN LA PANTALLA: MANDA SOBRE TODO LO DEMÁS ======
                        if(data.estado == 'ANULADO'){
                            return `<span class="badge badge-dark">ELIMINADO</span>`;
                        }

                        //======= ENVIADO A SUNAT ======
                        if(data.send_sunat == 1){
                            //===== ACEPTADO POR SUNAT =====
                            if(data.code_estado == '0' && data.cdr_response_code == '0'){
                                return `<span class="badge badge-success">ACEPTADO</span>`;
                            }

                            //======= RECHAZADO =====
                            if(data.code_estado == '0' && data.cdr_response_code != '0'){
                                return `<span class="badge badge-danger">RECHAZADO</span>`;
                            }

                            //====== RESPUESTA DE SUNAT "ERRORES EN EL ARCHIVO" ====
                            if(data.code_estado == 99){
                                return `<span class="badge badge-danger">ENVIADO CON ERRORES</span>`;
                            }
                            //===== EN PROCESO =====
                            if(data.code_estado == 98){
                                return `<span class="badge badge-warning">EN PROCESO</span>`;
                            }
                            if(!data.code_estado){
                                return `<span class="badge badge-primary">ENVIADO</span>`;
                            }
                        }

                        //====== AÚN NO ENVIADO A SUNAT =====
                        if(data.send_sunat == 0){
                            //======== ERRORES HTTP,ETC ======
                            if(!data.ticket){
                                return `<span class="badge badge-danger">ERROR AL ENVIAR</span>`;
                            }

                        }

                    }
                },
                { data: 'ticket', title: 'ticket' },
                {
                    //====== LO QUE CONTESTÓ SUNAT, TAL CUAL, SIN INTERPRETARLO ======
                    data: null,
                    render: function(data, type, row) {
                        const partes = [];

                        if(data.cdr_response_code){
                            partes.push('<b>' + data.cdr_response_code + '</b> ' +
                                (data.cdr_response_description || ''));
                        }
                        if(data.status_result_error_code || data.status_result_error_message){
                            partes.push('<span class="text-danger">' +
                                (data.status_result_error_code || '') + ' ' +
                                (data.status_result_error_message || '') + '</span>');
                        }
                        if(data.response_error){
                            partes.push('<span class="text-danger">' + data.response_error + '</span>');
                        }
                        if(data.estado == 'ANULADO'){
                            partes.push('ELIMINADO POR ' + (data.anulado_por_nombre || '-') +
                                ' EL ' + (data.anulado_at || '-'));
                        }

                        return partes.length ? partes.join('<br>') : '-';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        var html = `<td style="white-space: nowrap;"><div style="display: flex; justify-content: center;">`;

                        if (data.ruta_xml) {
                            let urlGetXml       =   "{{ route('ventas.resumenes.getXml', ['resumen_id' => ':resumen_id']) }}";
                            urlGetXml           =   urlGetXml.replace(':resumen_id', data.id);

                            html += `<form action="${urlGetXml}" method="get">`;
                            html += `<button type="submit" class="btn btn-primary btn-xml">XML</button>`;
                            html += `</form>`;
                        }

                        if (data.ruta_cdr) {
                            let urlGetCdr     = "{{ route('ventas.resumenes.getCdr', ['resumen_id' => ':resumen_id']) }}";
                            let url_getCdr    = urlGetCdr.replace(':resumen_id', data.id);


                            html += `<form style="margin-left:3px;" action="${url_getCdr}" method="get">`;
                            html += `<button type="submit" class="btn btn-primary btn-xml">CDR</button>`;
                            html += `</form>`;
                        }

                        html += `</div></td>`;

                        return html;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        var html = '<td><div class="btn-group">';

                            //====== UN RESUMEN YA ELIMINADO NO ADMITE NADA MÁS ======
                            if (data.estado == 'ANULADO') {
                                html += `<i class="fas fa-eye btn btn-success d-flex align-items-center btn-detalle-resumen" data-resumen-id="${data.id}"></i>`;
                                return html + '</div></td>';
                            }

                            if (data.send_sunat == 1) {
                                if (data.code_estado == 98 || (data.ticket && !data.code_estado)) {
                                    html += `<button type="button" data-resumen-id="${data.id}" class="btn btn-primary btn-consultar-resumen">CONSULTAR</button>`;
                                }
                            }

                            if (data.send_sunat == 0 && !data.ticket) {
                                html += `<button type="button" data-resumen-id="${data.id}" class="btn btn-primary btn-reenviar-resumen">REENVIAR</button>`;
                            }

                            html += `<i class="fas fa-eye btn btn-success d-flex align-items-center btn-detalle-resumen" data-resumen-id="${data.id}"></i>`;

                            //====== SÓLO SE ELIMINA LO QUE NUNCA LLEGÓ A SUNAT. LO ACEPTADO
                            //====== YA ESTÁ DECLARADO Y REHACERLO SERÍA DECLARARLO DOS VECES.
                            if (data.send_sunat == 0) {
                                html += `<i class="fas fa-trash-alt btn btn-danger d-flex align-items-center btn-eliminar-resumen" data-resumen-id="${data.id}"></i>`;
                            }

                        html += '</div></td>';

                        return html;
                    }
                }
            ],
            language: {
                processing:     "Cargando resúmenes",
                search:         "BUSCAR: ",
                lengthMenu:    "MOSTRAR _MENU_ RESÚMENES",
                info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ RESÚMENES",
                infoEmpty:      "MOSTRANDO 0 RESÚMENES",
                infoFiltered:   "(FILTRADO de _MAX_ RESÚMENES)",
                infoPostFix:    "",
                zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable:     "NO HAY RESÚMENES DISPONIBLES",
                paginate: {
                    first:      "PRIMERO",
                    previous:   "ANTERIOR",
                    next:       "SIGUIENTE",
                    last:       "ÚLTIMO"
                },
                aria: {
                    sortAscending:  ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            },
            "order": [[ 0, "desc" ]]
        });
    }


     //========= UN RESUMEN POR FECHA, UNO DETRAS DE OTRO ==========
     //========= SECUENCIAL A PROPOSITO: CADA store() TOMA EL CORRELATIVO
     //========= SIGUIENTE, ASI QUE LANZARLOS EN PARALELO LOS HARIA CHOCAR.
     async function saveSendResumen(){

        const url      = '{{route('ventas.resumenes.store')}}';
        const aEmitir  = gruposAEmitir();

        let enviados   = 0;
        let grabados   = 0;
        const fallos   = [];

        for (const grupo of aEmitir) {
            try {
                const res = await axios.post(url,{
                    'comprobantes': JSON.stringify(grupo.comprobantes),
                    'fecha_comprobantes': grupo.fecha,
                    'sede_id': sedeSeleccionada()
                });

                if(res.data.success && res.data.success_sunat){
                    enviados++;
                }else if(res.data.success){
                    grabados++;
                    fallos.push(grupo.fecha + ': grabado, pero no enviado (' + (res.data.error || '') + ')');
                }else{
                    fallos.push(grupo.fecha + ': ' + res.data.message);
                }

            } catch (error) {
                fallos.push(grupo.fecha + ': ' + error.message);
            }
        }

        Swal.close();
        tableResumenes.ajax.reload(null, false);

        if(fallos.length === 0){
            $('#modal_resumenes').modal('hide');
            toastr.success(enviados + ' RESUMEN(ES) REGISTRADOS Y ENVIADOS A SUNAT','OPERACION COMPLETADA');
            return;
        }

        //====== SI ALGO FALLO EL MODAL SE QUEDA ABIERTO Y SE DICE QUE PASO CON CADA FECHA ======
        toastr.error(
            'ENVIADOS: ' + enviados + ' &middot; GRABADOS SIN ENVIAR: ' + grabados + '<br>' + fallos.join('<br>'),
            'REVISE EL RESULTADO POR FECHA',
            {timeOut:0, extendedTimeOut:0, enableHtml:true}
        );

        await buscarPendientes();
     }


    //====== ELIMINAR UN RESUMEN QUE NUNCA LLEGO A SUNAT ======
    async function confirmarEliminarResumen(resumen_id){

        const confirmacion = await Swal.fire({
            title: 'Eliminar este resumen?',
            html: 'Se liberaran sus boletas para volver a resumirlas.<br>' +
                  'Queda registro de quien lo elimino y cuando.',
            icon: 'warning',
            input: 'text',
            inputLabel: 'Motivo (opcional)',
            showCancelButton: true,
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'No, cancelar',
            reverseButtons: true
        });

        if(!confirmacion.isConfirmed){ return; }

        try {
            mostrarAnimacion();

            const url = '{{route('ventas.resumenes.destroy')}}';
            const res = await axios.post(url,{
                'resumen_id': resumen_id,
                'motivo': confirmacion.value || ''
            });

            if(res.data.success){
                tableResumenes.ajax.reload(null, false);
                toastr.success(res.data.message,'OPERACION COMPLETADA');
            }else{
                toastr.error(res.data.message,'NO SE PUDO ELIMINAR',{timeOut:0});
            }

        } catch (error) {
            toastr.error(error.message,'ERROR EN LA PETICION ELIMINAR RESUMEN');
        } finally {
            ocultarAnimacion();
        }
    }

    //====== PINTAR NUEVO RESUMEN =====
    function addNewResumen(resumen){
        tableResumenes.row.add(resumen).draw();
    }

    //==== QUITAR UN COMPROBANTE DE SU GRUPO ======
    //==== EL GRUPO SE QUEDA AUNQUE SE VACÍE, PARA QUE SE VEA QUE ESA FECHA
    //==== EXISTÍA Y SE DECIDIÓ NO DECLARARLA.
    function eliminarComprobante(indice, documento_id){
        if(!gruposPendientes[indice]){ return; }

        gruposPendientes[indice].comprobantes =
            gruposPendientes[indice].comprobantes.filter((c) => c.documento_id != documento_id);
    }

    //============= ABRIR MODAL CLIENTE =============
    async function openModalResumenes(){

        $("#modal_resumenes").modal("show");
        gruposPendientes = [];
        pintarGrupos();
        await  isActive();
    }

    //====== TRAER TODO LO PENDIENTE DE LA SEDE, AGRUPADO POR FECHA ======
    let metaPendientes = {};

    async function buscarPendientes(){
        try {
            mostrarAnimacion();
            toastr.clear();

            const dias  =   document.querySelector('#dias_pendientes').value || 30;
            const url   =   route('ventas.resumenes.getPendientes',{sede_id:sedeSeleccionada(), dias:dias});
            const res   =   await axios.get(url);

            metaPendientes   = res.data;
            gruposPendientes = [];

            //====== SIN SERIE R NO SE PUEDE NUMERAR: SE AVISA Y NO SE CONTINUA ======
            if(!res.data.success){
                pintarGrupos();
                toastr.error(res.data.message,'NO SE PUEDE CONTINUAR',{timeOut:0});
                return;
            }

            gruposPendientes = (res.data.grupos || []).map((g) => {
                g.seleccionado = true;
                return g;
            });

            pintarGrupos();

        } catch (error) {
            toastr.error(error.message,'ERROR EN LA PETICION BUSCAR PENDIENTES');
        } finally {
            ocultarAnimacion();
        }
    }

    //====== LOS GRUPOS QUE REALMENTE SE VAN A EMITIR ======
    function gruposAEmitir(){
        return gruposPendientes.filter((g) => g.seleccionado && g.comprobantes.length > 0);
    }

    function hayAnuladas(){
        return gruposAEmitir().some((g) =>
            g.comprobantes.some((c) => c.documento_estado !== 'ACTIVO'));
    }

    //====== PINTAR UN BLOQUE POR FECHA: DE CADA UNO SALE UN RESUMEN ======
    function pintarGrupos(){
        const plan      = document.querySelector('#plan_resumenes');
        const avisoAnt  = document.querySelector('#aviso_anteriores');
        const avisoAnu  = document.querySelector('#aviso_anuladas');

        plan.style.display     = 'none';
        avisoAnt.style.display = 'none';
        avisoAnu.style.display = 'none';
        contenedorGrupos.innerHTML = '';

        if(gruposPendientes.length === 0){
            contenedorGrupos.innerHTML =
                '<div class="text-center text-muted p-4">NO HAY BOLETAS PENDIENTES EN EL PERIODO CONSULTADO.</div>';
            return;
        }

        const aEmitir   = gruposAEmitir();
        const serieR    = metaPendientes.serie_resumen || '';
        const base      = parseInt(metaPendientes.correlativo_siguiente, 10) || 0;

        //====== SE DICE DE ANTEMANO CUANTOS DOCUMENTOS SE VAN A EMITIR ======
        if(aEmitir.length > 0){
            plan.innerHTML = '<b>SE CREARAN ' + aEmitir.length + ' RESUMEN(ES)</b> PARA LA SERIE ' + serieR +
                ', UNO POR CADA FECHA: SUNAT NO ADMITE VARIAS FECHAS EN UN MISMO RESUMEN.';
            plan.style.display = 'block';
        }

        if(metaPendientes.anteriores > 0){
            avisoAnt.innerHTML = 'HAY <b>' + metaPendientes.anteriores + '</b> BOLETA(S) PENDIENTES ANTERIORES AL ' +
                metaPendientes.desde + '. AMPLIE LOS DIAS HACIA ATRAS PARA INCLUIRLAS.';
            avisoAnt.style.display = 'block';
        }

        if(hayAnuladas()){
            avisoAnu.innerHTML = 'HAY COMPROBANTES <b>ANULADOS</b> EN LA SELECCION. QUITELOS ANTES DE CONTINUAR: ' +
                'DECLARARLOS COMO ALTA ANTE SUNAT SERIA DECLARAR UNA VENTA QUE NO EXISTE.';
            avisoAnu.style.display = 'block';
        }

        let html = '';
        let nEmitido = 0;

        gruposPendientes.forEach((g, i) => {
            const activo = g.seleccionado && g.comprobantes.length > 0;
            if(activo){ nEmitido++; }

            const etiqueta = (activo && base) ? (serieR + '-' + (base + nEmitido - 1)) : '--';
            const total    = g.comprobantes.reduce((a,c) => a + parseFloat(c.documento_total || 0), 0);

            html += '<div class="ibox" style="margin-bottom:12px;">';
            html += '  <div class="ibox-title" style="padding:10px 15px;">';
            html += '    <label style="margin:0;font-weight:600;">';
            html += '      <input type="checkbox" class="chk-grupo" data-indice="' + i + '" ' + (g.seleccionado ? 'checked' : '') + '> ';
            html += '      ' + g.fecha + ' &middot; ' + g.comprobantes.length + ' BOLETA(S) &middot; S/ ' + total.toFixed(2);
            html += '    </label>';
            html += '    <span class="pull-right badge badge-primary">' + etiqueta + '</span>';
            html += '  </div>';
            html += '  <div class="ibox-content" style="padding:0;">';
            html += '    <table class="table table-sm" style="margin:0;">';
            html += '      <thead><tr>';
            html += '        <th>#</th><th>Numero</th><th>Estado</th><th>Moneda</th>';
            html += '        <th>T.gravado</th><th>T.Igv</th><th>T.Total</th><th></th>';
            html += '      </tr></thead><tbody>';

            g.comprobantes.forEach((c) => {
                const anulada = c.documento_estado !== 'ACTIVO';
                html += '<tr class="' + (anulada ? 'bg-danger text-white' : '') + '">';
                html += '  <th scope="row">' + c.documento_id + '</th>';
                html += '  <td>' + c.documento_serie + '-' + c.documento_correlativo + '</td>';
                html += '  <td><span class="badge ' + (anulada ? 'badge-danger' : 'badge-success') + '">' + c.documento_estado + '</span></td>';
                html += '  <td>' + c.documento_moneda + '</td>';
                html += '  <td>' + c.documento_subtotal + '</td>';
                html += '  <td>' + c.documento_igv + '</td>';
                html += '  <td>' + c.documento_total + '</td>';
                html += '  <td><i class="btn btn-danger btn-xs fas fa-trash-alt btn-delete-document"';
                html += '       data-documento-id="' + c.documento_id + '" data-indice="' + i + '"></i></td>';
                html += '</tr>';
            });

            html += '      </tbody></table>';
            html += '  </div>';
            html += '</div>';
        });

        contenedorGrupos.innerHTML = html;
    }

    //====== VERIFICAR SI ESTAN ACTIVOS LOS RESÚMENES =======
    async function isActive(){

        try {
            mostrarAnimacion();
            toastr.clear();

            const url       =   route('ventas.resumenes.getStatus',sedeSeleccionada());
            const response  =   await axios.get(url);

            const resumenActive =   response.data.resumenActive;
            //====== VERIFICANDO SI LOS RESUMENES ESTÁN ACTIVOS =====
            if(response.data.success){
                toastr.info(response.data.message,'OPERACIÓN COMPLETADA',{timeOut:0});
                return true;
            }else{
                toastr.error(response.data.message,'ERROR EN EL SERVIDOR');
                return false;
            }


        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VERIFICAR RESÚMENES EN LA SEDE');
        }finally{
            ocultarAnimacion();
        }
    }

    function validaciones(){
        let validacion  =   true;

        //======= AL MENOS UNA FECHA SELECCIONADA =======
        if(gruposAEmitir().length === 0){
            toastr.error('NO HAY NINGUNA FECHA SELECCIONADA CON COMPROBANTES','ERROR');
            validacion = false;
        }

        //======= NINGUNA ANULADA. ES UNA CORTESIA: LA GARANTIA ESTA EN store(),
        //======= QUE VUELVE A COMPROBARLO CONTRA LA BASE ANTES DE GRABAR.
        if(hayAnuladas()){
            toastr.error('HAY COMPROBANTES ANULADOS EN LA SELECCION. QUITELOS ANTES DE CONTINUAR.','ERROR',{timeOut:0});
            validacion = false;
        }

        if(!isActive()){
            validacion = false;
        }

        return validacion;
    }


    async function verDetalleResumen(resumen_id){
        const rowIndex = tableResumenes.rows().indexes().filter(function(index) {
            return tableResumenes.row(index).data().id == resumen_id;
        });


        if (rowIndex.length > 0) {
            const fila = tableResumenes.row(rowIndex[0]).data();
            document.querySelector('#resumen-title').textContent  =   `${fila.serie}-${fila.correlativo}`;

            await getDetallesResumen(resumen_id);

            $("#modal_resumen_detalle").modal("show");
        } else {
            toastr.error('NO SE ENCONTRÓ LA FILA CON EL RESUMEN','RECARGAR LA PÁGINA');
        }

    }



</script>
@endpush
