@extends('layout') 

@section('content')

@section('mantenimiento-active', 'active')
@section('sedes-active', 'active')


<div class="row wrapper border-bottom white-bg page-heading">
    @csrf
    <div class="col-lg-10 col-md-10">
        <h2  style="text-transform:uppercase">
            <b>Listado de Sedes</b>
        </h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Sedes</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button type="button" class="btn btn-block btn-w-m btn-primary m-t-md" onclick="goToSedeCreate()">
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
       
    }

    function goToSedeCreate(){
        window.location.href = route('mantenimiento.sedes.create');
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
                            if(data.code_estado == '0'){
                                return `<span class="badge badge-success">ACEPTADO</span>`;
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

  
</script>
@endpush
