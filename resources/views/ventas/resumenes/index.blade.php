@extends('layout') @section('content')
@section('ventas-active', 'active')
@section('resumenes-active', 'active')

@include('ventas.resumenes.modal_add')
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
                        style="text-transform:uppercase" id="table-resumenes">
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
                                @foreach ($resumenes as $resumen)
                                    <tr>
                                        <th scope="row">{{$resumen->id}}</th>
                                        <td>{{$resumen->created_at}}</td>
                                        <td>{{$resumen->fecha_comprobantes}}</td>
                                        <td>{{$resumen->serie.'-'.$resumen->correlativo}}</td>

                                        @if ($resumen->send_sunat == 1)
                                            @if ($resumen->code_estado == '0')
                                                <td>
                                                    <p class="mb-0" style="padding:2px;border-radius: 10px; background-color: #e5f9e0; color: #176e2f; font-weight: bold;text-align:center;">
                                                        ACEPTADO
                                                    </p>
                                                </td>  
                                            @endif
                                            @if ($resumen->code_estado == '99')
                                                <td>
                                                    <p class="mb-0" style="padding:2px;border-radius: 10px; background-color: #efd5d5; color: #be1919; font-weight: bold;text-align:center;">
                                                        Enviado con errores
                                                    </p>
                                                </td>  
                                            @endif
                                            @if ($resumen->code_estado == '98')
                                                <td>
                                                    <p class="mb-0" style="padding:2px;border-radius: 10px; background-color: #c0d5f5; color: #033bd6; font-weight: bold;text-align:center;">
                                                        EN PROCESO
                                                    </p>
                                                </td>  
                                            @endif
                                            @if ($resumen->code_estado == null)
                                                <td>
                                                    <p class="mb-0" style="padding:2px;border-radius: 10px; background-color: #c0d5f5; color: #033bd6; font-weight: bold;text-align:center;">
                                                        ERROR EN EL ENVÍO
                                                    </p>
                                                </td>  
                                            @endif
                                        @endif
                                        @if ($resumen->send_sunat == 0)
                                            <td>
                                                <p class="mb-0" style="padding:2px;border-radius: 10px; background-color: #f8f9e0; color: #ad8a14; font-weight: bold;text-align:center;">
                                                    ERROR EN EL ENVÍO
                                                </p>
                                            </td>
                                        @endif

                                        <td>{{$resumen->ticket}}</td>
                                        <td style="white-space: nowrap;">
                                            <div style="display: flex; justify-content: center;">
                                                @if ($resumen->ruta_xml)
                                                    <form action="{{ route('ventas.resumenes.getXml', $resumen->id) }}" method="get">
                                                        <button type="submit" class="btn btn-primary btn-xml">
                                                            XML
                                                        </button>
                                                    </form>
                                                @endif
                                                @if ($resumen->ruta_cdr)
                                                    <form style="margin-left:3px;" action="{{ route('ventas.resumenes.getCdr', $resumen->id) }}" method="get">
                                                        <button type="submit" class="btn btn-primary btn-xml">
                                                            CDR
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if ($resumen->code_estado == '98' || $resumen->regularize == 1 ||$resumen->code_estado == null)
                                                <button type="button" data-resumen-id="{{$resumen->id}}" class="btn btn-primary btn-consultar-resumen">
                                                    CONSULTAR
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
    <!-- DataTable -->
    <link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/dist/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">
@endpush


@push('scripts')
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>

