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

                        @include('mantenimiento.sedes.tables.tbl_lst_sedes')

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
    let tblLstSedes  = null;

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
        const getSedes = "{{ route('mantenimiento.sedes.getSedes') }}";

        tblLstSedes = new DataTable('#tbl_lst_sedes',
        {
            serverSide: true,
            ajax: {
                url: getSedes,
                type: 'GET' 
            },
            columns: [
                { data: 'id'},
                { data: 'nombre' },
                { data: 'direccion' },
                { data: 'ubigeo' },
                { data: 'codigo_local'},
                { data: 'tipo_sede'},
                { 
                    data: null, 
                    render: function(data, type, row) {
                        
                        let acciones            =   ``;
                        const ruta_numeracion   =   `{{ route('mantenimiento.sedes.numeracionCreate', ':sede_id') }}`.replace(':sede_id', row.id);
                        const ruta_editar       =   `{{ route('mantenimiento.sedes.edit', ':id') }}`.replace(':id', row.id);

                        if(data.tipo_sede == 'SECUNDARIA'){
                            acciones  =   `
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-th-large"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="${ruta_editar}">Editar</a>
                                                    <a class="dropdown-item" href="${ruta_numeracion}">Numeración</a>
                                                </div>
                                            </div>
                                            `;
                        }
                        
                        return acciones;
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
