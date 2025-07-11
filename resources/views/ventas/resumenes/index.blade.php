@extends('layout') @section('content')
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
                    <div class="table-responsive">

                        <table class="table dataTables-gui table-striped table-bordered table-hover"
                        style="text-transform:uppercase" id="table-resumenes" width="100%">
                            <thead>

                                <tr>
                                    <th class="text-center">NRO</th>
                                    <th class="text-center">FEC.EMISIÓN</th>
                                    <th class="text-center">FEC. REFERENCIA</th>
                                    <th class="text-center">IDENTIFICADOR</th>

                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">TICKET</th>
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
    <!-- DataTable -->
    <link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/dist/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">
@endpush


@push('scripts')
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    const btnGetComprobantes            =   document.querySelector('#btn-get-comprobantes');
    const bodyTableSearchComprobantes   =   document.querySelector('.table-search-comprobantes tbody');
    let tableResumenes  = null;

    let fecha_comprobantes              =   null;
    let listComprobantes                =   [];

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
                eliminarComprobante(documento_id);
                pintarTableComprobantes();
            }
        })

        //====== OBTENER COMPROBANTES BOLETAS ======
        btnGetComprobantes.addEventListener('click',()=>{
            //===== OBTENIENDO FECHA DEL INPUT DATE =====
            const fechaComprobantes  =   document.querySelector('#fecha_comprobante').value;
            fecha_comprobantes       =   fechaComprobantes;

            if(fechaComprobantes){
                getComprobantes(fechaComprobantes);
            }else{
                toastr.error('DEBE SELECCIONAR UNA FECHA');
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
            serverSide: true,
            ajax: {
                url: getResumenesUrl,
                type: 'GET'
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
                {
                    data: null,
                    render: function(data, type, row) {

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

                            if (data.send_sunat == 1) {
                                if (data.code_estado == 98 || (data.ticket && !data.code_estado)) {
                                    html += `<button type="button" data-resumen-id="${data.id}" class="btn btn-primary btn-consultar-resumen">CONSULTAR</button>`;
                                }
                            }

                            if (data.send_sunat == 0 && !data.ticket) {
                                html += `<button type="button" data-resumen-id="${data.id}" class="btn btn-primary btn-reenviar-resumen">REENVIAR</button>`;
                            }

                            html += `<i class="fas fa-eye btn btn-success d-flex align-items-center btn-detalle-resumen" data-resumen-id="${data.id}"></i>`;

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
                loadingRecords: "CARGA EN CURSO",
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


     //========= GUARDAR RESUMEN Y ENVIAR A SUNAT A LA VEZ ==========
     async function saveSendResumen(){

         try {

            const url   =   '{{route('ventas.resumenes.store')}}';
            const res   =   await axios.post(url,{
                                'comprobantes': JSON.stringify(listComprobantes),
                                'fecha_comprobantes': fecha_comprobantes,
                                'sede_id':@json($sede_id)
                             });

             if(res.data.success){
                 tableResumenes.ajax.reload(null, false);
                 $('#modal_resumenes').modal('hide');
                 toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
             }else{
                 toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
             }

         } catch (error) {
             toastr.error(error.message, 'ERROR EN LA PETICIÓN GUARDAR Y ENVIAR RESÚMEN', { timeOut: 0 });

         }finally{
            Swal.close();
            $("#modal_resumenes").modal("hide");
         }
     }


    //====== PINTAR NUEVO RESUMEN =====
    function addNewResumen(resumen){
        tableResumenes.row.add(resumen).draw();
    }

    //==== ELIMINAR COMPROBANTE DEL CARRITO ======
    function eliminarComprobante(documento_id){
        listComprobantes    =   listComprobantes.filter((c)=>{
            return  c.documento_id != documento_id;
        })
    }

    //============= ABRIR MODAL CLIENTE =============
    async function openModalResumenes(){

        $("#modal_resumenes").modal("show");
        clearTableComprobantes();
        await  isActive();
    }

    //===== OBTENER COMPROBANTES =====
    async function getComprobantes(fechaComprobantes){
        try {
            const url       =   route('ventas.resumenes.getComprobantes',{fechaComprobantes,sede_id:@json($sede_id)})
            const response  =   await axios.get(url);
            console.log(response);
            listComprobantes    =   response.data.success;
            pintarTableComprobantes();
        } catch (error) {
            console.error('Error al obtener comprobantes: ', error);
        }
    }

    //====== VERIFICAR SI ESTAN ACTIVOS LOS RESÚMENES =======
    async function isActive(){

        try {
            mostrarAnimacion();
            toastr.clear();

            const url       =   route('ventas.resumenes.getStatus',@json($sede_id));
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

    //====== PINTAR TABLE COMPROBANTES =====
    function pintarTableComprobantes(){
        clearTableComprobantes();

        let tbody = `<tr>`;
        listComprobantes.forEach((c)=>{
            tbody   +=  `
                                <th scope="row">${c.documento_id}</th>
                                <td>${c.documento_serie}-${c.documento_correlativo}</td>
                                <td>${c.documento_moneda}</td>
                                <td>${c.documento_subtotal}</td>
                                <td>${c.documento_igv}</td>
                                <td>${c.documento_total}</td>
                                <td>
                                    <i class="btn btn-danger fas fa-trash-alt btn-delete-document" data-documento-id=${c.documento_id}></i>
                                </td>
                            </tr>
                        `;
        })

        bodyTableSearchComprobantes.innerHTML   =   tbody;
    }

    //======== LIMPIAR TABLE COMPROBANTES =========
    function clearTableComprobantes(){
        while (bodyTableSearchComprobantes.firstChild) {
            bodyTableSearchComprobantes.removeChild(bodyTableSearchComprobantes.firstChild)
        }
    }

    function validaciones(){
        let validacion  =   true;
        //======= VALIDAR DETALLE DEL RESUMEN =======
        if(listComprobantes.length==0){
            toastr.error('NO HAY COMPROBANTES EN EL RESUMEN','ERROR');
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