<script>
    const btnGetComprobantes            =   document.querySelector('#btn-get-comprobantes');
    const bodyTableSearchComprobantes   =   document.querySelector('.table-search-comprobantes tbody');
    let tableResumenes  = null;

    let fecha_comprobantes              =   null;
    let listComprobantes                =   [];   

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        cargarDataTable()
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

                reenviarResumen(resumen_id);
            }
        })

        //======= GUARDAR RESUMEN ======
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('btn-guardar-resumen')){

               const valido   = validaciones();
               if(valido){
                saveSendResumen();
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
    async function reenviarResumen(){
        document.querySelector('.loader-container').style.display = 'flex'; 
        try {
            const url       =   `/ventas/resumenes/consultar`;
            const response  =   await axios.post(url,{
                'resumen_id': JSON.stringify(resumen_id)
            });

            console.log(response);
            if(response.status == 200){
                if(response.data.type == 'error'){
                    toastr.success(response.data.message,'ERROR EN LA CONSULTA');
                    return;
                }
                if(response.data.type == 'success'){
                    actualizarDataTable(resumen_id,response.data);
                    toastr.success(response.data.message,'CONSULTA COMPLETADA');
                }
                
            }
          
        } catch (error) {
            console.error('Error al consultar el estado del ticket:', error);
            toastr.error(error,'CONSULTA INCORRECTA');

        }finally{
            document.querySelector('.loader-container').style.display = 'none'; 
        }
    }

    //======== CONSULTAR RESUMEN ======
    async function consultarResumen(resumen_id){
        document.querySelector('.loader-container').style.display = 'flex'; 
        try {
            const url       =   `/ventas/resumenes/consultar`;
            const response  =   await axios.post(url,{
                'resumen_id': JSON.stringify(resumen_id)
            });

            console.log(response);
            if(response.status == 200){
                if(response.data.type == 'error'){
                    toastr.success(response.data.message,'ERROR EN LA CONSULTA');
                    return;
                }
                if(response.data.type == 'success'){
                    actualizarDataTable(resumen_id,response.data);
                    toastr.success(response.data.message,'CONSULTA COMPLETADA');
                }
                
            }
          
        } catch (error) {
            console.error('Error al consultar el estado del ticket:', error);
            toastr.error(error,'CONSULTA INCORRECTA');

        }finally{
            document.querySelector('.loader-container').style.display = 'none'; 
        }
    }

    //========== ACTUALIZAR DATATABLE ==============
    function actualizarDataTable(resumen_id,res_consulta) {
        //===== OBTENIENDO EL NRO DE FILA DE ACUERDO AL RESUMEN_ID =====
        const indiceFila    =   tableResumenes.row((idx,data) => data[0] == resumen_id).index();
        const fila          =   tableResumenes.row((idx,data) => data[0] == resumen_id);

        //====== ACTUALIZANDO DATA DE LA FILA ========
        //==== ESTADO - COLUMNA 4, DEL 0 AL 7 ====
        let resumen_estado  =   ``;
        
        if(res_consulta.code_estado == 0){
            resumen_estado  =   `<p class="mb-0" style="padding:2px;border-radius: 10px; 
                                background-color: #e5f9e0; color: #176e2f; font-weight: bold;text-align:center;">
                                ACEPTADO</p>`;
        }

         if(res_consulta.code_estado == 98){
            resumen_estado  =   ` <p class="mb-0" style="padding:2px;border-radius: 10px; 
                                background-color: #c0d5f5; color: #033bd6; font-weight: bold;text-align:center;">
                                EN PROCESO</p>`;
        }

        if(res_consulta.code_estado == 99){
            resumen_estado  =   `<p class="mb-0" style="padding:2px;border-radius: 10px; 
                                background-color: #efd5d5; color: #be1919; font-weight: bold;text-align:center;">
                                Enviado con errores</p>`;
        }

        fila.cell(indiceFila,4).data(resumen_estado).draw();

        //======= ACTUALIZANDO COLUMNA DEL CDR ==========
        let descargarArchivos   =   ``;

        
        descargarArchivos = `<div style="display: flex; justify-content: center;">`;

        descargarArchivos +=    `<form action="{{ route('ventas.resumenes.getXml', ':resumenId') }}" 
                                method="get">
                                    <button type="submit" class="btn btn-primary btn-xml">
                                            XML
                                    </button>
                                </form>`.replace(':resumenId', resumen_id);  

        if(res_consulta.cdr){
            descargarArchivos +=    `<form style="margin-left:3px;" action="{{ route('ventas.resumenes.getCdr', ':resumenId') }}" 
                                    method="get">
                                        <button type="submit" class="btn btn-primary btn-xml">
                                            CDR
                                        </button>
                                    </form>`.replace(':resumenId', resumen_id);                          
        }
        
        descargarArchivos+=`</div>`;  

        fila.cell(indiceFila,6).data(descargarArchivos).draw();

        //====== ACTUALIZANDO COLUMNA DE LAS ACCIONES ======
        let acciones    =   ``;
        if (res_consulta.code_estado == 98) {
            acciones+=`<button type="button" data-resumen-id="${resumen.id}" 
                class="btn btn-primary btn-consultar-resumen">
                CONSULTAR</button>`;
        }
        if (res_consulta.code_estado == 0) {
            acciones+=``;
        }

        fila.cell(indiceFila,7).data(acciones).draw();
    }

  

    function cargarDataTable(){
        tableResumenes = new DataTable('#table-resumenes',
        {
            language: {
                processing:     "Traitement en cours...",
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
            }
        });
    }

    //========= GUARDAR RESUMEN Y ENVIAR A SUNAT A LA VEZ ==========
    async function saveSendResumen(){
        document.querySelector('.loader-container').style.display = 'flex'; 
        try {
            const url       =   `/ventas/resumenes/store`;
            const response  =   await axios.post(url,{
                'comprobantes': JSON.stringify(listComprobantes),
                'fecha_comprobantes': JSON.stringify(fecha_comprobantes)
            });

            console.log(response);
            $("#modal_resumenes").modal("hide");
            addNewResumen(response.data.nuevo_resumen)
            toastr.info('RESUMEN REGISTRADO','OPERACIÓN EXITOSA');
        } catch (error) {
            console.error('Error al enviar y guardar resumen:', error);
        }finally{
            document.querySelector('.loader-container').style.display = 'none'; 
        }
    }

    //====== PINTAR NUEVO RESUMEN =====
    function addNewResumen(resumen){
        //====== CONSTRUYENDO HTML DE ESTADO =======
        let resumen_estado  =   ``;
        if(resumen.send_sunat == 1){

            if(resumen.code_estado == 0){
                resumen_estado  =   `<p class="mb-0" style="padding:2px;border-radius: 10px; 
                                    background-color: #e5f9e0; color: #176e2f; font-weight: bold;text-align:center;">
                                    ACEPTADO</p>`;
            }

            if(resumen.code_estado == 98){
                resumen_estado  =   ` <p class="mb-0" style="padding:2px;border-radius: 10px; 
                                    background-color: #c0d5f5; color: #033bd6; font-weight: bold;text-align:center;">
                                    EN PROCESO</p>`;
            }

            if(resumen.code_estado == 99){
                resumen_estado  =   `<p class="mb-0" style="padding:2px;border-radius: 10px; 
                                    background-color: #efd5d5; color: #be1919; font-weight: bold;text-align:center;">
                                    Enviado con errores</p>`;
            }

        }
        if(resumen.send_sunat == 0){
            resumen_estado  =   ` <p class="mb-0" style="padding:2px;border-radius: 10px; 
                                background-color: #f8f9e0; color: #ad8a14; font-weight: bold;text-align:center;">
                                ERROR EN EL ENVÍO</p>`;
        }

        let descargarArchivos   =   ``;

        if(resumen.ruta_xml || resumen.ruta_cdr){
            descargarArchivos = `<div style="display: flex; justify-content: center;">`;
                if(resumen.ruta_xml){
                    descargarArchivos +=    `<form action="{{ route('ventas.resumenes.getXml', ':resumenId') }}" 
                                            method="get">
                                                <button type="submit" class="btn btn-primary btn-xml">
                                                    XML
                                                </button>
                                            </form>`.replace(':resumenId', resumen.id);                       
                }
                if(resumen.ruta_cdr){
                    descargarArchivos +=    `<form style="margin-left:3px;" action="{{ route('ventas.resumenes.getCdr', ':resumenId') }}" 
                                            method="get">
                                                <button type="submit" class="btn btn-primary btn-xml">
                                                    CDR
                                                </button>
                                            </form>`.replace(':resumenId', resumen.id);                       
                }
                descargarArchivos+=`</div>`;   
        }

        let acciones    =   ``;
        if (resumen.code_estado == 98) {
            acciones+=`<button type="button" data-resumen-id="${resumen.id}" 
                class="btn btn-primary btn-consultar-resumen">
                CONSULTAR</button>`;
        }

        //========= OBTENIENDO TICKET ======
        let ticket  =   '';
        if('ticket' in resumen){
            ticket  =   resumen.ticket;
        }
       

        tableResumenes.row
        .add([resumen.id,
            resumen.created_at, 
            resumen.fecha_comprobantes,
            `${resumen.serie}-${resumen.correlativo}`,
            resumen_estado,
            ticket,
            descargarArchivos,
            acciones,
        ]).draw()
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
            const url       =   `/ventas/resumenes/getComprobantes/${fechaComprobantes}`;
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
            const url       =   `/ventas/resumenes/getStatus`;
            const response  =   await axios.get(url);
            
            if(response.status  === 200){
                const resumenActive =   response.data.resumenActive;
                //====== VERIFICANDO SI LOS RESUMENES ESTÁN ACTIVOS =====
                if(!resumenActive){
                    toastr.error('DEBE ACTIVAR LOS RESÚMENES','RESUMEN INACTIVO');
                    return false;
                }
                if(resumenActive){
                    toastr.success('RESÚMENES REGISTRADOS EN LA EMPRESA','RESUMEN ACTIVO');
                    return true;
                }
            }else{
                toastr.error(response.statusText,'ERROR EN LA SOLICITUD');
            }

        } catch (error) {
            console.error('Error al verificar estado: ', error);
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


</script>
@endpush
